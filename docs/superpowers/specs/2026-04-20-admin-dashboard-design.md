# Admin Dashboard Design — CCS Student Profiling System

**Date:** 2026-04-20  
**Scope:** Midterm — Student & Faculty Profile Modules, Data Management, Query/Filtering

---

## 1. Architecture & Routing

### Auth Flow
- `POST /api/login` returns a Sanctum token stored in `localStorage`
- Axios instance reads token and attaches `Authorization: Bearer <token>` to all requests
- `<AuthGuard>` wraps `/admin` — redirects to `/login` if no token present
- Logout clears token and redirects to `/login`

### Route Tree
```
/login                          public, redirects to /admin/students if authenticated
/admin                          layout (AppLayout: sidebar + outlet), requires auth
  /admin/students               student list with search/filter bar
  /admin/students/add           add student form
  /admin/students/:id           student profile (read-only, tabbed)
  /admin/students/:id/edit      edit student form
  /admin/faculty                faculty list with filters
  /admin/faculty/add            add faculty form
  /admin/faculty/:id            faculty profile (read-only, tabbed)
  /admin/faculty/:id/edit       edit faculty form
  /admin/query                  skill-based query/filtering
```

---

## 2. TypeScript Types

```ts
// Auth
interface LoginCredentials { email: string; password: string }
interface AuthResponse { token: string; user: User }

// Core
interface User {
  id: number; firstName: string; lastName: string; middleName?: string
  email: string; mobileNumber?: string; age?: number; birthDate?: string
  birthProvince?: string; city?: string; province?: string; role: string
}

interface Student {
  id: number; studentId: string; userId: number
  firstName: string; lastName: string; middleName?: string
  email: string; mobileNumber?: string; age?: number
  birthDate?: string; birthProvince?: string; city?: string; province?: string
  programId: number; programName?: string; yearLevel: number
  unitsTaken: number; unitsLeft: number; gpa?: number
  status: 'enrolled' | 'graduated' | 'dropped' | 'inactive'
  dateEnrolled?: string; dateGraduated?: string; dateDropped?: string
  awards?: Award[]; extraCurriculars?: ExtraCurricular[]
  skills?: Skill[]; grades?: Grade[]
}

interface Faculty {
  id: number; facultyId: string; userId: number
  firstName: string; lastName: string; middleName?: string
  email: string; mobileNumber?: string; age?: number
  department?: string; position?: string; rank?: string; status: string
  jobHistory?: JobHistory[]; eduBackground?: EduBackground[]; awards?: Award[]
}

interface Program { id: number; name: string; code: string; collegeId: number }
interface Skill { id: number; name: string; isAcademic: boolean }

interface Award {
  id: number; title: string; description?: string
  dateAwarded?: string; awardingBody?: string
}
interface ExtraCurricular {
  id: number; name: string; role?: string; description?: string
  startDate?: string; endDate?: string
}
interface Grade {
  id: number; studentId: number; courseId: number; courseName?: string
  term: 'preliminary' | 'midterm' | 'finals'; grade: number; academicYear: string
}
interface JobHistory {
  id: number; position: string; institution: string
  startDate: string; endDate?: string; isCurrent: boolean
}
interface EduBackground {
  id: number; degree: string; institution: string
  yearGraduated?: number; honors?: string
}

// API wrappers
interface ApiList<T> { data: T[]; message: string }
interface ApiItem<T> { data: T; message: string }

// Filters
interface StudentFilters { programId?: number; status?: string; skillId?: number }
interface FacultyFilters { department?: string; status?: string }
```

---

## 3. Component Structure

```
client/src/
├── types/index.ts
├── lib/
│   ├── axios.ts                axios instance with auth header
│   └── utils.ts                cn() utility (existing)
├── api/
│   ├── auth.ts                 login, logout
│   ├── students.ts             list, get, create, update, delete
│   ├── faculty.ts              list, get, create, update, delete
│   └── skills.ts               list all skills
├── hooks/
│   ├── useAuth.ts              token state, login/logout mutations
│   ├── useStudents.ts          useQuery/useMutation wrappers
│   └── useFaculty.ts           useQuery/useMutation wrappers
├── components/
│   ├── layout/
│   │   ├── AppLayout.tsx       sidebar + <Outlet />
│   │   └── AppSidebar.tsx      nav sections: Students, Faculty
│   ├── shared/
│   │   ├── AuthGuard.tsx       redirect wrapper
│   │   ├── StatusBadge.tsx     color-coded status pill
│   │   └── SkillBadge.tsx      orange=academic, gray=non-academic
│   └── ui/                     shadcn components
└── pages/
    ├── LoginPage.tsx
    ├── students/
    │   ├── StudentsPage.tsx        list + filter bar
    │   ├── StudentDetailPage.tsx   tabbed profile
    │   ├── AddStudentPage.tsx      form
    │   └── EditStudentPage.tsx     pre-filled form
    ├── faculty/
    │   ├── FacultyPage.tsx         list + filter bar
    │   ├── FacultyDetailPage.tsx   tabbed profile
    │   ├── AddFacultyPage.tsx      form
    │   └── EditFacultyPage.tsx     pre-filled form
    └── QueryPage.tsx               two skill query panels
```

**shadcn components to install:** `table`, `card`, `input`, `select`, `badge`, `dialog`, `form`, `label`, `separator`, `tabs`, `sidebar`, `avatar`, `skeleton`

---

## 4. Page Designs

### Theme
- Primary color: `#F28500` → `oklch(0.70 0.165 55.5)`
- Primary foreground: white
- Update `--primary` and `--primary-foreground` in `index.css`
- Sidebar uses `--sidebar-primary` set to same orange

### Login Page
- Centered card with CCS title
- Email + password inputs, login button (primary orange)
- Error message on failed auth

### Students List (`/admin/students`)
- Filter bar: name search input, Program dropdown, Status dropdown
- Table columns: Student ID | Name | Program | Year Level | GPA | Status | Actions
- Actions: View (→ detail page), Edit (→ edit page), Delete (confirmation dialog)
- "Add Student" button top-right

### Student Detail (`/admin/students/:id`)
- Left card: avatar initials, full name, student ID, email, contact, status badge
- Right tabbed panel:
  - **Academic** — program, year level, GPA, units taken/left, enrollment dates
  - **Grades** — table: Course | Term | Grade | Academic Year
  - **Skills** — skill badges (orange=academic, gray=non-academic)
  - **Activities** — awards list + extracurriculars list
- Edit button top-right

### Add/Edit Student
- Section 1 — Personal Info: firstName, lastName, middleName, email, mobile, birthDate, city, province
- Section 2 — Academic Info: program (dropdown), yearLevel, status, dateEnrolled
- Section 3 — Skills: multi-select from skills list

### Faculty List (`/admin/faculty`)
- Filter bar: name search, Department input, Status dropdown
- Table columns: Faculty ID | Name | Department | Position | Rank | Status | Actions

### Faculty Detail (`/admin/faculty/:id`)
- Left card: avatar initials, name, faculty ID, email, status
- Right tabbed panel:
  - **Info** — rank, department, position, contact
  - **Employment** — job history table: Position | Institution | Start | End | Current
  - **Education** — degree, institution, year, honors
  - **Awards** — awards list
  - **Organizations** — org memberships

### Add/Edit Faculty
- Section 1 — Personal Info: firstName, lastName, email, mobile, birthDate
- Section 2 — Faculty Info: rank, department, position, status

### Query Page (`/admin/query`)
Two side-by-side panels:
1. **Students by Skill** — skill dropdown → table of matching students (Name, Program, Year, GPA)
2. **Students by Program + Skill** — program + skill dropdowns → filtered results table

---

## 5. Backend Additions

### New: `GET /skills`
- New `SkillController` with `index()` method
- Returns all skills: `{ id, name, isAcademic }`
- Add to `api.php` inside the auth middleware group

### Extended: `GET /students?skillId=X`
- Extend `StudentService::getAll()` filter to join `student_skills` when `skillId` is present
- No route change needed

### Extended: `StudentResource` (show only)
- `GET /students/:id` must eager-load and return: `awards`, `extraCurriculars`, `skills`, `grades` with course name
- The list endpoint (`GET /students`) returns flat fields only (no relations) for performance

### Extended: `FacultyResource` (show only)
- `GET /faculty/:id` must eager-load and return: `jobHistory`, `eduBackground`, `awards`
- The list endpoint returns flat fields only

---

## 6. Data Flow

```
LoginPage → POST /api/login → store token → redirect /admin/students
StudentsPage → GET /api/students?programId=&status= → React Query cache
StudentDetail → GET /api/students/:id → full profile with relations
AddStudent → POST /api/students → invalidate students list query
EditStudent → PUT /api/students/:id → invalidate students list + detail queries
DeleteStudent → DELETE /api/students/:id → invalidate students list
QueryPage → GET /api/students?skillId=X → results table
           GET /api/students?skillId=X&programId=Y → filtered results
```
