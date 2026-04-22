# Student Module Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the full Student Profile Module — list with filters, individual profile view with tabs, add and edit forms, and delete with confirmation.

**Architecture:** Each page is a self-contained React component using React Query hooks for data. No prop drilling — each page fetches its own data. Mutations invalidate the `['students']` query key to keep the list fresh.

**Tech Stack:** React 19, TypeScript, React Query v5, React Router v7, shadcn/ui, Lucide React

**Prerequisites:**
- Plan 01 (Foundation) must be complete — types, API layer, layout, and stub pages must exist
- Plan 02 (Backend Additions) must be complete — StudentResource must return relations on show

---

### Task 1: Students list page

**Files:**
- Modify: `client/src/pages/students/StudentsPage.tsx`

Replace the stub with the full implementation.

- [ ] **Step 1: Write StudentsPage**

Replace `client/src/pages/students/StudentsPage.tsx`:
```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Pencil, Trash2, Eye, Search } from 'lucide-react';
import { getStudents, deleteStudent } from '@/api/students';
import { getPrograms } from '@/api/programs';
import { Student } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import StatusBadge from '@/components/shared/StatusBadge';

export default function StudentsPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const [search, setSearch] = useState('');
  const [programId, setProgramId] = useState<string>('');
  const [statusFilter, setStatusFilter] = useState<string>('');
  const [deleteTarget, setDeleteTarget] = useState<Student | null>(null);

  const { data: students = [], isLoading } = useQuery({
    queryKey: ['students'],
    queryFn: () => getStudents(),
  });

  const { data: programs = [] } = useQuery({
    queryKey: ['programs'],
    queryFn: getPrograms,
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteStudent(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['students'] });
      setDeleteTarget(null);
    },
  });

  const filtered = students.filter((s) => {
    const fullName = `${s.firstName} ${s.lastName}`.toLowerCase();
    const matchName = fullName.includes(search.toLowerCase()) || s.studentId.toLowerCase().includes(search.toLowerCase());
    const matchProgram = !programId || String(s.programId) === programId;
    const matchStatus = !statusFilter || s.status === statusFilter;
    return matchName && matchProgram && matchStatus;
  });

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Students</h1>
        <Button onClick={() => navigate('/admin/students/add')}>
          <Plus className="h-4 w-4 mr-2" /> Add Student
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
              <Input className="pl-8" placeholder="Search name or ID…" value={search} onChange={e => setSearch(e.target.value)} />
            </div>
            <Select value={programId} onValueChange={setProgramId}>
              <SelectTrigger className="w-52">
                <SelectValue placeholder="All Programs" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All Programs</SelectItem>
                {programs.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
              </SelectContent>
            </Select>
            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-36">
                <SelectValue placeholder="All Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">All Status</SelectItem>
                {['Active', 'Graduated', 'Dropped', 'Inactive'].map(s => (
                  <SelectItem key={s} value={s}>{s}</SelectItem>
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
                <TableHead>Student ID</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Program</TableHead>
                <TableHead>Year</TableHead>
                <TableHead>GPA</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {isLoading && (
                <TableRow><TableCell colSpan={7} className="text-center py-8 text-muted-foreground">Loading…</TableCell></TableRow>
              )}
              {!isLoading && filtered.length === 0 && (
                <TableRow><TableCell colSpan={7} className="text-center py-8 text-muted-foreground">No students found.</TableCell></TableRow>
              )}
              {filtered.map((s) => (
                <TableRow key={s.id}>
                  <TableCell className="font-mono text-sm">{s.studentId}</TableCell>
                  <TableCell className="font-medium">{s.firstName} {s.lastName}</TableCell>
                  <TableCell className="text-sm text-muted-foreground">{s.programName}</TableCell>
                  <TableCell>{s.yearLevel}</TableCell>
                  <TableCell>{s.gpa?.toFixed(2) ?? '—'}</TableCell>
                  <TableCell><StatusBadge status={s.status} /></TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-1">
                      <Button size="icon" variant="ghost" onClick={() => navigate(`/admin/students/${s.id}`)}>
                        <Eye className="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" onClick={() => navigate(`/admin/students/${s.id}/edit`)}>
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" className="text-destructive hover:text-destructive" onClick={() => setDeleteTarget(s)}>
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
            <DialogTitle>Delete Student</DialogTitle>
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

Start both servers (`php artisan serve` and `npm run dev`). Log in as admin@ccs.edu.ph / Admin@12345. Navigate to `/admin/students`. Expected: table with 20 seeded students, search and filter controls work, delete dialog opens.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/students/StudentsPage.tsx
git commit -m "feat: implement student list page with search, filter, and delete"
```

---

### Task 2: Student detail page

**Files:**
- Modify: `client/src/pages/students/StudentDetailPage.tsx`

- [ ] **Step 1: Write StudentDetailPage**

Replace `client/src/pages/students/StudentDetailPage.tsx`:
```tsx
import { useParams, useNavigate } from 'react-router';
import { useQuery } from '@tanstack/react-query';
import { ArrowLeft, Pencil } from 'lucide-react';
import { getStudent } from '@/api/students';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Separator } from '@/components/ui/separator';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Skeleton } from '@/components/ui/skeleton';
import StatusBadge from '@/components/shared/StatusBadge';
import SkillBadge from '@/components/shared/SkillBadge';

export default function StudentDetailPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const { data: student, isLoading } = useQuery({
    queryKey: ['students', Number(id)],
    queryFn: () => getStudent(Number(id)),
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

  if (!student) return <p className="text-muted-foreground">Student not found.</p>;

  const initials = `${student.firstName[0]}${student.lastName[0]}`.toUpperCase();

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/students')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <Button size="sm" onClick={() => navigate(`/admin/students/${student.id}/edit`)}>
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
              <h2 className="font-bold text-lg">{student.firstName} {student.middleName ? student.middleName + ' ' : ''}{student.lastName}</h2>
              <p className="text-sm text-muted-foreground font-mono">{student.studentId}</p>
            </div>
            <StatusBadge status={student.status} />
            <Separator />
            <div className="w-full text-left space-y-1 text-sm">
              <p><span className="text-muted-foreground">Email:</span> {student.email}</p>
              {student.mobileNumber && <p><span className="text-muted-foreground">Mobile:</span> {student.mobileNumber}</p>}
              {student.city && <p><span className="text-muted-foreground">City:</span> {student.city}{student.province ? `, ${student.province}` : ''}</p>}
              {student.birthDate && <p><span className="text-muted-foreground">Birth:</span> {student.birthDate}</p>}
            </div>
          </CardContent>
        </Card>

        {/* Right tabbed panel */}
        <div className="md:col-span-2">
          <Tabs defaultValue="academic">
            <TabsList className="mb-4">
              <TabsTrigger value="academic">Academic</TabsTrigger>
              <TabsTrigger value="grades">Grades</TabsTrigger>
              <TabsTrigger value="skills">Skills</TabsTrigger>
              <TabsTrigger value="activities">Activities</TabsTrigger>
            </TabsList>

            <TabsContent value="academic">
              <Card>
                <CardHeader><CardTitle className="text-base">Academic Information</CardTitle></CardHeader>
                <CardContent className="grid grid-cols-2 gap-3 text-sm">
                  {[
                    ['Program', student.programName],
                    ['Year Level', student.yearLevel],
                    ['GPA', student.gpa?.toFixed(2) ?? '—'],
                    ['Units Taken', student.unitsTaken],
                    ['Units Left', student.unitsLeft],
                    ['Date Enrolled', student.dateEnrolled ?? '—'],
                    ['Date Graduated', student.dateGraduated ?? '—'],
                    ['Date Dropped', student.dateDropped ?? '—'],
                  ].map(([label, value]) => (
                    <div key={String(label)}>
                      <p className="text-muted-foreground text-xs">{label}</p>
                      <p className="font-medium">{String(value)}</p>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="grades">
              <Card>
                <CardHeader><CardTitle className="text-base">Grades</CardTitle></CardHeader>
                <CardContent>
                  {(!student.grades || student.grades.length === 0) ? (
                    <p className="text-sm text-muted-foreground">No grade records.</p>
                  ) : (
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="border-b">
                          <th className="text-left py-2 font-medium text-muted-foreground">Course</th>
                          <th className="text-left py-2 font-medium text-muted-foreground">Term</th>
                          <th className="text-left py-2 font-medium text-muted-foreground">Grade</th>
                          <th className="text-left py-2 font-medium text-muted-foreground">AY</th>
                        </tr>
                      </thead>
                      <tbody>
                        {student.grades.map(g => (
                          <tr key={g.id} className="border-b last:border-0">
                            <td className="py-1.5">{g.courseName ?? `Course #${g.courseId}`}</td>
                            <td className="py-1.5 capitalize">{g.term}</td>
                            <td className="py-1.5 font-mono">{g.grade.toFixed(2)}</td>
                            <td className="py-1.5 text-muted-foreground">{g.academicYear}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="skills">
              <Card>
                <CardHeader><CardTitle className="text-base">Skills</CardTitle></CardHeader>
                <CardContent>
                  {(!student.skills || student.skills.length === 0) ? (
                    <p className="text-sm text-muted-foreground">No skills recorded.</p>
                  ) : (
                    <div className="flex flex-wrap gap-2">
                      {student.skills.map(skill => <SkillBadge key={skill.id} skill={skill} />)}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="activities">
              <div className="space-y-4">
                <Card>
                  <CardHeader><CardTitle className="text-base">Awards</CardTitle></CardHeader>
                  <CardContent>
                    {(!student.awards || student.awards.length === 0) ? (
                      <p className="text-sm text-muted-foreground">No awards recorded.</p>
                    ) : (
                      <ul className="space-y-2">
                        {student.awards.map(a => (
                          <li key={a.id} className="text-sm border-l-2 border-primary pl-3">
                            <p className="font-medium">{a.title}</p>
                            {a.awardingOrganization && <p className="text-muted-foreground">{a.awardingOrganization} {a.awardingDate ? `· ${a.awardingDate}` : ''}</p>}
                          </li>
                        ))}
                      </ul>
                    )}
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader><CardTitle className="text-base">Extracurricular Activities</CardTitle></CardHeader>
                  <CardContent>
                    {(!student.extraCurriculars || student.extraCurriculars.length === 0) ? (
                      <p className="text-sm text-muted-foreground">No extracurricular records.</p>
                    ) : (
                      <ul className="space-y-2">
                        {student.extraCurriculars.map(e => (
                          <li key={e.id} className="text-sm border-l-2 border-primary pl-3">
                            <p className="font-medium">{e.name} {e.role ? `— ${e.role}` : ''}</p>
                            {e.startDate && <p className="text-muted-foreground">{e.startDate} – {e.endDate ?? 'present'}</p>}
                          </li>
                        ))}
                      </ul>
                    )}
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Verify in browser**

Click any "View" button from the student list. Expected: profile page with left card (avatar, name, status) and right tabbed panel. Switch between tabs. Skills show orange/gray badges. Back button returns to list.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/students/StudentDetailPage.tsx
git commit -m "feat: implement student profile detail page with tabs"
```

---

### Task 3: Add student page

**Files:**
- Modify: `client/src/pages/students/AddStudentPage.tsx`

- [ ] **Step 1: Write AddStudentPage**

Replace `client/src/pages/students/AddStudentPage.tsx`:
```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { createStudent } from '@/api/students';
import { getPrograms } from '@/api/programs';
import { getSkills } from '@/api/skills';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { X } from 'lucide-react';

export default function AddStudentPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: programs = [] } = useQuery({ queryKey: ['programs'], queryFn: getPrograms });
  const { data: skills = [] } = useQuery({ queryKey: ['skills'], queryFn: getSkills });

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    programId: '', yearLevel: '1', status: 'Active', dateEnrolled: '',
  });
  const [selectedSkillIds, setSelectedSkillIds] = useState<number[]>([]);
  const [error, setError] = useState('');

  const mutation = useMutation({
    mutationFn: createStudent,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['students'] });
      navigate('/admin/students');
    },
    onError: () => setError('Failed to create student. Check all required fields.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const toggleSkill = (id: number) => {
    setSelectedSkillIds(prev =>
      prev.includes(id) ? prev.filter(s => s !== id) : [...prev, id]
    );
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate({
      ...form,
      programId: Number(form.programId),
      yearLevel: Number(form.yearLevel),
      skillIds: selectedSkillIds,
    } as any);
  };

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/students')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Add Student</h1>
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
          <CardHeader><CardTitle className="text-base">Academic Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div className="space-y-1">
              <Label>Program <span className="text-destructive">*</span></Label>
              <Select value={form.programId} onValueChange={v => set('programId', v)} required>
                <SelectTrigger><SelectValue placeholder="Select program" /></SelectTrigger>
                <SelectContent>
                  {programs.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Year Level <span className="text-destructive">*</span></Label>
              <Select value={form.yearLevel} onValueChange={v => set('yearLevel', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  {[1,2,3,4].map(y => <SelectItem key={y} value={String(y)}>Year {y}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Status</Label>
              <Select value={form.status} onValueChange={v => set('status', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  {['Active', 'Graduated', 'Dropped', 'Inactive'].map(s => <SelectItem key={s} value={s}>{s}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label htmlFor="dateEnrolled">Date Enrolled</Label>
              <Input id="dateEnrolled" type="date" value={form.dateEnrolled} onChange={e => set('dateEnrolled', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Skills</CardTitle></CardHeader>
          <CardContent className="space-y-2">
            {selectedSkillIds.length > 0 && (
              <div className="flex flex-wrap gap-1 mb-2">
                {selectedSkillIds.map(id => {
                  const skill = skills.find(s => s.id === id);
                  return skill ? (
                    <Badge key={id} variant="secondary" className="bg-primary/10 text-primary gap-1">
                      {skill.name}
                      <button type="button" onClick={() => toggleSkill(id)}><X className="h-3 w-3" /></button>
                    </Badge>
                  ) : null;
                })}
              </div>
            )}
            <div className="flex flex-wrap gap-1">
              {skills.filter(s => !selectedSkillIds.includes(s.id)).map(s => (
                <Badge key={s.id} variant="outline" className="cursor-pointer hover:bg-primary/10" onClick={() => toggleSkill(s.id)}>
                  {s.name}
                </Badge>
              ))}
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2 justify-end">
          <Button type="button" variant="outline" onClick={() => navigate('/admin/students')}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Add Student'}
          </Button>
        </div>
      </form>
    </div>
  );
}
```

- [ ] **Step 2: Verify in browser**

Click "Add Student". Fill in required fields (First Name, Last Name, Email, Program). Submit. Expected: redirects to student list, new student appears. If skills were selected, verify by viewing the student detail page.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/students/AddStudentPage.tsx
git commit -m "feat: implement add student form with skill multi-select"
```

---

### Task 4: Edit student page

**Files:**
- Modify: `client/src/pages/students/EditStudentPage.tsx`

- [ ] **Step 1: Write EditStudentPage**

Replace `client/src/pages/students/EditStudentPage.tsx`:
```tsx
import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { getStudent, updateStudent } from '@/api/students';
import { getPrograms } from '@/api/programs';
import { getSkills } from '@/api/skills';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { X } from 'lucide-react';

export default function EditStudentPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: student, isLoading } = useQuery({
    queryKey: ['students', Number(id)],
    queryFn: () => getStudent(Number(id)),
    enabled: !!id,
  });
  const { data: programs = [] } = useQuery({ queryKey: ['programs'], queryFn: getPrograms });
  const { data: skills = [] } = useQuery({ queryKey: ['skills'], queryFn: getSkills });

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    programId: '', yearLevel: '1', status: 'Active', dateEnrolled: '',
    dateGraduated: '', dateDropped: '', gpa: '',
  });
  const [selectedSkillIds, setSelectedSkillIds] = useState<number[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    if (student) {
      setForm({
        firstName: student.firstName ?? '',
        lastName: student.lastName ?? '',
        middleName: student.middleName ?? '',
        email: student.email ?? '',
        mobileNumber: student.mobileNumber ?? '',
        birthDate: student.birthDate ?? '',
        city: student.city ?? '',
        province: student.province ?? '',
        programId: String(student.programId),
        yearLevel: String(student.yearLevel),
        status: student.status,
        dateEnrolled: student.dateEnrolled ?? '',
        dateGraduated: student.dateGraduated ?? '',
        dateDropped: student.dateDropped ?? '',
        gpa: student.gpa != null ? String(student.gpa) : '',
      });
      setSelectedSkillIds(student.skills?.map(s => s.id) ?? []);
    }
  }, [student]);

  const mutation = useMutation({
    mutationFn: (payload: any) => updateStudent(Number(id), payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['students'] });
      queryClient.invalidateQueries({ queryKey: ['students', Number(id)] });
      navigate(`/admin/students/${id}`);
    },
    onError: () => setError('Failed to update student.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));
  const toggleSkill = (sid: number) => setSelectedSkillIds(prev => prev.includes(sid) ? prev.filter(x => x !== sid) : [...prev, sid]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate({
      ...form,
      programId: Number(form.programId),
      yearLevel: Number(form.yearLevel),
      gpa: form.gpa ? Number(form.gpa) : undefined,
      skillIds: selectedSkillIds,
    });
  };

  if (isLoading) return <Skeleton className="h-96 w-full" />;

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate(`/admin/students/${id}`)}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Edit Student</h1>
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
          <CardHeader><CardTitle className="text-base">Academic Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div className="space-y-1">
              <Label>Program <span className="text-destructive">*</span></Label>
              <Select value={form.programId} onValueChange={v => set('programId', v)}>
                <SelectTrigger><SelectValue placeholder="Select program" /></SelectTrigger>
                <SelectContent>
                  {programs.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Year Level</Label>
              <Select value={form.yearLevel} onValueChange={v => set('yearLevel', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  {[1,2,3,4].map(y => <SelectItem key={y} value={String(y)}>Year {y}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Status</Label>
              <Select value={form.status} onValueChange={v => set('status', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  {['Active', 'Graduated', 'Dropped', 'Inactive'].map(s => <SelectItem key={s} value={s}>{s}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label htmlFor="gpa">GPA (1.0–3.0)</Label>
              <Input id="gpa" type="number" step="0.01" min="1" max="3" value={form.gpa} onChange={e => set('gpa', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="dateEnrolled">Date Enrolled</Label>
              <Input id="dateEnrolled" type="date" value={form.dateEnrolled} onChange={e => set('dateEnrolled', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="dateGraduated">Date Graduated</Label>
              <Input id="dateGraduated" type="date" value={form.dateGraduated} onChange={e => set('dateGraduated', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Skills</CardTitle></CardHeader>
          <CardContent className="space-y-2">
            {selectedSkillIds.length > 0 && (
              <div className="flex flex-wrap gap-1 mb-2">
                {selectedSkillIds.map(sid => {
                  const skill = skills.find(s => s.id === sid);
                  return skill ? (
                    <Badge key={sid} variant="secondary" className="bg-primary/10 text-primary gap-1">
                      {skill.name}
                      <button type="button" onClick={() => toggleSkill(sid)}><X className="h-3 w-3" /></button>
                    </Badge>
                  ) : null;
                })}
              </div>
            )}
            <div className="flex flex-wrap gap-1">
              {skills.filter(s => !selectedSkillIds.includes(s.id)).map(s => (
                <Badge key={s.id} variant="outline" className="cursor-pointer hover:bg-primary/10" onClick={() => toggleSkill(s.id)}>
                  {s.name}
                </Badge>
              ))}
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2 justify-end">
          <Button type="button" variant="outline" onClick={() => navigate(`/admin/students/${id}`)}>Cancel</Button>
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

Click "Edit" on any student. Form should be pre-filled with existing data. Change the year level and save. Expected: redirected to detail page, updated value shown.

- [ ] **Step 3: Commit**
```bash
git add client/src/pages/students/EditStudentPage.tsx
git commit -m "feat: implement edit student form pre-filled from existing data"
```
