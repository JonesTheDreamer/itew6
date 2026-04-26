import { Navigate } from "react-router";

interface AuthGuardProps {
  children: React.ReactNode;
  allowedRoles?: string[];
}

export default function AuthGuard({ children, allowedRoles }: AuthGuardProps) {
  const token = localStorage.getItem("auth_token");
  const userRaw = localStorage.getItem("auth_user");

  if (!token || !userRaw) return <Navigate to="/login" replace />;

  if (allowedRoles) {
    const user = JSON.parse(userRaw);
    if (!allowedRoles.includes(user.role)) {
      // Redirect to their correct area
      if (user.role === "admin")
        return <Navigate to="/admin/students" replace />;
      if (user.role === "faculty") return <Navigate to="/faculty" replace />;
      if (user.role === "student") return <Navigate to="/student" replace />;
      return <Navigate to="/login" replace />;
    }
  }

  return <>{children}</>;
}
