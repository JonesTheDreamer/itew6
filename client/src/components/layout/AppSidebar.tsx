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
