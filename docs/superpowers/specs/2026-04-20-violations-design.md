# Violations Design

## Goal

Add a Violations tab to the Student Detail page that lets admins record student violations, edit their descriptions, and append timestamped notes to each violation after it is created. Violations are permanent records — no delete endpoint exists.

## Architecture

### Data model

**Existing `violation` table** (no migration change needed):
- `id`, `studentId` (FK→users.id — the student's user account), `title`, `violationDate` (date), `description` (text, nullable), `timestamps`

**New `violation_notes` table** (migration required):
- `id`
- `violationId` (FK→violation.id, cascadeOnDelete)
- `note` (text, required)
- `addedBy` (FK→users.id)
- `timestamps`

Note: `violation.studentId` references `users.id`, not `student.id`. Violations are queried using the student's `userId` field (`student.userId`).

---

### Backend changes

#### Fix `Violation` model (`server/app/Models/Violation.php`)

- Keep `$table = 'violation'`, `$fillable = ['studentId', 'title', 'violationDate', 'description']`
- Replace `user()` belongsTo with `student()` relationship via `User::class` (keep FK as `studentId`)
- Add `notes()` hasMany: `$this->hasMany(ViolationNote::class, 'violationId')`

#### New `ViolationNote` model (`server/app/Models/ViolationNote.php`)

- `$table = 'violation_notes'`
- `$fillable = ['violationId', 'note', 'addedBy']`
- `violation()` belongsTo `Violation::class`, FK `violationId`
- `author()` belongsTo `User::class`, FK `addedBy`

#### New `ViolationController` (`server/app/Http/Controllers/Api/ViolationController.php`)

- `index(int $studentId)` — finds student, gets their `userId`, returns `Violation::where('studentId', $userId)->with(['notes.author'])->orderBy('violationDate', 'desc')->get()`
- `store(int $studentId)` — validates `title` (required), `violationDate` (required, date), `description` (nullable). Creates violation with `studentId = $student->userId`.
- `update(int $id)` — validates `description` (nullable string). Updates description only — title and violationDate are never changed.
- No `destroy` method.

#### New `ViolationNoteController` (`server/app/Http/Controllers/Api/ViolationNoteController.php`)

- `store(int $violationId)` — validates `note` (required, string). Creates note with `addedBy = auth()->id()`.

#### Routes (`server/routes/api.php`)

Add inside `auth:sanctum` group:
```php
// Violations
Route::get('/students/{studentId}/violations', [ViolationController::class, 'index']);
Route::post('/students/{studentId}/violations', [ViolationController::class, 'store']);
Route::put('/violations/{id}', [ViolationController::class, 'update']);

// Violation notes
Route::post('/violations/{violationId}/notes', [ViolationNoteController::class, 'store']);
```

#### New migration (`violation_notes` table)

```php
Schema::create('violation_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('violationId')->constrained('violation')->cascadeOnDelete();
    $table->text('note');
    $table->foreignId('addedBy')->constrained('users');
    $table->timestamps();
});
```

---

### Frontend changes

#### 1. Types (`client/src/types/index.ts`)

```ts
export interface ViolationNote {
  id: number;
  violationId: number;
  note: string;
  addedBy: number;
  addedByName?: string;
  created_at: string;
}

export interface Violation {
  id: number;
  studentId: number;
  title: string;
  violationDate: string;
  description?: string;
  notes?: ViolationNote[];
  created_at: string;
}
```

#### 2. API layer (`client/src/api/violations.ts`)

```ts
getViolations(studentId: number): Promise<Violation[]>
createViolation(studentId: number, payload: { title: string; violationDate: string; description?: string }): Promise<Violation>
updateViolation(id: number, payload: { description: string }): Promise<Violation>
addViolationNote(violationId: number, payload: { note: string }): Promise<ViolationNote>
```

#### 3. Student Detail Page — Violations tab (`client/src/pages/students/StudentDetailPage.tsx`)

Replace the (currently unused) Violations tab content with:

**"Add Violation" button** — top-right of the tab header. Opens a Dialog containing:
- Amber warning banner: *"Violations cannot be deleted once added."*
- Title input (required)
- Date input (required)
- Description textarea (optional)
- Cancel / Add Violation buttons

**Violation cards** — one per violation, newest first. Each card:
- Header: title + date badge
- Body: description text (or italic "No description.")
- Pencil icon → replaces description with a textarea + Save / Cancel; only description is editable; on save calls `PUT /violations/{id}`
- **"Notes (N)"** toggle button → expands/collapses inline notes section

**Expanded notes section** (inline below description):
- Chronological list: note text, author name (`addedByName`), formatted timestamp
- "Add Note" textarea + Submit button at the bottom; on submit calls `POST /violations/{violationId}/notes`
- Both mutations invalidate `['violations', studentId]` on success

**Query:** `useQuery({ queryKey: ['violations', Number(id)], queryFn: () => getViolations(Number(id)), enabled: !!id })`

---

## Data flow — add violation

1. Admin fills dialog (title, date, optional description), acknowledges the no-delete warning
2. `createViolation(studentId, payload)` → `POST /students/{studentId}/violations`
3. On success: invalidate `['violations', studentId]`; close dialog

## Data flow — edit description

1. Admin clicks pencil icon on a violation card
2. Textarea pre-filled with current description
3. On save: `updateViolation(id, { description })` → `PUT /violations/{id}`
4. On success: invalidate `['violations', studentId]`

## Data flow — add note

1. Admin expands a violation, types in the "Add Note" textarea
2. `addViolationNote(violationId, { note })` → `POST /violations/{violationId}/notes`
3. On success: invalidate `['violations', studentId]`; note list re-renders with new entry

## Error handling

- `title` or `violationDate` missing on create: 422 from backend, show inline form error
- `note` empty on add note: disabled Submit button when textarea is empty
- `updateViolation` failure: toast error, revert textarea to original text
