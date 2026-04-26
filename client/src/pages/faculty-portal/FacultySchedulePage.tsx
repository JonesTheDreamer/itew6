import { useQuery } from "@tanstack/react-query";
import { getSchedules } from "../../api/schedule";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export default function FacultySchedulePage() {
  const userRaw = localStorage.getItem("auth_user");
  const user = userRaw ? JSON.parse(userRaw) : null;
  const facultyId = user?.profile?.id as number | undefined;

  const { data: schedules = [], isLoading } = useQuery({
    queryKey: ["schedules", facultyId],
    queryFn: () => getSchedules({ facultyId }),
    enabled: !!facultyId,
  });

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">My Schedule</h1>
      <Card>
        <CardHeader>
          <CardTitle className="text-base">
            Class Schedule — AY 2025-2026
          </CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-2">
              <Skeleton className="h-12 w-full" />
              <Skeleton className="h-12 w-full" />
              <Skeleton className="h-12 w-full" />
            </div>
          ) : schedules.length === 0 ? (
            <p className="text-sm text-muted-foreground">No schedule found.</p>
          ) : (
            <div className="space-y-3">
              {schedules.map((s) => (
                <div
                  key={s.id}
                  className="border-l-2 border-primary pl-3 text-sm"
                >
                  <p className="font-medium">
                    {s.course?.courseName ?? s.courseName}
                  </p>
                  <p className="text-muted-foreground">
                    {s.timeStart ? `${s.timeStart} – ${s.timeEnd}` : "—"}
                    {s.room ? ` · Room ${s.room}` : ""}
                  </p>
                  {s.section && (
                    <p className="text-muted-foreground text-xs">
                      Section {s.section.sectionName} · Year{" "}
                      {s.section.yearLevel}, Sem {s.section.semester} · AY{" "}
                      {s.section.academicYear}
                    </p>
                  )}
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
