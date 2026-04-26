import { Outlet } from "react-router";
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import FacultySidebar from "./FacultySidebar";

export default function FacultyLayout() {
  return (
    <SidebarProvider>
      <FacultySidebar />
      <main className="flex-1 flex flex-col min-h-screen">
        <header className="h-12 flex items-center px-4 border-b bg-background">
          <SidebarTrigger />
        </header>
        <div className="flex-1 p-6">
          <Outlet />
        </div>
      </main>
    </SidebarProvider>
  );
}
