# Skills Management Design

## Goal

Build a dedicated Skills page (`/admin/skills`) for managing the global skill catalog and querying students by skill, fix the broken `Skill` model, and make the Student Detail Skills tab interactive (add/remove skills without entering the edit form).

## Architecture

### Data model (existing, no migration needed)

- `skills` table: `id`, `name` (unique), `isAcademic` (boolean), `timestamps`
- `student_skills` pivot: `id`, `studentId` (FK→student), `skillId` (FK→skills), unique(studentId, skillId)
- `Student` model already has `skills()` belongsToMany via `student_skills`

### Backend changes

**Fix `Skill` model** (`server/app/Models/Skill.php`)

The model currently points to the old `skill` table (a per-student table from an earlier design). Correct it:
- `$table = 'skills'`
- `$fillable = ['name', 'isAcademic']`
- Remove `studentId` from fillable and the `belongsTo(Student::class)` relationship
- Add `students()` belongsToMany: `$this->belongsToMany(Student::class, 'student_skills', 'skillId', 'studentId')`

**Expand `SkillController`** (`server/app/Http/Controllers/Api/SkillController.php`)

- `index()` — already exists. Add `withCount('students')` so each skill includes `students_count`. Order by `isAcademic desc`, then `name`.
- `store()` — validate `name` (required, unique:skills), `isAcademic` (boolean, default false). Return created skill.
- `update(int $id)` — validate same rules (unique except self). Return updated skill.
- `destroy(int $id)` — delete skill. Pivot rows cascade automatically. Return 204.

**Routes** (`server/routes/api.php`)

Add inside the `auth:sanctum` group:
```
Route::post('/skills', [SkillController::class, 'store']);
Route::put('/skills/{id}', [SkillController::class, 'update']);
Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
```

**Student skill assignment** — no new endpoint. `PUT /students/{id}` already syncs `skillIds` via `StudentService::updateWithUser()`. The frontend re-submits all required student fields with only `skillIds` changed, using data already in the React Query cache.

---

### Frontend changes

#### 1. Types (`client/src/types/index.ts`)

Add `studentCount?: number` to `Skill` interface.

#### 2. API layer (`client/src/api/skills.ts`)

Add:
- `createSkill(payload: { name: string; isAcademic: boolean }): Promise<Skill>`
- `updateSkill(id: number, payload: { name: string; isAcademic: boolean }): Promise<Skill>`
- `deleteSkill(id: number): Promise<void>`

#### 3. Skills page (`client/src/pages/SkillsPage.tsx`)

Split-panel layout (`lg:grid-cols-[320px_1fr]`):

**Left panel — Skill Catalog:**
- "Add Skill" button (primary, top of panel)
- Scrollable list of skill rows. Each row:
  - Skill name + Academic/Soft badge + student count pill
  - Pencil (edit) and Trash (delete) icon buttons
  - Clicking the row body selects it (highlighted with `bg-primary/10 border-l-2 border-primary`)
- Delete confirmation Dialog (same pattern as student/faculty delete)

**Right panel — Students with Skill:**
- Empty state when nothing selected: "Select a skill to see assigned students"
- When selected: skill name as header, student count label, table of students (Student ID, Name, Program, Year, Status with StatusBadge)
- Data from `GET /students?skillId=X` — fetches when selected skill changes, `enabled: !!selectedSkillId`
- Loading state: Skeleton rows

**Add/Edit Dialog:**
- Single `Dialog` for both add and edit (title changes based on mode)
- `name` text input (required)
- `isAcademic` checkbox labeled "Academic skill"
- On submit: calls `createSkill` or `updateSkill`, invalidates `['skills']` query

#### 4. Student Detail Skills tab (`client/src/pages/students/StudentDetailPage.tsx`)

Replace the read-only badge list with an interactive panel:

- Assigned skills: `SkillBadge` for each, with an **×** button (`aria-label="Remove {name}"`)
- Clicking × calls `updateStudent(id, { ...currentStudentFields, skillIds: existingIds.filter(x => x !== removed) })`
- "Add Skill" button opens a `Popover` + `Command` (shadcn combobox) showing skills from `['skills']` query filtered to exclude already-assigned ones. Searchable by name.
- Selecting a skill from the combobox calls `updateStudent(id, { ...currentStudentFields, skillIds: [...existingIds, newId] })`
- Both mutations invalidate `['students', studentId]` on success
- Current student data (for the required fields) comes from the React Query cache — the same `['students', Number(id)]` query already loaded by the detail page

#### 5. Router + Sidebar

- Add route `/admin/skills` → `<SkillsPage />` in `client/src/main.tsx`
- Add "Skills" nav item to `AppSidebar.tsx` (under a "Skills" group or alongside Reports)

---

## Data flow — skill removal from detail page

1. User clicks × on a skill badge in the Skills tab
2. Component reads current student from React Query cache (`['students', Number(id)]`)
3. Builds updated `skillIds` array: current `student.skills.map(s => s.id)` minus removed id
4. Calls `updateStudent(id, { firstName, lastName, email, programId, yearLevel, status, skillIds })`
5. On success: invalidates `['students', Number(id)]` — detail page re-renders with updated skills

## Data flow — skill addition from detail page

Same as above but appends the new skill id to the array.

## Error handling

- Skill name uniqueness violation (422): show inline error in the Add/Edit dialog
- Delete with students assigned: allowed (cascade removes pivot rows, students keep their other skills)
- `updateStudent` failure on skill toggle: show toast/error, leave existing state intact
