# Skills Management Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a dedicated Skills page with a split-panel catalog + student query, fix the broken Skill model, add skill CRUD API, and make the Student Detail Skills tab interactive (add/remove without entering the edit form).

**Architecture:** Backend fixes the Skill model (wrong table reference) and adds store/update/destroy to SkillController. Frontend adds a new `/admin/skills` route with a split-panel page (left: skill list with CRUD dialogs; right: students for selected skill via existing `GET /students?skillId=X`). Student Detail page Skills tab gains inline add/remove backed by the existing `PUT /students/{id}` endpoint with `skillIds`.

**Tech Stack:** Laravel 11 (PHP), React 19, TypeScript, React Query v5, shadcn/ui, Tailwind CSS v4

**Prerequisites:** All previous plans complete. The `skills` and `student_skills` tables exist in the database.

---

### Task 1: Fix Skill model and expand SkillController with CRUD routes

**Files:**
- Modify: `server/app/Models/Skill.php`
- Modify: `server/app/Http/Controllers/Api/SkillController.php`
- Modify: `server/routes/api.php`

- [ ] **Step 1: Fix the Skill model**

The current model points to the old `skill` table (a per-student table). Replace `server/app/Models/Skill.php` entirely:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills';
    protected $fillable = ['name', 'isAcademic'];
    protected $casts = ['isAcademic' => 'boolean'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_skills', 'skillId', 'studentId');
    }
}
```

- [ ] **Step 2: Expand SkillController**

Replace `server/app/Http/Controllers/Api/SkillController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::withCount('students')
            ->orderBy('isAcademic', 'desc')
            ->orderBy('name')
            ->get();
        return response()->json(['data' => $skills, 'message' => 'success']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:skills,name'],
            'isAcademic' => ['boolean'],
        ]);

        $skill = Skill::create([
            'name'       => $validated['name'],
            'isAcademic' => $validated['isAcademic'] ?? false,
        ]);

        $skill->loadCount('students');
        return response()->json(['data' => $skill, 'message' => 'success'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique('skills', 'name')->ignore($id)],
            'isAcademic' => ['boolean'],
        ]);

        $skill->update($validated);
        $skill->loadCount('students');
        return response()->json(['data' => $skill, 'message' => 'success']);
    }

    public function destroy(int $id): JsonResponse
    {
        Skill::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
```

- [ ] **Step 3: Add routes**

In `server/routes/api.php`, find the existing `Route::get('/skills', ...)` line and replace it with:

```php
    // Skills
    Route::get('/skills', [SkillController::class, 'index']);
    Route::post('/skills', [SkillController::class, 'store']);
    Route::put('/skills/{id}', [SkillController::class, 'update']);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
```

- [ ] **Step 4: Verify with curl**

With the Laravel server running (`php artisan serve` in `server/`):

```bash
# Should return skills list with students_count field on each item
curl -s -H "Authorization: Bearer <token>" http://localhost:8000/api/skills | python -m json.tool
```

Expected: JSON with `data` array where each skill has `id`, `name`, `isAcademic`, `students_count`.

- [ ] **Step 5: Commit**

```bash
git add server/app/Models/Skill.php server/app/Http/Controllers/Api/SkillController.php server/routes/api.php
git commit -m "feat: fix Skill model table reference and add skill CRUD endpoints"
```

---

### Task 2: Update frontend types and API layer

**Files:**
- Modify: `client/src/types/index.ts`
- Modify: `client/src/api/skills.ts`

- [ ] **Step 1: Add `students_count` to Skill type**

In `client/src/types/index.ts`, find the `Skill` interface and add `students_count`:

```ts
export interface Skill {
  id: number;
  name: string;
  isAcademic: boolean;
  students_count?: number;
}
```

- [ ] **Step 2: Add createSkill, updateSkill, deleteSkill to API layer**

Replace `client/src/api/skills.ts`:

```ts
import api from '@/lib/axios';
import type { ApiItem, ApiList, Skill } from '@/types';

export const getSkills = async (): Promise<Skill[]> => {
  const { data } = await api.get<ApiList<Skill>>('/skills');
  return data.data;
};

export const createSkill = async (payload: { name: string; isAcademic: boolean }): Promise<Skill> => {
  const { data } = await api.post<ApiItem<Skill>>('/skills', payload);
  return data.data;
};

export const updateSkill = async (id: number, payload: { name: string; isAcademic: boolean }): Promise<Skill> => {
  const { data } = await api.put<ApiItem<Skill>>(`/skills/${id}`, payload);
  return data.data;
};

export const deleteSkill = async (id: number): Promise<void> => {
  await api.delete(`/skills/${id}`);
};
```

- [ ] **Step 3: Verify TypeScript compiles**

```bash
cd client && npx tsc --noEmit
```

Expected: No errors.

- [ ] **Step 4: Commit**

```bash
git add client/src/types/index.ts client/src/api/skills.ts
git commit -m "feat: add students_count to Skill type and skill CRUD API functions"
```

---

### Task 3: Build the Skills page

**Files:**
- Create: `client/src/pages/SkillsPage.tsx`

- [ ] **Step 1: Create SkillsPage**

Create `client/src/pages/SkillsPage.tsx`:

```tsx
import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Plus, Pencil, Trash2, BookOpen, Users } from 'lucide-react';
import { getSkills, createSkill, updateSkill, deleteSkill } from '@/api/skills';
import { getStudents } from '@/api/students';
import type { Skill } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Skeleton } from '@/components/ui/skeleton';
import StatusBadge from '@/components/shared/StatusBadge';

export default function SkillsPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const [selectedSkillId, setSelectedSkillId] = useState<number | null>(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [editTarget, setEditTarget] = useState<Skill | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Skill | null>(null);
  const [formName, setFormName] = useState('');
  const [formIsAcademic, setFormIsAcademic] = useState(false);
  const [formError, setFormError] = useState('');

  const { data: skills = [], isLoading: skillsLoading } = useQuery({
    queryKey: ['skills'],
    queryFn: getSkills,
  });

  const { data: students = [], isLoading: studentsLoading } = useQuery({
    queryKey: ['students', { skillId: selectedSkillId }],
    queryFn: () => getStudents({ skillId: selectedSkillId! }),
    enabled: !!selectedSkillId,
  });

  const createMutation = useMutation({
    mutationFn: createSkill,
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['skills'] }); closeDialog(); },
    onError: () => setFormError('Failed to save. Name may already exist.'),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: { name: string; isAcademic: boolean } }) =>
      updateSkill(id, payload),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['skills'] }); closeDialog(); },
    onError: () => setFormError('Failed to save. Name may already exist.'),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteSkill(id),
    onSuccess: (_, id) => {
      queryClient.invalidateQueries({ queryKey: ['skills'] });
      if (selectedSkillId === id) setSelectedSkillId(null);
      setDeleteTarget(null);
    },
  });

  const openAdd = () => {
    setEditTarget(null);
    setFormName('');
    setFormIsAcademic(false);
    setFormError('');
    setDialogOpen(true);
  };

  const openEdit = (skill: Skill) => {
    setEditTarget(skill);
    setFormName(skill.name);
    setFormIsAcademic(skill.isAcademic);
    setFormError('');
    setDialogOpen(true);
  };

  const closeDialog = () => {
    setDialogOpen(false);
    setEditTarget(null);
    setFormError('');
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setFormError('');
    const payload = { name: formName.trim(), isAcademic: formIsAcademic };
    if (editTarget) {
      updateMutation.mutate({ id: editTarget.id, payload });
    } else {
      createMutation.mutate(payload);
    }
  };

  const selectedSkill = skills.find(s => s.id === selectedSkillId);
  const isSaving = createMutation.isPending || updateMutation.isPending;

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-bold">Skills</h1>
        <p className="text-sm text-muted-foreground mt-1">Manage the skill catalog and view students by skill.</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4 items-start">
        {/* Left panel — Skill Catalog */}
        <Card>
          <CardHeader className="pb-3">
            <div className="flex items-center justify-between">
              <CardTitle className="text-base">Skill Catalog</CardTitle>
              <Button size="sm" onClick={openAdd}>
                <Plus className="h-4 w-4 mr-1" /> Add Skill
              </Button>
            </div>
          </CardHeader>
          <CardContent className="p-0">
            {skillsLoading ? (
              <div className="space-y-1 p-3">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Skeleton key={i} className="h-10 w-full" />
                ))}
              </div>
            ) : skills.length === 0 ? (
              <p className="text-sm text-muted-foreground p-4 text-center">No skills yet. Add one above.</p>
            ) : (
              <div className="divide-y">
                {skills.map(skill => (
                  <div
                    key={skill.id}
                    className={`flex items-center gap-2 px-3 py-2.5 cursor-pointer hover:bg-muted/50 transition-colors ${
                      selectedSkillId === skill.id ? 'bg-primary/10 border-l-2 border-primary' : ''
                    }`}
                    onClick={() => setSelectedSkillId(skill.id)}
                  >
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium truncate">{skill.name}</p>
                      <div className="flex items-center gap-1.5 mt-0.5">
                        <Badge
                          variant="secondary"
                          className={`text-[10px] px-1 py-0 ${skill.isAcademic ? 'bg-primary/10 text-primary' : ''}`}
                        >
                          {skill.isAcademic ? 'Academic' : 'Soft'}
                        </Badge>
                        <span className="text-xs text-muted-foreground flex items-center gap-0.5">
                          <Users className="h-3 w-3" />
                          {skill.students_count ?? 0}
                        </span>
                      </div>
                    </div>
                    <div className="flex gap-0.5 shrink-0" onClick={e => e.stopPropagation()}>
                      <Button size="icon" variant="ghost" className="h-7 w-7" onClick={() => openEdit(skill)}>
                        <Pencil className="h-3.5 w-3.5" />
                      </Button>
                      <Button
                        size="icon" variant="ghost"
                        className="h-7 w-7 text-destructive hover:text-destructive"
                        onClick={() => setDeleteTarget(skill)}
                      >
                        <Trash2 className="h-3.5 w-3.5" />
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Right panel — Students with selected skill */}
        <Card className="min-h-64">
          {!selectedSkill ? (
            <CardContent className="flex flex-col items-center justify-center py-16 text-center">
              <BookOpen className="h-8 w-8 text-muted-foreground mb-3" />
              <p className="text-sm text-muted-foreground">Select a skill to see assigned students.</p>
            </CardContent>
          ) : (
            <>
              <CardHeader className="pb-3">
                <div className="flex items-center gap-2">
                  <CardTitle className="text-base">{selectedSkill.name}</CardTitle>
                  <Badge
                    variant="secondary"
                    className={selectedSkill.isAcademic ? 'bg-primary/10 text-primary' : ''}
                  >
                    {selectedSkill.isAcademic ? 'Academic' : 'Soft'}
                  </Badge>
                </div>
                {!studentsLoading && (
                  <p className="text-xs text-muted-foreground">
                    {students.length} student{students.length !== 1 ? 's' : ''}
                  </p>
                )}
              </CardHeader>
              <CardContent className="p-0">
                {studentsLoading ? (
                  <div className="space-y-1 p-3">
                    {Array.from({ length: 3 }).map((_, i) => (
                      <Skeleton key={i} className="h-10 w-full" />
                    ))}
                  </div>
                ) : students.length === 0 ? (
                  <p className="text-sm text-muted-foreground p-4 text-center">No students have this skill yet.</p>
                ) : (
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Student ID</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Program</TableHead>
                        <TableHead>Year</TableHead>
                        <TableHead>Status</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {students.map(s => (
                        <TableRow
                          key={s.id}
                          className="cursor-pointer hover:bg-muted/50"
                          onClick={() => navigate(`/admin/students/${s.id}`)}
                        >
                          <TableCell className="font-mono text-sm">{s.studentId}</TableCell>
                          <TableCell className="font-medium">{s.firstName} {s.lastName}</TableCell>
                          <TableCell className="text-sm text-muted-foreground">{s.programName}</TableCell>
                          <TableCell>{s.yearLevel}</TableCell>
                          <TableCell><StatusBadge status={s.status} /></TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                )}
              </CardContent>
            </>
          )}
        </Card>
      </div>

      {/* Add / Edit Dialog */}
      <Dialog open={dialogOpen} onOpenChange={open => { if (!open) closeDialog(); }}>
        <DialogContent className="max-w-sm">
          <DialogHeader>
            <DialogTitle>{editTarget ? 'Edit Skill' : 'Add Skill'}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit} className="space-y-4 pt-1">
            <div className="space-y-1">
              <Label htmlFor="skillName">Name <span className="text-destructive">*</span></Label>
              <Input
                id="skillName"
                value={formName}
                onChange={e => setFormName(e.target.value)}
                placeholder="e.g. Python Programming"
                required
                autoFocus
              />
            </div>
            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="isAcademic"
                checked={formIsAcademic}
                onChange={e => setFormIsAcademic(e.target.checked)}
                className="h-4 w-4 accent-primary"
              />
              <Label htmlFor="isAcademic" className="cursor-pointer font-normal">Academic skill</Label>
            </div>
            {formError && <p className="text-sm text-destructive">{formError}</p>}
            <DialogFooter>
              <Button type="button" variant="outline" onClick={closeDialog}>Cancel</Button>
              <Button type="submit" disabled={isSaving}>
                {isSaving ? 'Saving…' : editTarget ? 'Save Changes' : 'Add Skill'}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Dialog */}
      <Dialog open={!!deleteTarget} onOpenChange={() => setDeleteTarget(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Skill</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to delete <strong>{deleteTarget?.name}</strong>? It will be removed from all assigned students.
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteTarget(null)}>Cancel</Button>
            <Button
              variant="destructive"
              onClick={() => deleteTarget && deleteMutation.mutate(deleteTarget.id)}
              disabled={deleteMutation.isPending}
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

- [ ] **Step 2: Verify TypeScript compiles**

```bash
cd client && npx tsc --noEmit
```

Expected: No errors.

- [ ] **Step 3: Commit**

```bash
git add client/src/pages/SkillsPage.tsx
git commit -m "feat: add split-panel Skills page with catalog CRUD and student query"
```

---

### Task 4: Make StudentDetailPage Skills tab interactive

**Files:**
- Modify: `client/src/pages/students/StudentDetailPage.tsx`

The Skills tab currently shows read-only `SkillBadge` components. Replace it with an interactive panel: assigned skills show an × remove button, and a Select lets the admin add unassigned skills. Both actions call `PUT /students/{id}` with the updated `skillIds` array, using the student data already in the React Query cache.

- [ ] **Step 1: Update StudentDetailPage**

Replace `client/src/pages/students/StudentDetailPage.tsx`:

```tsx
import { useParams, useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, Pencil, X } from 'lucide-react';
import { getStudent, updateStudent } from '@/api/students';
import { getSkills } from '@/api/skills';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Separator } from '@/components/ui/separator';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Skeleton } from '@/components/ui/skeleton';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import StatusBadge from '@/components/shared/StatusBadge';
import SkillBadge from '@/components/shared/SkillBadge';

export default function StudentDetailPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: student, isLoading } = useQuery({
    queryKey: ['students', Number(id)],
    queryFn: () => getStudent(Number(id)),
    enabled: !!id,
  });

  const { data: allSkills = [] } = useQuery({
    queryKey: ['skills'],
    queryFn: getSkills,
  });

  const skillMutation = useMutation({
    mutationFn: (skillIds: number[]) => updateStudent(Number(id), {
      firstName: student!.firstName,
      lastName: student!.lastName,
      email: student!.email,
      programId: student!.programId,
      yearLevel: student!.yearLevel,
      status: student!.status,
      skillIds,
    }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['students', Number(id)] });
    },
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

  const initials = [student.firstName?.[0], student.lastName?.[0]].filter(Boolean).join('').toUpperCase() || '?';

  const assignedIds = student.skills?.map(s => s.id) ?? [];
  const availableSkills = allSkills.filter(s => !assignedIds.includes(s.id));

  const removeSkill = (skillId: number) => {
    skillMutation.mutate(assignedIds.filter(sid => sid !== skillId));
  };

  const addSkill = (value: string) => {
    skillMutation.mutate([...assignedIds, Number(value)]);
  };

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
                    ['GPA', student.gpa != null ? Number(student.gpa).toFixed(2) : '—'],
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
                            <td className="py-1.5 font-mono">{Number(g.grade).toFixed(2)}</td>
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
                <CardContent className="space-y-3">
                  {student.skills && student.skills.length > 0 ? (
                    <div className="flex flex-wrap gap-2">
                      {student.skills.map(skill => (
                        <div key={skill.id} className="flex items-center gap-1">
                          <SkillBadge skill={skill} />
                          <button
                            type="button"
                            aria-label={`Remove ${skill.name}`}
                            onClick={() => removeSkill(skill.id)}
                            disabled={skillMutation.isPending}
                            className="rounded-full hover:bg-muted p-0.5 text-muted-foreground hover:text-foreground disabled:opacity-50"
                          >
                            <X className="h-3 w-3" />
                          </button>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-sm text-muted-foreground">No skills recorded.</p>
                  )}

                  {availableSkills.length > 0 && (
                    <div className="pt-1">
                      <Select
                        key={assignedIds.join(',')}
                        onValueChange={addSkill}
                        disabled={skillMutation.isPending}
                      >
                        <SelectTrigger className="w-56">
                          <SelectValue placeholder="Add a skill…" />
                        </SelectTrigger>
                        <SelectContent>
                          {availableSkills.map(s => (
                            <SelectItem key={s.id} value={String(s.id)}>
                              {s.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
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

- [ ] **Step 2: Verify TypeScript compiles**

```bash
cd client && npx tsc --noEmit
```

Expected: No errors.

- [ ] **Step 3: Commit**

```bash
git add client/src/pages/students/StudentDetailPage.tsx
git commit -m "feat: make student detail Skills tab interactive with add/remove"
```

---

### Task 5: Add route and sidebar link

**Files:**
- Modify: `client/src/main.tsx`
- Modify: `client/src/components/layout/AppSidebar.tsx`

- [ ] **Step 1: Add /admin/skills route**

In `client/src/main.tsx`, add the import and route:

```tsx
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { createBrowserRouter, RouterProvider, Navigate } from 'react-router';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import './index.css';

import AppLayout from '@/components/layout/AppLayout';
import AuthGuard from '@/components/shared/AuthGuard';
import LoginPage from '@/pages/LoginPage';
import StudentsPage from '@/pages/students/StudentsPage';
import StudentDetailPage from '@/pages/students/StudentDetailPage';
import AddStudentPage from '@/pages/students/AddStudentPage';
import EditStudentPage from '@/pages/students/EditStudentPage';
import FacultyPage from '@/pages/faculty/FacultyPage';
import FacultyDetailPage from '@/pages/faculty/FacultyDetailPage';
import AddFacultyPage from '@/pages/faculty/AddFacultyPage';
import EditFacultyPage from '@/pages/faculty/EditFacultyPage';
import QueryPage from '@/pages/QueryPage';
import SkillsPage from '@/pages/SkillsPage';

const queryClient = new QueryClient({
  defaultOptions: { queries: { retry: 1, staleTime: 30_000 } },
});

const router = createBrowserRouter([
  { path: '/login', element: <LoginPage /> },
  {
    path: '/admin',
    element: <AuthGuard><AppLayout /></AuthGuard>,
    children: [
      { index: true, element: <Navigate to="/admin/students" replace /> },
      { path: 'students', element: <StudentsPage /> },
      { path: 'students/add', element: <AddStudentPage /> },
      { path: 'students/:id', element: <StudentDetailPage /> },
      { path: 'students/:id/edit', element: <EditStudentPage /> },
      { path: 'faculty', element: <FacultyPage /> },
      { path: 'faculty/add', element: <AddFacultyPage /> },
      { path: 'faculty/:id', element: <FacultyDetailPage /> },
      { path: 'faculty/:id/edit', element: <EditFacultyPage /> },
      { path: 'query', element: <QueryPage /> },
      { path: 'skills', element: <SkillsPage /> },
    ],
  },
  { path: '*', element: <Navigate to="/login" replace /> },
]);

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <RouterProvider router={router} />
    </QueryClientProvider>
  </StrictMode>
);
```

- [ ] **Step 2: Add Skills link to sidebar**

Replace `client/src/components/layout/AppSidebar.tsx`:

```tsx
import { NavLink, useNavigate } from 'react-router';
import { Users, GraduationCap, Search, LogOut, BookOpen, Layers } from 'lucide-react';
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

const reportsLinks = [
  { to: '/admin/skills', label: 'Skills', icon: Layers },
  { to: '/admin/query', label: 'Skill Query', icon: Search },
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
          <SidebarGroupLabel>Reports</SidebarGroupLabel>
          <SidebarMenu>
            {reportsLinks.map(({ to, label, icon: Icon }) => (
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

- [ ] **Step 3: Build and verify**

```bash
cd client && npm run build
```

Expected: Build succeeds with no TypeScript errors.

- [ ] **Step 4: Commit**

```bash
git add client/src/main.tsx client/src/components/layout/AppSidebar.tsx
git commit -m "feat: add /admin/skills route and Skills sidebar link"
```
