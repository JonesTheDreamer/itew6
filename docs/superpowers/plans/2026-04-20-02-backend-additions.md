# Backend Additions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the missing backend pieces needed by the admin frontend — a SkillController, skillId filtering on students, and enriched show responses for students and faculty that include related data (awards, skills, grades, job history, education).

**Architecture:** Follows the existing Repository → Service → Controller → Resource pattern. SkillController is simple enough to skip the service/repository layers and query the model directly. StudentResource and FacultyResource use conditional loading: list responses stay lean, show responses include eager-loaded relations.

**Tech Stack:** Laravel 11, Eloquent, Sanctum auth middleware

**Prerequisite:** Run `php artisan migrate` to ensure `skills` and `student_skills` tables exist before testing.

> **Note:** Task 6 (skill sync) must be complete before the frontend Add/Edit Student forms work correctly — the forms pass `skillIds` in the payload which the service must handle.

---

### Task 1: Fix StudentController method name mismatch

**Files:**
- Modify: `server/app/Http/Controllers/StudentController.php`

The controller calls `$this->service->createWithEntity()` and `$this->service->updateWithEntity()`, but `StudentService` defines these as `createWithUser()` and `updateWithUser()`. This causes a 500 error when creating or updating students.

- [ ] **Step 1: Fix the method call in store()**

In `server/app/Http/Controllers/StudentController.php`, line that calls `createWithEntity`:
```php
public function store(StudentRequest $request): JsonResponse
{
    $student = $this->service->createWithUser($request->validated());
    return response()->json(['data' => new StudentResource($student), 'message' => 'Student created'], 201);
}
```

- [ ] **Step 2: Fix the method call in update()**
```php
public function update(StudentRequest $request, int $id): JsonResponse
{
    $student = $this->service->updateWithUser($id, $request->validated());
    if (!$student) return response()->json(['message' => 'Not found'], 404);
    return response()->json(['data' => new StudentResource($student), 'message' => 'Student updated']);
}
```

- [ ] **Step 3: Verify**

Start the Laravel server: `php artisan serve` from `server/`.

Send a test request (use any REST client or curl):
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ccs.edu.ph","password":"Admin@12345"}'
```
Expected: `{ "token": "...", "user": {...} }`

- [ ] **Step 4: Commit**
```bash
git add server/app/Http/Controllers/StudentController.php
git commit -m "fix: correct StudentController method names to match StudentService"
```

---

### Task 2: Create SkillController and register the route

**Files:**
- Create: `server/app/Http/Controllers/Api/SkillController.php`
- Modify: `server/routes/api.php`

- [ ] **Step 1: Create SkillController**

Create `server/app/Http/Controllers/Api/SkillController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::orderBy('isAcademic', 'desc')->orderBy('name')->get();
        return response()->json(['data' => $skills, 'message' => 'success']);
    }
}
```

- [ ] **Step 2: Register the route**

In `server/routes/api.php`, inside the `auth:sanctum` middleware group, add after the existing student routes:
```php
// Skills
Route::get('/skills', [SkillController::class, 'index']);
```

Also add the import at the top of the file:
```php
use App\Http\Controllers\Api\SkillController;
```

- [ ] **Step 3: Verify**

With the Laravel server running and a valid token:
```bash
curl http://localhost:8000/api/skills \
  -H "Authorization: Bearer <your-token>"
```
Expected: JSON array of skills with `id`, `name`, `isAcademic`.

- [ ] **Step 4: Commit**
```bash
git add server/app/Http/Controllers/Api/SkillController.php server/routes/api.php
git commit -m "feat: add SkillController with GET /skills endpoint"
```

---

### Task 3: Extend StudentRepository to filter by skillId

**Files:**
- Modify: `server/app/Repositories/StudentRepository.php`
- Modify: `server/app/Service/StudentService.php`

- [ ] **Step 1: Add getBySkill method to StudentRepository**

In `server/app/Repositories/StudentRepository.php`, add after the `getByStatus` method:
```php
public function getBySkill(int $skillId): Collection
{
    return Student::with(['user', 'program'])
        ->whereHas('skills', fn($q) => $q->where('skills.id', $skillId))
        ->get();
}

public function getByProgramAndSkill(int $programId, int $skillId): Collection
{
    return Student::with(['user', 'program'])
        ->where('programId', $programId)
        ->whereHas('skills', fn($q) => $q->where('skills.id', $skillId))
        ->get();
}
```

- [ ] **Step 2: Add skillId filter to StudentService::getAll()**

In `server/app/Service/StudentService.php`, update `getAll()`:
```php
public function getAll(array $filters = []): mixed
{
    if (!empty($filters['programId']) && !empty($filters['skillId'])) {
        return $this->studentRepo->getByProgramAndSkill(
            (int) $filters['programId'],
            (int) $filters['skillId']
        );
    }
    if (!empty($filters['skillId'])) {
        return $this->studentRepo->getBySkill((int) $filters['skillId']);
    }
    if (!empty($filters['programId'])) {
        return $this->studentRepo->getByProgram((int) $filters['programId']);
    }
    if (!empty($filters['status'])) {
        return $this->studentRepo->getByStatus($filters['status']);
    }
    return $this->studentRepo->getAll();
}
```

Also update `StudentController::index()` to pass `skillId` through:
```php
public function index(Request $request): JsonResponse
{
    $students = $this->service->getAll($request->only(['programId', 'status', 'skillId']));
    return response()->json(['data' => StudentResource::collection($students), 'message' => 'success']);
}
```

- [ ] **Step 3: Verify**

```bash
# Get skill ID 1's students
curl "http://localhost:8000/api/students?skillId=1" \
  -H "Authorization: Bearer <your-token>"
```
Expected: array of students who have that skill.

- [ ] **Step 4: Commit**
```bash
git add server/app/Repositories/StudentRepository.php server/app/Service/StudentService.php server/app/Http/Controllers/StudentController.php
git commit -m "feat: add skillId and programId+skillId filters to student list"
```

---

### Task 4: Enrich StudentResource show response with relations

**Files:**
- Modify: `server/app/Http/Resources/StudentResource.php`
- Modify: `server/app/Repositories/StudentRepository.php`

The list endpoint returns flat fields only. The show endpoint (single student) should also return skills, grades, awards, and extracurriculars.

**Note on awards:** The `Award` model uses `userId`, so awards are fetched via the student's user relationship. The `User` model needs an `awards()` relationship — check `server/app/Models/User.php`. If it doesn't have one, add it.

- [ ] **Step 1: Add awards() to User model if missing**

Open `server/app/Models/User.php`. Check if `awards()` method exists. If not, add:
```php
public function awards()
{
    return $this->hasMany(\App\Models\Award::class, 'userId');
}

public function eduBackground()
{
    return $this->hasMany(\App\Models\EducationalBackground::class, 'userId');
}
```

- [ ] **Step 2: Update StudentRepository::getById() to eager-load relations**

In `server/app/Repositories/StudentRepository.php`, replace `getById`:
```php
public function getById(int $id): ?Student
{
    return Student::with([
        'user',
        'user.awards',
        'program',
        'grades',
        'extraCurricular',
        'skills',
    ])->find($id);
}
```

- [ ] **Step 3: Update StudentResource to include relations for the show response**

Replace `server/app/Http/Resources/StudentResource.php`:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $isSingle = $request->route('id') !== null;

        $base = [
            'id' => $this->id,
            'studentId' => $this->studentId,
            'userId' => $this->userId,
            'firstName' => $user?->firstName,
            'lastName' => $user?->lastName,
            'middleName' => $user?->middleName,
            'email' => $user?->email,
            'mobileNumber' => $user?->mobileNumber,
            'age' => $user?->age,
            'birthDate' => $user?->birthDate,
            'birthProvince' => $user?->birthProvince,
            'city' => $user?->city,
            'province' => $user?->province,
            'programId' => $this->programId,
            'programName' => $this->program?->name,
            'yearLevel' => $this->yearLevel,
            'unitsTaken' => $this->unitsTaken,
            'unitsLeft' => $this->unitsLeft,
            'dateEnrolled' => $this->dateEnrolled,
            'dateGraduated' => $this->dateGraduated,
            'dateDropped' => $this->dateDropped,
            'gpa' => $this->gpa,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($isSingle) {
            $base['awards'] = $user?->awards ?? [];
            $base['extraCurriculars'] = $this->extraCurricular ?? [];
            $base['skills'] = $this->skills ?? [];
            $base['grades'] = $this->grades ?? [];
        }

        return $base;
    }
}
```

- [ ] **Step 4: Verify**

```bash
curl http://localhost:8000/api/students/1 \
  -H "Authorization: Bearer <your-token>"
```
Expected: student object includes `awards`, `extraCurriculars`, `skills`, `grades` arrays.

- [ ] **Step 5: Commit**
```bash
git add server/app/Http/Resources/StudentResource.php server/app/Repositories/StudentRepository.php server/app/Models/User.php
git commit -m "feat: enrich StudentResource show response with relations"
```

---

### Task 5: Enrich FacultyResource show response with relations

**Files:**
- Modify: `server/app/Http/Resources/FacultyResource.php`
- Modify: `server/app/Repositories/FacultyRepository.php`

- [ ] **Step 1: Update FacultyRepository::getById() to eager-load relations**

Open `server/app/Repositories/FacultyRepository.php`. Update `getById`:
```php
public function getById(int $id): ?Faculty
{
    return Faculty::with([
        'user',
        'user.awards',
        'user.eduBackground',
        'jobHistory',
    ])->find($id);
}
```

- [ ] **Step 2: Replace FacultyResource**

Replace `server/app/Http/Resources/FacultyResource.php`:
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $isSingle = $request->route('id') !== null;

        $base = [
            'id' => $this->id,
            'userId' => $this->userId,
            'firstName' => $user?->firstName,
            'lastName' => $user?->lastName,
            'middleName' => $user?->middleName,
            'email' => $user?->email,
            'mobileNumber' => $user?->mobileNumber,
            'age' => $user?->age,
            'birthDate' => $user?->birthDate,
            'city' => $user?->city,
            'province' => $user?->province,
            'position' => $this->position,
            'employmentDate' => $this->employmentDate,
            'employmentType' => $this->employmentType,
            'monthlyIncome' => $this->monthlyIncome,
            'department' => $this->department,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($isSingle) {
            $base['awards'] = $user?->awards ?? [];
            $base['eduBackground'] = $user?->eduBackground ?? [];
            $base['jobHistory'] = $this->jobHistory ?? [];
        }

        return $base;
    }
}
```

- [ ] **Step 3: Verify**

```bash
curl http://localhost:8000/api/faculty/1 \
  -H "Authorization: Bearer <your-token>"
```
Expected: faculty object includes `awards`, `eduBackground`, `jobHistory` arrays.

- [ ] **Step 4: Commit**
```bash
git add server/app/Http/Resources/FacultyResource.php server/app/Repositories/FacultyRepository.php
git commit -m "feat: enrich FacultyResource show response with job history, education, awards"
```

---

### Task 6: Sync student skills on create and update

**Files:**
- Modify: `server/app/Service/StudentService.php`

The frontend Add/Edit Student forms send `skillIds` (array of integer skill IDs) in the payload. The service must sync the `student_skills` pivot table when creating or updating a student.

- [ ] **Step 1: Add skill sync to createWithUser()**

In `server/app/Service/StudentService.php`, update `createWithUser()` — add skill sync inside the DB transaction after creating the student record:

```php
public function createWithUser(array $data): Student
{
    return DB::transaction(function () use ($data) {
        $user = $this->userRepo->create([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'middleName' => $data['middleName'] ?? null,
            'age' => $data['age'] ?? null,
            'birthDate' => $data['birthDate'] ?? null,
            'birthProvince' => $data['birthProvince'] ?? null,
            'mobileNumber' => $data['mobileNumber'] ?? null,
            'email' => $data['email'],
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
        ]);

        $studentId = $data['studentId'] ?? ('CSC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT));

        $student = $this->studentRepo->create([
            'userId' => $user->id,
            'studentId' => $studentId,
            'programId' => $data['programId'],
            'yearLevel' => $data['yearLevel'] ?? 1,
            'unitsTaken' => $data['unitsTaken'] ?? 0,
            'unitsLeft' => $data['unitsLeft'] ?? 0,
            'dateEnrolled' => $data['dateEnrolled'] ?? now()->toDateString(),
            'dateGraduated' => $data['dateGraduated'] ?? null,
            'dateDropped' => $data['dateDropped'] ?? null,
            'gpa' => $data['gpa'] ?? null,
            'status' => $data['status'] ?? 'Active',
        ]);

        if (!empty($data['skillIds']) && is_array($data['skillIds'])) {
            $student->skills()->sync($data['skillIds']);
        }

        return $this->studentRepo->getById($student->id);
    });
}
```

- [ ] **Step 2: Add skill sync to updateWithUser()**

In `server/app/Service/StudentService.php`, update `updateWithUser()` — add skill sync after the student record update:

```php
public function updateWithUser(int $id, array $data): ?Student
{
    return DB::transaction(function () use ($id, $data) {
        $student = $this->studentRepo->getById($id);
        if (!$student)
            return null;

        $this->userRepo->update($student->userId, [
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'middleName' => $data['middleName'] ?? null,
            'age' => $data['age'] ?? null,
            'birthDate' => $data['birthDate'] ?? null,
            'birthProvince' => $data['birthProvince'] ?? null,
            'mobileNumber' => $data['mobileNumber'] ?? null,
            'email' => $data['email'],
            'city' => $data['city'] ?? null,
            'province' => $data['province'] ?? null,
        ]);

        $this->studentRepo->update($id, [
            'programId' => $data['programId'],
            'yearLevel' => $data['yearLevel'] ?? $student->yearLevel,
            'unitsTaken' => $data['unitsTaken'] ?? $student->unitsTaken,
            'unitsLeft' => $data['unitsLeft'] ?? $student->unitsLeft,
            'dateEnrolled' => $data['dateEnrolled'] ?? $student->dateEnrolled,
            'dateGraduated' => $data['dateGraduated'] ?? $student->dateGraduated,
            'dateDropped' => $data['dateDropped'] ?? $student->dateDropped,
            'gpa' => $data['gpa'] ?? $student->gpa,
            'status' => $data['status'] ?? $student->status,
        ]);

        if (isset($data['skillIds']) && is_array($data['skillIds'])) {
            $student->skills()->sync($data['skillIds']);
        }

        return $this->studentRepo->getById($id);
    });
}
```

- [ ] **Step 3: Verify skill sync on create**

Create a new student via the frontend Add Student form, selecting 2–3 skills. After redirect, open the student detail page and check the Skills tab. Expected: the selected skills appear as badges.

- [ ] **Step 4: Verify skill sync on update**

Open the Edit Student form for an existing student. Remove one skill and add a different one. Save. Open the detail page Skills tab. Expected: updated skill list matches what was saved.

- [ ] **Step 5: Commit**
```bash
git add server/app/Service/StudentService.php
git commit -m "feat: sync student skills pivot on create and update"
```
