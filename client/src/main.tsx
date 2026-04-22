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
import OrganizationsPage from '@/pages/organizations/OrganizationsPage';
import AddOrganizationPage from '@/pages/organizations/AddOrganizationPage';
import OrganizationDetailPage from '@/pages/organizations/OrganizationDetailPage';
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
      { path: 'organizations', element: <OrganizationsPage /> },
      { path: 'organizations/add', element: <AddOrganizationPage /> },
      { path: 'organizations/:id', element: <OrganizationDetailPage /> },
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
