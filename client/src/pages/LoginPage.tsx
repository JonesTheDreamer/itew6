  import { useEffect, useState } from "react";
  import { useNavigate } from "react-router";
  import { GraduationCap } from "lucide-react";
  import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
  } from "@/components/ui/card";
  import { Input } from "@/components/ui/input";
  import { Button } from "@/components/ui/button";
  import { Label } from "@/components/ui/label";
  import { login } from "@/api/auth";

  function getRoleRedirect(role: string) {
    if (role === "faculty") return "/faculty/profile";
    if (role === "student") return "/student";
    return "/admin/students";
  }

  export default function LoginPage() {
    const navigate = useNavigate();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(false);
    const [checking, setChecking] = useState(true); // ← new

    useEffect(() => {
      const token = localStorage.getItem("auth_token");
      const userRaw = localStorage.getItem("auth_user");
      if (token && userRaw) {
        try {
          const user = JSON.parse(userRaw);
          navigate(getRoleRedirect(user.role), { replace: true });
          return; // don't setChecking if navigating away
        } catch {
          localStorage.removeItem("auth_token");
          localStorage.removeItem("auth_user");
        }
      }
      setChecking(false); // ← only show form once we know user is not logged in
    }, []); // eslint-disable-line react-hooks/exhaustive-deps

    if (checking) return null; // ← blank but instant, avoids flash

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      setError("");
      setLoading(true);
      try {
        const response = await login({ email, password });
        localStorage.setItem("auth_token", response.token);
        localStorage.setItem("auth_user", JSON.stringify(response.user));
        navigate(getRoleRedirect(response.user.role), { replace: true });
      } catch {
        setError("Invalid email or password.");
      } finally {
        setLoading(false);
      }
    };

    return (
      <div className="min-h-screen flex items-center justify-center bg-muted/30">
        <Card className="w-full max-w-sm shadow-lg">
          <CardHeader className="text-center space-y-2">
            <div className="flex justify-center">
              <div className="p-3 rounded-full bg-primary/10">
                <GraduationCap className="h-8 w-8 text-primary" />
              </div>
            </div>
            <CardTitle className="text-xl">CCS Profiling System</CardTitle>
            <CardDescription>Sign in to continue</CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="space-y-1">
                <Label htmlFor="email">Email</Label>
                <Input
                  id="email"
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="admin@ccs.edu.ph"
                  required
                />
              </div>
              <div className="space-y-1">
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••"
                  required
                />
              </div>
              {error && <p className="text-sm text-destructive">{error}</p>}
              <Button type="submit" className="w-full" disabled={loading}>
                {loading ? "Signing in…" : "Sign In"}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>
    );
  }
