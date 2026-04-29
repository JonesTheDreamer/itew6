import { useQuery } from "@tanstack/react-query";
import { getSchedules } from "../../api/schedule";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";
import { Clock, MapPin, BookOpen, Users } from "lucide-react";

export default function FacultySchedulePage() {
  const userRaw = localStorage.getItem("auth_user");
  const user = userRaw ? JSON.parse(userRaw) : null;
  const facultyId = user?.profile?.id as number | undefined;

  const { data: schedules = [], isLoading } = useQuery({
    queryKey: ["schedules", facultyId],
    queryFn: () => getSchedules({ facultyId }),
    enabled: !!facultyId,
    retry: 1,
  });

  // Group schedules by section
  const grouped = schedules.reduce(
    (acc, s) => {
      const key = s.section?.sectionName ?? `Section ${s.sectionId}`;
      if (!acc[key]) acc[key] = [];
      acc[key].push(s);
      return acc;
    },
    {} as Record<string, typeof schedules>,
  );

  const formatTime = (time: string) => {
    if (!time) return "—";
    const [h, m] = time.split(":");
    const hour = parseInt(h);
    const ampm = hour >= 12 ? "PM" : "AM";
    const hour12 = hour % 12 || 12;
    return `${hour12}:${m} ${ampm}`;
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">My Schedule</h1>
        <p className="text-muted-foreground text-sm mt-1">
          Academic Year 2025–2026
        </p>
      </div>

      {isLoading ? (
        <div className="space-y-4">
          {[1, 2, 3].map((i) => (
            <Skeleton key={i} className="h-32 w-full" />
          ))}
        </div>
      ) : schedules.length === 0 ? (
        <Card>
          <CardContent className="py-12 text-center text-muted-foreground">
            No schedule found.
          </CardContent>
        </Card>
      ) : (
        <div className="space-y-6">
          {Object.entries(grouped).map(([sectionName, items]) => {
            const first = items[0];
            return (
              <Card key={sectionName}>
                <CardHeader className="pb-3">
                  <div className="flex items-center justify-between">
                    <CardTitle className="text-base flex items-center gap-2">
                      <Users className="h-4 w-4 text-primary" />
                      Section {sectionName}
                    </CardTitle>
                    {first.section && (
                      <div className="flex gap-2">
                        <Badge variant="outline">
                          Year {first.section.yearLevel}
                        </Badge>
                        <Badge variant="outline">
                          Sem {first.section.semester}
                        </Badge>
                        <Badge variant="secondary">
                          AY {first.section.academicYear}
                        </Badge>
                      </div>
                    )}
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="divide-y">
                    {items.map((s) => (
                      <div
                        key={s.id}
                        className="py-3 flex items-start justify-between gap-4"
                      >
                        <div className="flex items-start gap-3">
                          <div className="mt-0.5 p-1.5 rounded-md bg-primary/10">
                            <BookOpen className="h-4 w-4 text-primary" />
                          </div>
                          <div>
                            <p className="font-medium text-sm">
                              {s.course?.courseName ?? s.courseName}
                            </p>
                            {s.course?.courseCode && (
                              <p className="text-xs text-muted-foreground">
                                {s.course.courseCode}
                              </p>
                            )}
                          </div>
                        </div>
                        <div className="flex items-center gap-4 text-sm text-muted-foreground shrink-0">
                          <span className="flex items-center gap-1">
                            <Clock className="h-3.5 w-3.5" />
                            {formatTime(s.timeStart)} – {formatTime(s.timeEnd)}
                          </span>
                          {s.room && (
                            <span className="flex items-center gap-1">
                              <MapPin className="h-3.5 w-3.5" />
                              {s.room}
                            </span>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            );
          })}
        </div>
      )}
    </div>
  );
}
