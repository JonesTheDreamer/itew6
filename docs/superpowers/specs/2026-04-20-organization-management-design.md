# Organization Management — Design Spec
Date: 2026-04-20

## Overview

Admin-facing organization management for the CCS Profiling System. The admin can create organizations, toggle their active status, manage members (students and faculty separately), and view organization details with current and former members.

## Backend Changes

The backend already has Organization and UserOrganization models, controllers, repositories, requests, and routes. Three targeted changes are required:

### 1. OrganizationRepository — eager-load college
Override `getAll()` and `getById()` to include `with('college')` so the list and detail pages receive college name without a second request.

### 2. UserOrganizationRepository::getByOrganization()
Change `with('user')` to `with('user.student', 'user.faculty')` so the response includes enough data to split members into student vs. faculty and display their specific details.

### 3. No new routes or controllers
All existing endpoints are sufficient:
- `GET/POST /organizations`, `GET/PUT/DELETE /organizations/{id}` — org CRUD
- `GET/POST /organizations/stats` — stats
- `GET/POST /user-organizations`, `GET/PUT/DELETE /user-organizations/{id}` — membership CRUD
- `GET /colleges` — for college select in forms

## Frontend — New Files

### `client/src/api/organizations.ts`
API service functions:
- `getOrganizations()` — GET `/organizations`
- `getOrganization(id)` — GET `/organizations/{id}`
- `createOrganization(payload)` — POST `/organizations`
- `updateOrganization(id, payload)` — PUT `/organizations/{id}`
- `deleteOrganization(id)` — DELETE `/organizations/{id}`
- `getOrgMembers(organizationId)` — GET `/user-organizations?organizationId={id}`
- `addOrgMember(payload)` — POST `/user-organizations`
- `updateOrgMember(id, payload)` — PUT `/user-organizations/{id}`
- `removeOrgMember(id)` — PUT `/user-organizations/{id}` with `{ dateLeft: today }`

### `client/src/pages/organizations/OrganizationsPage.tsx`
List page at `/admin/organizations`:
- Header: "Organizations" title + "Add Organization" button
- Filter card: search by name (client-side), status filter (All / Active / Inactive)
- Table columns: Name, College, Date Created, Status badge, Actions
- Row actions: View (eye), Toggle Active/Inactive (with confirmation dialog), Delete (with confirmation dialog)
- Toggle active calls `PUT /organizations/:id` with `{ isActive: !current }`

### `client/src/pages/organizations/AddOrganizationPage.tsx`
Create form at `/admin/organizations/add`:
- Fields: Organization Name (required, text), Description (optional, textarea), Date Created (optional, date), College (optional, select populated from GET `/colleges`)
- Submit → POST `/organizations` → redirect to `/admin/organizations/:newId` on success

### `client/src/pages/organizations/OrganizationDetailPage.tsx`
Detail page at `/admin/organizations/:id` with three tabs:

**Overview tab**
- Displays: Name, Description, College, Date Created
- Status badge + "Activate" / "Deactivate" button → PUT `/organizations/:id` with `{ isActive: !current }`

**Student Members tab**
- "Show Former Members" toggle (client-side filter; off = only rows where `dateLeft == null`)
- "Add Student" button → dialog:
  - Search students by name (filters against GET `/students` result)
  - Select one student
  - Role field (text, optional)
  - Date Joined field (date, optional)
  - Submit → POST `/user-organizations` with `{ userId, organizationId, role, dateJoined }`
- Table columns: Student ID, Name, Program, Role, Date Joined, Date Left, Remove
- Remove action → sets `dateLeft` to today via PUT `/user-organizations/:id`

**Faculty Members tab**
- Same structure as Student Members tab
- Table columns: Name, Position, Department, Role, Date Joined, Date Left, Remove
- "Add Faculty" dialog searches GET `/faculty` instead of students

## Frontend — Modified Files

### `client/src/main.tsx`
Add routes under `/admin`:
```
{ path: 'organizations', element: <OrganizationsPage /> }
{ path: 'organizations/add', element: <AddOrganizationPage /> }
{ path: 'organizations/:id', element: <OrganizationDetailPage /> }
```

### `client/src/components/layout/AppSidebar.tsx`
Add an "Organizations" nav group with:
- Organization List → `/admin/organizations`
- Add Organization → `/admin/organizations/add`

### `client/src/types/index.ts`
Add `OrgMember` type representing a UserOrganization with resolved user details:
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
    student?: { id: number; studentId: string; programName?: string };
    faculty?: { id: number; position?: string; department?: string };
  };
};
```

## Data Flow

- All fetches use `useQuery`; all writes use `useMutation` — matching existing patterns
- Query keys: `['organizations']` for list, `['organization', id]` for detail, `['org-members', id]` for members
- On mutation success, invalidate relevant query keys to refresh UI
- Colleges fetched once via `useQuery(['colleges'])`, reused in Add form and Detail overview
- Former members toggle is a client-side filter on the already-fetched members array — no extra API call

## Error Handling

- Mutation errors display an inline error message; submit button disabled while `isPending`
- 404 on detail page shows "Organization not found" with a back link
- Confirmation dialogs required before toggle active/inactive and delete actions
