# Faculty Module Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the full Faculty Module — list with filters, individual faculty profile with tabs (info, employment, education, awards), add and edit forms, and delete with confirmation.

**Architecture:** Mirrors the student module. Each page is self-contained with its own React Query hooks. Mutations invalidate the `['faculty']` query key. Faculty model fields: `position`, `employmentType`, `employmentDate`, `monthlyIncome`, `department` (no `rank` field). Related data comes from `jobHistory` (via `facultyId`) and `awards`/`eduBackground` (via `userId`).

**Tech Stack:** React 19, TypeScript, React Query v5, React Router v7, shadcn/ui, Lucide React

**Prerequisites:**
- Plan 01 (Foundation) complete
- Plan 02 (Backend Additions) complete — FacultyResource must return `jobHistory`, `eduBackground`, `awards` on show

---

### Task 1: Faculty list page

**Files:**
- Modify: `client/src/pages/faculty/FacultyPage.tsx`

- [ ] **Step 1: Write FacultyPage**

Replace `client/src/pages/faculty/FacultyPage.tsx`:
```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Pencil, Trash2, Eye, Search } from 'lucide-react';
import { getFaculty, deleteFaculty } from '@/api/faculty';
import { Faculty } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import StatusBadge from '@/components/shared/StatusBadge';

export default function FacultyPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [search, setSearch] = useState('');
  const [deptFilter, setDeptFilter] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [deleteTarget, setDeleteTarget] = useState<Faculty | null>(null);

  const { data: faculty = [], isLoading } = useQuery({
    queryKey: ['faculty'],
    queryFn: () => getFaculty(),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteFaculty(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['faculty'] });
      setDeleteTarget(null);
    },
  });

  const departments = [...new Set(faculty.map(f => f.department).filter(Boolean))] as string[];

  const filtered = faculty.filter((f) => {
    const fullName = `${f.firstName} ${f.lastName}`.toLowerCase();
    const matchName = fullName.includes(search.toLowerCase());
    const matchDept = !deptFilter || f.department === deptFilter;
    const matchType = !typeFilter || f.employmentType === typeFilter;
    return matchName && matchDept && matchType;
  });

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Faculty</h1>
        <Button onClick={() => navigate('/admin/faculty/add')}>
          <Plus className="h-4 w-4 mr-2" /> Add Faculty
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
              <Input className="pl-8" placeholder="Search name…" value={search} onChange={e => setSearch(e.target.value)} />
            </div>
            <Select value={deptFilter} onValueChange={setDeptFilter}>
              <SelectTrigger className="w-52">
                <SelectValue placeholder="All Departments" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All Departments</SelectItem>
                {departments.map(d => <SelectItem key={d} value={d}>{d}</SelectItem>)}
              </SelectContent>
            </Select>
            <Select value={typeFilter} onValueChange={setTypeFilter}>
              <SelectTrigger className="w-44">
                <SelectValue placeholder="Employment Type" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All Types</SelectItem>
                {['Full-Time', 'Part-Time', 'Contractual'].map(t => (
                  <SelectItem key={t} value={t}>{t}</SelectItem>
                ))}
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
                <TableHead>Department</TableHead>
                <TableHead>Position</TableHead>
                <TableHead>Employment Type</TableHead>
                <TableHead>Email</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {isLoading && (
                <TableRow><TableCell colSpan={6} className="text-center py-8 text-muted-foreground">Loading…</TableCell></TableRow>
              )}
              {!isLoading && filtered.length === 0 && (
                <TableRow><TableCell colSpan={6} className="text-center py-8 text-muted-foreground">No faculty found.</TableCell></TableRow>
              )}
              {filtered.map((f) => (
                <TableRow key={f.id}>
                  <TableCell className="font-medium">{f.firstName} {f.lastName}</TableCell>
                  <TableCell className="text-sm text-muted-foreground">{f.department ?? '—'}</TableCell>
                  <TableCell className="text-sm">{f.position ?? '—'}</TableCell>
                  <TableCell>{f.employmentType ? <StatusBadge status={f.employmentType} /> : '—'}</TableCell>
                  <TableCell className="text-sm text-muted-foreground">{f.email}</TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-1">
                      <Button size="icon" variant="ghost" onClick={() => navigate(`/admin/faculty/${f.id}`)}>
                        <Eye className="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" onClick={() => navigate(`/admin/faculty/${f.id}/edit`)}>
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" className="text-destructive hover:text-destructive" onClick={() => setDeleteTarget(f)}>
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

      <Dialog open={!!deleteTarget} onOpenChange={() => setDeleteTarget(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Faculty Member</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to delete <strong>{deleteTarget?.firstName} {deleteTarget?.lastName}</strong>? This cannot be undone.
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteTarget(null)}>Cancel</Button>
            <Button variant="destructive" onClick={() => deleteTarget && deleteMutation.mutate(deleteTarget.id)} disabled={deleteMutation.isPending}>
              {deleteMutation.isPending ? 'Deleting…' : 'Delete'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
```

- [ ] **Step 2: Verify in browser**

Navigate to `/admin/faculty`. Expected: table with 5 seeded faculty members. Search and department filter work. Delete dialog opens and removes the row after confirmation.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/faculty/FacultyPage.tsx
git commit -m "feat: implement faculty list page with filters and delete"
```

---

### Task 2: Faculty detail page

**Files:**
- Modify: `client/src/pages/faculty/FacultyDetailPage.tsx`

- [ ] **Step 1: Write FacultyDetailPage**

Replace `client/src/pages/faculty/FacultyDetailPage.tsx`:
```tsx
import { useParams, useNavigate } from 'react-router';
import { useQuery } from '@tanstack/react-query';
import { ArrowLeft, Pencil } from 'lucide-react';
import { getFacultyMember } from '@/api/faculty';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Separator } from '@/components/ui/separator';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Skeleton } from '@/components/ui/skeleton';
import { Badge } from '@/components/ui/badge';

export default function FacultyDetailPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const { data: member, isLoading } = useQuery({
    queryKey: ['faculty', Number(id)],
    queryFn: () => getFacultyMember(Number(id)),
    enabled: !!id,
  });

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <div className="grid grid-cols-3 gap-4">
          <Skeleton className="h-64 col-span-1" />
          <Skeleton className="h-64 col-span-2" />
        </div>
      </div>
    );
  }

  if (!member) return <p className="text-muted-foreground">Faculty member not found.</p>;

  const initials = `${member.firstName[0]}${member.lastName[0]}`.toUpperCase();

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/faculty')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <Button size="sm" onClick={() => navigate(`/admin/faculty/${member.id}/edit`)}>
          <Pencil className="h-4 w-4 mr-1" /> Edit
        </Button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Left card */}
        <Card>
          <CardContent className="pt-6 flex flex-col items-center text-center space-y-3">
            <Avatar className="h-20 w-20">
              <AvatarFallback className="text-2xl bg-primary/10 text-primary">{initials}</AvatarFallback>
            </Avatar>
            <div>
              <h2 className="font-bold text-lg">{member.firstName} {member.middleName ? member.middleName + ' ' : ''}{member.lastName}</h2>
              {member.position && <p className="text-sm text-muted-foreground">{member.position}</p>}
            </div>
            {member.employmentType && <Badge variant="outline">{member.employmentType}</Badge>}
            <Separator />
            <div className="w-full text-left space-y-1 text-sm">
              <p><span className="text-muted-foreground">Email:</span> {member.email}</p>
              {member.mobileNumber && <p><span className="text-muted-foreground">Mobile:</span> {member.mobileNumber}</p>}
              {member.department && <p><span className="text-muted-foreground">Dept:</span> {member.department}</p>}
              {member.city && <p><span className="text-muted-foreground">City:</span> {member.city}{member.province ? `, ${member.province}` : ''}</p>}
            </div>
          </CardContent>
        </Card>

        {/* Right tabbed panel */}
        <div className="md:col-span-2">
          <Tabs defaultValue="info">
            <TabsList className="mb-4">
              <TabsTrigger value="info">Info</TabsTrigger>
              <TabsTrigger value="employment">Employment</TabsTrigger>
              <TabsTrigger value="education">Education</TabsTrigger>
              <TabsTrigger value="awards">Awards</TabsTrigger>
            </TabsList>

            <TabsContent value="info">
              <Card>
                <CardHeader><CardTitle className="text-base">Faculty Information</CardTitle></CardHeader>
                <CardContent className="grid grid-cols-2 gap-3 text-sm">
                  {[
                    ['Department', member.department ?? '—'],
                    ['Position', member.position ?? '—'],
                    ['Employment Type', member.employmentType ?? '—'],
                    ['Employment Date', member.employmentDate ?? '—'],
                    ['Birth Date', member.birthDate ?? '—'],
                    ['City', member.city ?? '—'],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <p className="text-muted-foreground text-xs">{label}</p>
                      <p className="font-medium">{value}</p>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="employment">
              <Card>
                <CardHeader><CardTitle className="text-base">Job History</CardTitle></CardHeader>
                <CardContent>
                  {(!member.jobHistory || member.jobHistory.length === 0) ? (
                    <p className="text-sm text-muted-foreground">No job history recorded.</p>
                  ) : (
                    <div className="space-y-3">
                      {member.jobHistory.map(j => (
                        <div key={j.id} className="border-l-2 border-primary pl-3 text-sm">
                          <p className="font-medium">{j.position}</p>
                          {j.company && <p className="text-muted-foreground">{j.company}{j.workLocation ? ` — ${j.workLocation}` : ''}</p>}
                          <p className="text-muted-foreground text-xs">
                            {j.employmentDate ?? '?'} – {j.employmentEndDate ?? 'present'}
                            {j.employmentType ? ` · ${j.employmentType}` : ''}
                          </p>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="education">
              <Card>
                <CardHeader><CardTitle className="text-base">Educational Background</CardTitle></CardHeader>
                <CardContent>
                  {(!member.eduBackground || member.eduBackground.length === 0) ? (
                    <p className="text-sm text-muted-foreground">No educational background recorded.</p>
                  ) : (
                    <div className="space-y-3">
                      {member.eduBackground.map(e => (
                        <div key={e.id} className="border-l-2 border-primary pl-3 text-sm">
                          <p className="font-medium">{e.type ? `${e.type} — ` : ''}{e.schoolUniversity}</p>
                          <p className="text-muted-foreground">
                            {e.startYear ?? '?'} – {e.graduateYear ?? 'present'}
                            {e.award ? ` · ${e.award}` : ''}
                          </p>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="awards">
              <Card>
                <CardHeader><CardTitle className="text-base">Awards & Recognition</CardTitle></CardHeader>
                <CardContent>
                  {(!member.awards || member.awards.length === 0) ? (
                    <p className="text-sm text-muted-foreground">No awards recorded.</p>
                  ) : (
                    <ul className="space-y-2">
                      {member.awards.map(a => (
                        <li key={a.id} className="text-sm border-l-2 border-primary pl-3">
                          <p className="font-medium">{a.title}</p>
                          {a.awardingOrganization && <p className="text-muted-foreground">{a.awardingOrganization} {a.awardingDate ? `· ${a.awardingDate}` : ''}</p>}
                          {a.awardingLocation && <p className="text-muted-foreground text-xs">{a.awardingLocation}</p>}
                        </li>
                      ))}
                    </ul>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Verify in browser**

Click "View" on a faculty member. Expected: profile with left card and tabs. Employment tab shows job history entries. Education tab shows school/degree entries. Back navigates to faculty list.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/faculty/FacultyDetailPage.tsx
git commit -m "feat: implement faculty detail page with employment, education, awards tabs"
```

---

### Task 3: Add faculty page

**Files:**
- Modify: `client/src/pages/faculty/AddFacultyPage.tsx`

- [ ] **Step 1: Write AddFacultyPage**

Replace `client/src/pages/faculty/AddFacultyPage.tsx`:
```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { createFaculty } from '@/api/faculty';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function AddFacultyPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    department: '', position: '', employmentType: '', employmentDate: '',
  });
  const [error, setError] = useState('');

  const mutation = useMutation({
    mutationFn: createFaculty,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['faculty'] });
      navigate('/admin/faculty');
    },
    onError: () => setError('Failed to create faculty. Check all required fields.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate(form as any);
  };

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/faculty')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Add Faculty</h1>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <Card>
          <CardHeader><CardTitle className="text-base">Personal Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            {[
              { key: 'firstName', label: 'First Name', required: true },
              { key: 'lastName', label: 'Last Name', required: true },
              { key: 'middleName', label: 'Middle Name' },
              { key: 'email', label: 'Email', type: 'email', required: true },
              { key: 'mobileNumber', label: 'Mobile Number' },
              { key: 'birthDate', label: 'Birth Date', type: 'date' },
              { key: 'city', label: 'City' },
              { key: 'province', label: 'Province' },
            ].map(({ key, label, type = 'text', required }) => (
              <div key={key} className="space-y-1">
                <Label htmlFor={key}>{label}{required && <span className="text-destructive ml-1">*</span>}</Label>
                <Input id={key} type={type} value={(form as any)[key]} onChange={e => set(key, e.target.value)} required={required} />
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Faculty Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div className="space-y-1">
              <Label htmlFor="department">Department</Label>
              <Input id="department" value={form.department} onChange={e => set('department', e.target.value)} placeholder="e.g. Information Technology" />
            </div>
            <div className="space-y-1">
              <Label htmlFor="position">Position</Label>
              <Input id="position" value={form.position} onChange={e => set('position', e.target.value)} placeholder="e.g. Associate Professor" />
            </div>
            <div className="space-y-1">
              <Label>Employment Type</Label>
              <Select value={form.employmentType} onValueChange={v => set('employmentType', v)}>
                <SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger>
                <SelectContent>
                  {['Full-Time', 'Part-Time', 'Contractual'].map(t => <SelectItem key={t} value={t}>{t}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label htmlFor="employmentDate">Employment Date</Label>
              <Input id="employmentDate" type="date" value={form.employmentDate} onChange={e => set('employmentDate', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2 justify-end">
          <Button type="button" variant="outline" onClick={() => navigate('/admin/faculty')}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Add Faculty'}
          </Button>
        </div>
      </form>
    </div>
  );
}
```

- [ ] **Step 2: Verify in browser**

Navigate to Add Faculty. Fill in First Name, Last Name, Email. Submit. Expected: redirects to faculty list, new faculty member appears in the table.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/faculty/AddFacultyPage.tsx
git commit -m "feat: implement add faculty form"
```

---

### Task 4: Edit faculty page

**Files:**
- Modify: `client/src/pages/faculty/EditFacultyPage.tsx`

- [ ] **Step 1: Write EditFacultyPage**

Replace `client/src/pages/faculty/EditFacultyPage.tsx`:
```tsx
import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { getFacultyMember, updateFaculty } from '@/api/faculty';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';

export default function EditFacultyPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: member, isLoading } = useQuery({
    queryKey: ['faculty', Number(id)],
    queryFn: () => getFacultyMember(Number(id)),
    enabled: !!id,
  });

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    department: '', position: '', employmentType: '', employmentDate: '',
  });
  const [error, setError] = useState('');

  useEffect(() => {
    if (member) {
      setForm({
        firstName: member.firstName ?? '',
        lastName: member.lastName ?? '',
        middleName: member.middleName ?? '',
        email: member.email ?? '',
        mobileNumber: member.mobileNumber ?? '',
        birthDate: member.birthDate ?? '',
        city: member.city ?? '',
        province: member.province ?? '',
        department: member.department ?? '',
        position: member.position ?? '',
        employmentType: member.employmentType ?? '',
        employmentDate: member.employmentDate ?? '',
      });
    }
  }, [member]);

  const mutation = useMutation({
    mutationFn: (payload: any) => updateFaculty(Number(id), payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['faculty'] });
      queryClient.invalidateQueries({ queryKey: ['faculty', Number(id)] });
      navigate(`/admin/faculty/${id}`);
    },
    onError: () => setError('Failed to update faculty.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate(form);
  };

  if (isLoading) return <Skeleton className="h-96 w-full" />;

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate(`/admin/faculty/${id}`)}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Edit Faculty</h1>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <Card>
          <CardHeader><CardTitle className="text-base">Personal Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            {[
              { key: 'firstName', label: 'First Name', required: true },
              { key: 'lastName', label: 'Last Name', required: true },
              { key: 'middleName', label: 'Middle Name' },
              { key: 'email', label: 'Email', type: 'email', required: true },
              { key: 'mobileNumber', label: 'Mobile Number' },
              { key: 'birthDate', label: 'Birth Date', type: 'date' },
              { key: 'city', label: 'City' },
              { key: 'province', label: 'Province' },
            ].map(({ key, label, type = 'text', required }) => (
              <div key={key} className="space-y-1">
                <Label htmlFor={key}>{label}{required && <span className="text-destructive ml-1">*</span>}</Label>
                <Input id={key} type={type} value={(form as any)[key]} onChange={e => set(key, e.target.value)} required={required} />
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Faculty Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div className="space-y-1">
              <Label htmlFor="department">Department</Label>
              <Input id="department" value={form.department} onChange={e => set('department', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="position">Position</Label>
              <Input id="position" value={form.position} onChange={e => set('position', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label>Employment Type</Label>
              <Select value={form.employmentType} onValueChange={v => set('employmentType', v)}>
                <SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger>
                <SelectContent>
                  {['Full-Time', 'Part-Time', 'Contractual'].map(t => <SelectItem key={t} value={t}>{t}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label htmlFor="employmentDate">Employment Date</Label>
              <Input id="employmentDate" type="date" value={form.employmentDate} onChange={e => set('employmentDate', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2 justify-end">
          <Button type="button" variant="outline" onClick={() => navigate(`/admin/faculty/${id}`)}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Save Changes'}
          </Button>
        </div>
      </form>
    </div>
  );
}
```

- [ ] **Step 2: Verify in browser**

Click "Edit" on a faculty member. Form should be pre-filled. Change the department and save. Expected: redirects to detail page, updated department shown.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/faculty/EditFacultyPage.tsx
git commit -m "feat: implement edit faculty form pre-filled from existing data"
```
