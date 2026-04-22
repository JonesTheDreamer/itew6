# Organization Management Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build admin-facing organization management: list, create, toggle active/inactive, manage student and faculty members, and view organization details.

**Architecture:** Backend CRUD already exists — three targeted repository changes expose college and member-type data. Frontend follows the existing Faculty pattern: list page → add page → detail page with tabs.

**Tech Stack:** Laravel 13 (PHP, Eloquent), React 19, TypeScript, TanStack Query, Tailwind CSS, shadcn UI, React Router v7, Axios

---

## File Map

**Create:**
- `client/src/api/colleges.ts` — fetch colleges list
- `client/src/api/organizations.ts` — org + membership API functions
- `client/src/pages/organizations/OrganizationsPage.tsx` — list + filter + toggle + delete
- `client/src/pages/organizations/AddOrganizationPage.tsx` — create form
- `client/src/pages/organizations/OrganizationDetailPage.tsx` — overview + student members + faculty members tabs

**Modify:**
- `server/app/Repositories/OrganizationRepository.php` — eager-load college on getAll/getById
- `server/app/Repositories/UserOrganizationRepository.php` — eager-load user.student.program and user.faculty
- `client/src/types/index.ts` — add OrgMember type
- `client/src/components/layout/AppSidebar.tsx` — add Organizations nav group
- `client/src/main.tsx` — add organization routes

---

## Task 1: Backend — OrganizationRepository college eager-load

**Files:**
- Modify: `server/app/Repositories/OrganizationRepository.php`

- [ ] **Step 1: Replace the file with college-aware overrides**

Replace the entire file with:

```php
<?php

namespace App\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrganizationRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Organization()); }

    public function getAll(): Collection
    {
        return Organization::with('college')->get();
    }

    public function getById(int $id): ?Model
    {
        return Organization::with('college')->find($id);
    }

    public function getStats(): array
    {
        return [
            'totalOrganizations'  => Organization::count(),
            'activeOrganizations' => Organization::where('isActive', true)->count(),
        ];
    }
}
```

- [ ] **Step 2: Verify with curl (server must be running on port 8000)**

```bash
curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' | grep token
```

Copy the token, then:

```bash
curl -s http://localhost:8000/api/organizations \
  -H "Authorization: Bearer <TOKEN>" | python -m json.tool
```

Expected: each organization object contains a `college` key (either `null` or `{ "id": ..., "name": "..." }`).

- [ ] **Step 3: Commit**

```bash
cd server
git add app/Repositories/OrganizationRepository.php
git commit -m "feat: eager-load college on organization queries"
```

---

## Task 2: Backend — UserOrganizationRepository member-type eager-load

**Files:**
- Modify: `server/app/Repositories/UserOrganizationRepository.php`

- [ ] **Step 1: Update getByOrganization to load user.student.program and user.faculty**

Replace the `getByOrganization` method body:

```php
public function getByOrganization(int $organizationId): Collection
{
    return UserOrganization::where('organizationId', $organizationId)
        ->with('user.student.program', 'user.faculty')
        ->get();
}
```

The full file becomes:

```php
<?php

namespace App\Repositories;

use App\Models\UserOrganization;
use Illuminate\Database\Eloquent\Collection;

class UserOrganizationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new UserOrganization());
    }

    public function getByUser(int $userId): Collection
    {
        return UserOrganization::where('userId', $userId)->with('organization')->get();
    }

    public function getByOrganization(int $organizationId): Collection
    {
        return UserOrganization::where('organizationId', $organizationId)
            ->with('user.student.program', 'user.faculty')
            ->get();
    }
}
```

- [ ] **Step 2: Verify — fetch members for an org that has a student member**

```bash
curl -s "http://localhost:8000/api/user-organizations?organizationId=1" \
  -H "Authorization: Bearer <TOKEN>" | python -m json.tool
```

Expected: each item has a `user` object containing `student` (with `program`) and/or `faculty` sub-objects.

- [ ] **Step 3: Commit**

```bash
git add app/Repositories/UserOrganizationRepository.php
git commit -m "feat: eager-load user student and faculty on org member queries"
```

---

## Task 3: Frontend — Add OrgMember type

**Files:**
- Modify: `client/src/types/index.ts`

- [ ] **Step 1: Append OrgMember type at the end of the file**

Add after the `College` type (after line 250):

```typescript
export type OrgMember = {
  id: number;
  userId: number;
  organizationId: number;
  role: string | null;
  dateJoined: string | null;
  dateLeft: string | null;
  user?: {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    student?: {
      id: number;
      studentId: string;
      program?: { name: string; code: string };
    };
    faculty?: {
      id: number;
      position?: string;
      department?: string;
    };
  };
};
```

- [ ] **Step 2: Type-check**

```bash
cd client
npx tsc --noEmit
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add src/types/index.ts
git commit -m "feat: add OrgMember type for organization membership"
```

---

## Task 4: Frontend — API services

**Files:**
- Create: `client/src/api/colleges.ts`
- Create: `client/src/api/organizations.ts`

- [ ] **Step 1: Create colleges API**

Create `client/src/api/colleges.ts`:

```typescript
import api from '@/lib/axios';
import type { ApiList, College } from '@/types';

export const getColleges = async (): Promise<College[]> => {
  const { data } = await api.get<ApiList<College>>('/colleges');
  return data.data;
};
```

- [ ] **Step 2: Create organizations API**

Create `client/src/api/organizations.ts`:

```typescript
import api from '@/lib/axios';
import type { ApiItem, ApiList, Organization, OrgMember } from '@/types';

export const getOrganizations = async (): Promise<Organization[]> => {
  const { data } = await api.get<ApiList<Organization>>('/organizations');
  return data.data;
};

export const getOrganization = async (id: number): Promise<Organization> => {
  const { data } = await api.get<ApiItem<Organization>>(`/organizations/${id}`);
  return data.data;
};

export const createOrganization = async (payload: {
  organizationName: string;
  organizationDescription?: string | null;
  dateCreated?: string | null;
  collegeId?: number | null;
  isActive?: boolean;
}): Promise<Organization> => {
  const { data } = await api.post<ApiItem<Organization>>('/organizations', payload);
  return data.data;
};

export const updateOrganization = async (
  id: number,
  payload: Partial<Organization>,
): Promise<Organization> => {
  const { data } = await api.put<ApiItem<Organization>>(`/organizations/${id}`, payload);
  return data.data;
};

export const deleteOrganization = async (id: number): Promise<void> => {
  await api.delete(`/organizations/${id}`);
};

export const getOrgMembers = async (organizationId: number): Promise<OrgMember[]> => {
  const { data } = await api.get<ApiList<OrgMember>>('/user-organizations', {
    params: { organizationId },
  });
  return data.data;
};

export const addOrgMember = async (payload: {
  userId: number;
  organizationId: number;
  role?: string | null;
  dateJoined?: string | null;
}): Promise<OrgMember> => {
  const { data } = await api.post<ApiItem<OrgMember>>('/user-organizations', payload);
  return data.data;
};

export const removeOrgMember = async (id: number): Promise<OrgMember> => {
  const today = new Date().toISOString().split('T')[0];
  const { data } = await api.put<ApiItem<OrgMember>>(`/user-organizations/${id}`, {
    dateLeft: today,
  });
  return data.data;
};
```

- [ ] **Step 3: Type-check**

```bash
cd client
npx tsc --noEmit
```

Expected: no errors.

- [ ] **Step 4: Commit**

```bash
git add src/api/colleges.ts src/api/organizations.ts
git commit -m "feat: add colleges and organizations API service functions"
```

---

## Task 5: Frontend — Sidebar navigation and routes

**Files:**
- Modify: `client/src/components/layout/AppSidebar.tsx`
- Modify: `client/src/main.tsx`

- [ ] **Step 1: Add Organizations section to sidebar**

In `client/src/components/layout/AppSidebar.tsx`, add the `Building2` icon import and an `orgLinks` array, then a new `SidebarGroup` for Organizations.

Replace the entire file with:

```tsx
import { NavLink, useNavigate } from 'react-router';
import { Users, GraduationCap, Search, LogOut, BookOpen, Layers, Building2, Plus } from 'lucide-react';
import {
  Sidebar, SidebarContent, SidebarFooter, SidebarGroup,
  SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarHeader,
} from '@/components/ui/sidebar';
import { logout } from '@/api/auth';

const studentLinks = [
  { to: '/admin/students', label: 'Student List', icon: Users },
  { to: '/admin/students/add', label: 'Add Student', icon: GraduationCap },
];

const facultyLinks = [
  { to: '/admin/faculty', label: 'Faculty List', icon: BookOpen },
  { to: '/admin/faculty/add', label: 'Add Faculty', icon: Users },
];

const orgLinks = [
  { to: '/admin/organizations', label: 'Organization List', icon: Building2 },
  { to: '/admin/organizations/add', label: 'Add Organization', icon: Plus },
];

export default function AppSidebar() {
  const navigate = useNavigate();

  const handleLogout = async () => {
    try { await logout(); } catch {}
    navigate('/login');
  };

  return (
    <Sidebar>
      <SidebarHeader className="px-4 py-4">
        <div className="flex items-center gap-2">
          <GraduationCap className="h-6 w-6 text-primary" />
          <span className="font-semibold text-sm leading-tight">CCS Profiling<br />System</span>
        </div>
      </SidebarHeader>
      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>Students</SidebarGroupLabel>
          <SidebarMenu>
            {studentLinks.map(({ to, label, icon: Icon }) => (
              <SidebarMenuItem key={to}>
                <SidebarMenuButton asChild>
                  <NavLink to={to} className={({ isActive }) => isActive ? 'text-primary font-medium' : ''}>
                    <Icon className="h-4 w-4" />
                    <span>{label}</span>
                  </NavLink>
                </SidebarMenuButton>
              </SidebarMenuItem>
            ))}
          </SidebarMenu>
        </SidebarGroup>
        <SidebarGroup>
          <SidebarGroupLabel>Faculty</SidebarGroupLabel>
          <SidebarMenu>
            {facultyLinks.map(({ to, label, icon: Icon }) => (
              <SidebarMenuItem key={to}>
                <SidebarMenuButton asChild>
                  <NavLink to={to} className={({ isActive }) => isActive ? 'text-primary font-medium' : ''}>
                    <Icon className="h-4 w-4" />
                    <span>{label}</span>
                  </NavLink>
                </SidebarMenuButton>
              </SidebarMenuItem>
            ))}
          </SidebarMenu>
        </SidebarGroup>
        <SidebarGroup>
          <SidebarGroupLabel>Organizations</SidebarGroupLabel>
          <SidebarMenu>
            {orgLinks.map(({ to, label, icon: Icon }) => (
              <SidebarMenuItem key={to}>
                <SidebarMenuButton asChild>
                  <NavLink to={to} className={({ isActive }) => isActive ? 'text-primary font-medium' : ''}>
                    <Icon className="h-4 w-4" />
                    <span>{label}</span>
                  </NavLink>
                </SidebarMenuButton>
              </SidebarMenuItem>
            ))}
          </SidebarMenu>
        </SidebarGroup>
        <SidebarGroup>
          <SidebarGroupLabel>Reports</SidebarGroupLabel>
          <SidebarMenu>
            <SidebarMenuItem>
              <SidebarMenuButton asChild>
                <NavLink to="/admin/skills" className={({ isActive }) => isActive ? 'text-primary font-medium' : ''}>
                  <Layers className="h-4 w-4" />
                  <span>Skills</span>
                </NavLink>
              </SidebarMenuButton>
            </SidebarMenuItem>
            <SidebarMenuItem>
              <SidebarMenuButton asChild>
                <NavLink to="/admin/query" className={({ isActive }) => isActive ? 'text-primary font-medium' : ''}>
                  <Search className="h-4 w-4" />
                  <span>Skill Query</span>
                </NavLink>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroup>
      </SidebarContent>
      <SidebarFooter>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton onClick={handleLogout} className="text-destructive hover:text-destructive">
              <LogOut className="h-4 w-4" />
              <span>Logout</span>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarFooter>
    </Sidebar>
  );
}
```

- [ ] **Step 2: Add organization routes to main.tsx**

In `client/src/main.tsx`, add the three imports after the existing faculty imports:

```tsx
import OrganizationsPage from '@/pages/organizations/OrganizationsPage';
import AddOrganizationPage from '@/pages/organizations/AddOrganizationPage';
import OrganizationDetailPage from '@/pages/organizations/OrganizationDetailPage';
```

Then add these three routes inside the `/admin` children array, after the faculty routes:

```tsx
{ path: 'organizations', element: <OrganizationsPage /> },
{ path: 'organizations/add', element: <AddOrganizationPage /> },
{ path: 'organizations/:id', element: <OrganizationDetailPage /> },
```

- [ ] **Step 3: Type-check**

```bash
cd client
npx tsc --noEmit
```

Expected: errors about missing page files — that's fine, they'll be created in the next tasks. If there are other unexpected errors, fix them before continuing.

- [ ] **Step 4: Commit**

```bash
git add src/components/layout/AppSidebar.tsx src/main.tsx
git commit -m "feat: add Organizations sidebar nav and routes"
```

---

## Task 6: Frontend — OrganizationsPage (list)

**Files:**
- Create: `client/src/pages/organizations/OrganizationsPage.tsx`

- [ ] **Step 1: Create the organizations list page**

Create `client/src/pages/organizations/OrganizationsPage.tsx`:

```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Eye, Trash2, PowerOff, Power, Search } from 'lucide-react';
import { getOrganizations, deleteOrganization, updateOrganization } from '@/api/organizations';
import type { Organization } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import StatusBadge from '@/components/shared/StatusBadge';

export default function OrganizationsPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [deleteTarget, setDeleteTarget] = useState<Organization | null>(null);
  const [toggleTarget, setToggleTarget] = useState<Organization | null>(null);

  const { data: organizations = [], isLoading } = useQuery({
    queryKey: ['organizations'],
    queryFn: getOrganizations,
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteOrganization(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['organizations'] });
      setDeleteTarget(null);
    },
  });

  const toggleMutation = useMutation({
    mutationFn: ({ id, isActive }: { id: number; isActive: boolean }) =>
      updateOrganization(id, { isActive }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['organizations'] });
      setToggleTarget(null);
    },
  });

  const filtered = organizations.filter((o) => {
    const matchName = o.organizationName.toLowerCase().includes(search.toLowerCase());
    const matchStatus =
      statusFilter === 'all' ||
      (statusFilter === 'active' && o.isActive) ||
      (statusFilter === 'inactive' && !o.isActive);
    return matchName && matchStatus;
  });

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Organizations</h1>
        <Button onClick={() => navigate('/admin/organizations/add')}>
          <Plus className="h-4 w-4 mr-2" /> Add Organization
        </Button>
      </div>

      <Card>
        <CardHeader className="pb-3">
          <CardTitle className="text-base font-medium">Filter</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex gap-3 flex-wrap">
            <div className="relative flex-1 min-w-48">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                className="pl-8"
                placeholder="Search name…"
                value={search}
                onChange={e => setSearch(e.target.value)}
              />
            </div>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-44">
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Statuses</SelectItem>
                <SelectItem value="active">Active</SelectItem>
                <SelectItem value="inactive">Inactive</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>College</TableHead>
                <TableHead>Date Created</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {isLoading && (
                <TableRow>
                  <TableCell colSpan={5} className="text-center py-8 text-muted-foreground">Loading…</TableCell>
                </TableRow>
              )}
              {!isLoading && filtered.length === 0 && (
                <TableRow>
                  <TableCell colSpan={5} className="text-center py-8 text-muted-foreground">No organizations found.</TableCell>
                </TableRow>
              )}
              {filtered.map((o) => (
                <TableRow key={o.id}>
                  <TableCell className="font-medium">{o.organizationName}</TableCell>
                  <TableCell className="text-sm text-muted-foreground">{o.college?.name ?? '—'}</TableCell>
                  <TableCell className="text-sm text-muted-foreground">{o.dateCreated ?? '—'}</TableCell>
                  <TableCell>
                    <StatusBadge status={o.isActive ? 'Active' : 'Inactive'} />
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-1">
                      <Button size="icon" variant="ghost" onClick={() => navigate(`/admin/organizations/${o.id}`)}>
                        <Eye className="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" onClick={() => setToggleTarget(o)}>
                        {o.isActive
                          ? <PowerOff className="h-4 w-4 text-muted-foreground" />
                          : <Power className="h-4 w-4 text-green-600" />}
                      </Button>
                      <Button
                        size="icon"
                        variant="ghost"
                        className="text-destructive hover:text-destructive"
                        onClick={() => setDeleteTarget(o)}
                      >
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <Dialog open={!!toggleTarget} onOpenChange={() => setToggleTarget(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>
              {toggleTarget?.isActive ? 'Deactivate' : 'Activate'} Organization
            </DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to {toggleTarget?.isActive ? 'deactivate' : 'activate'}{' '}
            <strong>{toggleTarget?.organizationName}</strong>?
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setToggleTarget(null)}>Cancel</Button>
            <Button
              variant={toggleTarget?.isActive ? 'destructive' : 'default'}
              disabled={toggleMutation.isPending}
              onClick={() =>
                toggleTarget &&
                toggleMutation.mutate({ id: toggleTarget.id, isActive: !toggleTarget.isActive })
              }
            >
              {toggleMutation.isPending
                ? 'Updating…'
                : toggleTarget?.isActive ? 'Deactivate' : 'Activate'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <Dialog open={!!deleteTarget} onOpenChange={() => setDeleteTarget(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Organization</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to delete <strong>{deleteTarget?.organizationName}</strong>? This cannot be undone.
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteTarget(null)}>Cancel</Button>
            <Button
              variant="destructive"
              disabled={deleteMutation.isPending}
              onClick={() => deleteTarget && deleteMutation.mutate(deleteTarget.id)}
            >
              {deleteMutation.isPending ? 'Deleting…' : 'Delete'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
```

- [ ] **Step 2: Type-check**

```bash
cd client
npx tsc --noEmit
```

Expected: no errors (aside from the two remaining unresolved page imports in main.tsx for AddOrganizationPage and OrganizationDetailPage).

- [ ] **Step 3: Commit**

```bash
git add src/pages/organizations/OrganizationsPage.tsx
git commit -m "feat: add organizations list page with filter, toggle, and delete"
```

---

## Task 7: Frontend — AddOrganizationPage

**Files:**
- Create: `client/src/pages/organizations/AddOrganizationPage.tsx`

- [ ] **Step 1: Create the add organization page**

Create `client/src/pages/organizations/AddOrganizationPage.tsx`:

```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { createOrganization } from '@/api/organizations';
import { getColleges } from '@/api/colleges';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function AddOrganizationPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: colleges = [] } = useQuery({
    queryKey: ['colleges'],
    queryFn: getColleges,
  });

  const [form, setForm] = useState({
    organizationName: '',
    organizationDescription: '',
    dateCreated: '',
    collegeId: '',
  });
  const [error, setError] = useState('');

  const mutation = useMutation({
    mutationFn: createOrganization,
    onSuccess: (org) => {
      queryClient.invalidateQueries({ queryKey: ['organizations'] });
      navigate(`/admin/organizations/${org.id}`);
    },
    onError: () => setError('Failed to create organization. Check required fields.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate({
      organizationName: form.organizationName,
      organizationDescription: form.organizationDescription || null,
      dateCreated: form.dateCreated || null,
      collegeId: form.collegeId ? Number(form.collegeId) : null,
      isActive: true,
    });
  };

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/organizations')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Add Organization</h1>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Organization Details</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-1">
              <Label htmlFor="orgName">
                Organization Name <span className="text-destructive">*</span>
              </Label>
              <Input
                id="orgName"
                value={form.organizationName}
                onChange={e => set('organizationName', e.target.value)}
                required
              />
            </div>

            <div className="space-y-1">
              <Label htmlFor="orgDesc">Description</Label>
              <Textarea
                id="orgDesc"
                value={form.organizationDescription}
                onChange={e => set('organizationDescription', e.target.value)}
                rows={3}
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <Label htmlFor="dateCreated">Date Created</Label>
                <Input
                  id="dateCreated"
                  type="date"
                  value={form.dateCreated}
                  onChange={e => set('dateCreated', e.target.value)}
                />
              </div>
              <div className="space-y-1">
                <Label>College</Label>
                <Select value={form.collegeId} onValueChange={v => set('collegeId', v)}>
                  <SelectTrigger>
                    <SelectValue placeholder="Select college (optional)" />
                  </SelectTrigger>
                  <SelectContent>
                    {colleges.map(c => (
                      <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2">
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Creating…' : 'Create Organization'}
          </Button>
          <Button type="button" variant="outline" onClick={() => navigate('/admin/organizations')}>
            Cancel
          </Button>
        </div>
      </form>
    </div>
  );
}
```

- [ ] **Step 2: Type-check**

```bash
cd client
npx tsc --noEmit
```

Expected: no errors (aside from OrganizationDetailPage still missing).

- [ ] **Step 3: Commit**

```bash
git add src/pages/organizations/AddOrganizationPage.tsx
git commit -m "feat: add organization creation form page"
```

---

## Task 8: Frontend — OrganizationDetailPage

**Files:**
- Create: `client/src/pages/organizations/OrganizationDetailPage.tsx`

- [ ] **Step 1: Create the organization detail page**

Create `client/src/pages/organizations/OrganizationDetailPage.tsx`:

```tsx
import { useState } from 'react';
import { useNavigate, useParams } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, UserPlus } from 'lucide-react';
import {
  getOrganization,
  updateOrganization,
  getOrgMembers,
  addOrgMember,
  removeOrgMember,
} from '@/api/organizations';
import { getStudents } from '@/api/students';
import { getFaculty } from '@/api/faculty';
import type { OrgMember, Student, Faculty } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  Table, TableBody, TableCell, TableHead, TableHeader, TableRow,
} from '@/components/ui/table';
import {
  Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import { Skeleton } from '@/components/ui/skeleton';
import StatusBadge from '@/components/shared/StatusBadge';

export default function OrganizationDetailPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const orgId = Number(id);

  const { data: org, isLoading } = useQuery({
    queryKey: ['organization', orgId],
    queryFn: () => getOrganization(orgId),
    enabled: !!orgId,
  });

  const { data: members = [] } = useQuery({
    queryKey: ['org-members', orgId],
    queryFn: () => getOrgMembers(orgId),
    enabled: !!orgId,
  });

  const { data: students = [] } = useQuery({
    queryKey: ['students'],
    queryFn: getStudents,
  });

  const { data: faculty = [] } = useQuery({
    queryKey: ['faculty'],
    queryFn: getFaculty,
  });

  const toggleMutation = useMutation({
    mutationFn: (isActive: boolean) => updateOrganization(orgId, { isActive }),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['organization', orgId] }),
  });

  const removeMutation = useMutation({
    mutationFn: (memberId: number) => removeOrgMember(memberId),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['org-members', orgId] }),
  });

  const addMutation = useMutation({
    mutationFn: addOrgMember,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['org-members', orgId] });
      closeAddDialog();
    },
  });

  const studentMembers = members.filter(m => m.user?.student != null);
  const facultyMembers = members.filter(m => m.user?.faculty != null);

  const [showFormerStudents, setShowFormerStudents] = useState(false);
  const [showFormerFaculty, setShowFormerFaculty] = useState(false);

  type DialogType = 'student' | 'faculty' | null;
  const [addDialogType, setAddDialogType] = useState<DialogType>(null);
  const [memberSearch, setMemberSearch] = useState('');
  const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
  const [memberRole, setMemberRole] = useState('');
  const [memberDateJoined, setMemberDateJoined] = useState('');
  const [addError, setAddError] = useState('');

  const openAddDialog = (type: 'student' | 'faculty') => {
    setAddDialogType(type);
    setMemberSearch('');
    setSelectedUserId(null);
    setMemberRole('');
    setMemberDateJoined('');
    setAddError('');
  };

  const closeAddDialog = () => {
    setAddDialogType(null);
    setAddError('');
  };

  const handleAddMember = () => {
    if (!selectedUserId) {
      setAddError('Please select a member.');
      return;
    }
    addMutation.mutate({
      userId: selectedUserId,
      organizationId: orgId,
      role: memberRole || null,
      dateJoined: memberDateJoined || null,
    });
  };

  const existingUserIds = new Set(members.map(m => m.userId));

  const filteredStudentsForDialog = students.filter(s => {
    if (existingUserIds.has(s.userId)) return false;
    const name = `${s.firstName} ${s.lastName}`.toLowerCase();
    return (
      name.includes(memberSearch.toLowerCase()) ||
      (s.studentId ?? '').toLowerCase().includes(memberSearch.toLowerCase())
    );
  });

  const filteredFacultyForDialog = (faculty as Faculty[]).filter(f => {
    if (existingUserIds.has(f.userId)) return false;
    const name = `${f.firstName} ${f.lastName}`.toLowerCase();
    return name.includes(memberSearch.toLowerCase());
  });

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-64" />
        <Skeleton className="h-48" />
      </div>
    );
  }

  if (!org) {
    return (
      <p className="text-muted-foreground">
        Organization not found.{' '}
        <Button variant="link" className="p-0" onClick={() => navigate('/admin/organizations')}>
          Go back
        </Button>
      </p>
    );
  }

  const visibleStudents = showFormerStudents
    ? studentMembers
    : studentMembers.filter(m => !m.dateLeft);

  const visibleFaculty = showFormerFaculty
    ? facultyMembers
    : facultyMembers.filter(m => !m.dateLeft);

  const MemberRow = ({ m, cols }: { m: OrgMember; cols: 'student' | 'faculty' }) => (
    <TableRow className={m.dateLeft ? 'opacity-60' : ''}>
      {cols === 'student' && (
        <TableCell className="text-sm">{m.user?.student?.studentId ?? '—'}</TableCell>
      )}
      <TableCell className="font-medium">{m.user?.firstName} {m.user?.lastName}</TableCell>
      {cols === 'student' && (
        <TableCell className="text-sm text-muted-foreground">
          {m.user?.student?.program?.name ?? '—'}
        </TableCell>
      )}
      {cols === 'faculty' && (
        <>
          <TableCell className="text-sm text-muted-foreground">{m.user?.faculty?.position ?? '—'}</TableCell>
          <TableCell className="text-sm text-muted-foreground">{m.user?.faculty?.department ?? '—'}</TableCell>
        </>
      )}
      <TableCell className="text-sm">{m.role ?? '—'}</TableCell>
      <TableCell className="text-sm">{m.dateJoined ?? '—'}</TableCell>
      <TableCell className="text-sm">{m.dateLeft ?? '—'}</TableCell>
      <TableCell className="text-right">
        {!m.dateLeft && (
          <Button
            size="sm"
            variant="ghost"
            className="text-destructive hover:text-destructive"
            disabled={removeMutation.isPending}
            onClick={() => removeMutation.mutate(m.id)}
          >
            Remove
          </Button>
        )}
      </TableCell>
    </TableRow>
  );

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/organizations')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">{org.organizationName}</h1>
        <StatusBadge status={org.isActive ? 'Active' : 'Inactive'} />
      </div>

      <Tabs defaultValue="overview">
        <TabsList>
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="students">
            Students ({studentMembers.filter(m => !m.dateLeft).length})
          </TabsTrigger>
          <TabsTrigger value="faculty">
            Faculty ({facultyMembers.filter(m => !m.dateLeft).length})
          </TabsTrigger>
        </TabsList>

        <TabsContent value="overview" className="space-y-4 mt-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-sm text-muted-foreground">College</p>
                  <p className="font-medium">{org.college?.name ?? '—'}</p>
                </div>
                <div>
                  <p className="text-sm text-muted-foreground">Date Created</p>
                  <p className="font-medium">{org.dateCreated ?? '—'}</p>
                </div>
              </div>
              <Separator />
              <div>
                <p className="text-sm text-muted-foreground">Description</p>
                <p className="mt-1 text-sm">{org.organizationDescription ?? '—'}</p>
              </div>
              <Separator />
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-muted-foreground mb-1">Status</p>
                  <StatusBadge status={org.isActive ? 'Active' : 'Inactive'} />
                </div>
                <Button
                  size="sm"
                  variant={org.isActive ? 'destructive' : 'default'}
                  disabled={toggleMutation.isPending}
                  onClick={() => toggleMutation.mutate(!org.isActive)}
                >
                  {toggleMutation.isPending
                    ? 'Updating…'
                    : org.isActive ? 'Deactivate' : 'Activate'}
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="students" className="space-y-4 mt-4">
          <div className="flex items-center justify-between">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setShowFormerStudents(v => !v)}
            >
              {showFormerStudents ? 'Hide Former Members' : 'Show Former Members'}
            </Button>
            <Button size="sm" onClick={() => openAddDialog('student')}>
              <UserPlus className="h-4 w-4 mr-2" /> Add Student
            </Button>
          </div>
          <Card>
            <CardContent className="p-0">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Student ID</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Program</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Date Joined</TableHead>
                    <TableHead>Date Left</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {visibleStudents.length === 0 && (
                    <TableRow>
                      <TableCell colSpan={7} className="text-center py-8 text-muted-foreground">
                        No student members.
                      </TableCell>
                    </TableRow>
                  )}
                  {visibleStudents.map(m => <MemberRow key={m.id} m={m} cols="student" />)}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="faculty" className="space-y-4 mt-4">
          <div className="flex items-center justify-between">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setShowFormerFaculty(v => !v)}
            >
              {showFormerFaculty ? 'Hide Former Members' : 'Show Former Members'}
            </Button>
            <Button size="sm" onClick={() => openAddDialog('faculty')}>
              <UserPlus className="h-4 w-4 mr-2" /> Add Faculty
            </Button>
          </div>
          <Card>
            <CardContent className="p-0">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Position</TableHead>
                    <TableHead>Department</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Date Joined</TableHead>
                    <TableHead>Date Left</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {visibleFaculty.length === 0 && (
                    <TableRow>
                      <TableCell colSpan={7} className="text-center py-8 text-muted-foreground">
                        No faculty members.
                      </TableCell>
                    </TableRow>
                  )}
                  {visibleFaculty.map(m => <MemberRow key={m.id} m={m} cols="faculty" />)}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <Dialog open={!!addDialogType} onOpenChange={closeAddDialog}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>
              Add {addDialogType === 'student' ? 'Student' : 'Faculty'} Member
            </DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <div className="space-y-1">
              <Label>Search</Label>
              <Input
                placeholder={
                  addDialogType === 'student'
                    ? 'Search by name or student ID…'
                    : 'Search by name…'
                }
                value={memberSearch}
                onChange={e => {
                  setMemberSearch(e.target.value);
                  setSelectedUserId(null);
                }}
              />
            </div>
            <div className="max-h-40 overflow-y-auto border rounded-md divide-y">
              {(addDialogType === 'student' ? filteredStudentsForDialog : filteredFacultyForDialog).map(p => {
                const userId = p.userId;
                const isSelected = selectedUserId === userId;
                return (
                  <div
                    key={userId}
                    className={`px-3 py-2 cursor-pointer hover:bg-muted text-sm select-none ${isSelected ? 'bg-muted font-medium' : ''}`}
                    onClick={() => setSelectedUserId(userId)}
                  >
                    {p.firstName} {p.lastName}
                    {addDialogType === 'student' && (p as Student).studentId && (
                      <span className="ml-2 text-muted-foreground text-xs">
                        {(p as Student).studentId}
                      </span>
                    )}
                  </div>
                );
              })}
              {(addDialogType === 'student' ? filteredStudentsForDialog : filteredFacultyForDialog).length === 0 && (
                <p className="px-3 py-2 text-sm text-muted-foreground">No results.</p>
              )}
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1">
                <Label>Role (optional)</Label>
                <Input
                  value={memberRole}
                  onChange={e => setMemberRole(e.target.value)}
                  placeholder="e.g. President"
                />
              </div>
              <div className="space-y-1">
                <Label>Date Joined (optional)</Label>
                <Input
                  type="date"
                  value={memberDateJoined}
                  onChange={e => setMemberDateJoined(e.target.value)}
                />
              </div>
            </div>
            {addError && <p className="text-sm text-destructive">{addError}</p>}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={closeAddDialog}>Cancel</Button>
            <Button onClick={handleAddMember} disabled={addMutation.isPending}>
              {addMutation.isPending ? 'Adding…' : 'Add Member'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
```

- [ ] **Step 2: Type-check — all errors should now be resolved**

```bash
cd client
npx tsc --noEmit
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add src/pages/organizations/OrganizationDetailPage.tsx
git commit -m "feat: add organization detail page with member management"
```

---

## Final Verification

- [ ] Start both servers and exercise the full flow in a browser:
  1. Navigate to `/admin/organizations` — list loads, filter by name and status work
  2. Click "Add Organization" — fill the form, submit — redirects to detail page
  3. On detail page Overview tab — toggle Deactivate / Activate works; status badge updates
  4. On Students tab — "Add Student" dialog opens, search filters, selecting a name highlights it, submit adds the member to the table
  5. On Students tab — click "Remove" on a member — row fades, "Remove" button disappears, "Show Former Members" toggle reveals the row again with a Date Left
  6. On Faculty tab — same flow as students
  7. Back on list page — toggle active/inactive via power icon — confirmation dialog appears, confirm — status badge updates
  8. Delete an organization — confirmation dialog appears, confirm — row disappears
