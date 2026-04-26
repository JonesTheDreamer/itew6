// src/pages/faculty-portal/FacultyProfilePage.tsx
import { useQuery } from "@tanstack/react-query";
import { getFacultyMember } from "@/api/faculty";
import { getLessons } from "../../api/lessons";
import { getSchedules } from "../../api/schedule";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Separator } from "@/components/ui/separator";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";

export default function FacultyProfilePage() {
  const userRaw = localStorage.getItem("auth_user");
  const user = userRaw ? JSON.parse(userRaw) : null;
  const facultyId = user?.profile?.id as number | undefined;

  const { data: member, isLoading } = useQuery({
    queryKey: ["faculty", facultyId],
    queryFn: () => getFacultyMember(facultyId!),
    enabled: !!facultyId,
  });

  const { data: lessons = [], isLoading: lessonsLoading } = useQuery({
    queryKey: ["lessons", facultyId],
    queryFn: () => getLessons(facultyId!),
    enabled: !!facultyId,
  });

  const { data: schedules = [], isLoading: schedulesLoading } = useQuery({
    queryKey: ["schedules", facultyId],
    queryFn: () => getSchedules({ facultyId }),
    enabled: !!facultyId,
  });

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-48" />
        <div className="grid grid-cols-3 gap-4">
          <Skeleton className="h-64 col-span-1" />
          <Skeleton className="h-64 col-span-2" />
        </div>
      </div>
    );
  }

  if (!member)
    return <p className="text-muted-foreground">Profile not found.</p>;

  const initials =
    [member.firstName?.[0], member.lastName?.[0]]
      .filter(Boolean)
      .join("")
      .toUpperCase() || "?";

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">My Profile</h1>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* ── Left card ── */}
        <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
          <CardContent className="pt-6 flex flex-col items-center text-center space-y-3">
            <Avatar className="h-20 w-20">
              <AvatarFallback className="text-2xl bg-primary/10 text-primary">
                {initials}
              </AvatarFallback>
            </Avatar>
            <div>
              <h2 className="font-bold text-lg">
                {member.firstName}{" "}
                {member.middleName ? member.middleName + " " : ""}
                {member.lastName}
              </h2>
              {member.position && (
                <p className="text-sm text-muted-foreground">
                  {member.position}
                </p>
              )}
            </div>
            {member.employmentType && (
              <Badge variant="outline">{member.employmentType}</Badge>
            )}
            <Separator />
            <div className="w-full text-left space-y-1 text-sm">
              <p>
                <span className="text-muted-foreground">Email:</span>{" "}
                {member.email}
              </p>
              {member.mobileNumber && (
                <p>
                  <span className="text-muted-foreground">Mobile:</span>{" "}
                  {member.mobileNumber}
                </p>
              )}
              {member.department && (
                <p>
                  <span className="text-muted-foreground">Dept:</span>{" "}
                  {member.department}
                </p>
              )}
              {member.city && (
                <p>
                  <span className="text-muted-foreground">City:</span>{" "}
                  {member.city}
                  {member.province ? `, ${member.province}` : ""}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        {/* ── Right tabs ── */}
        <div className="md:col-span-2">
          <Tabs defaultValue="info">
            <TabsList className="mb-4">
              <TabsTrigger value="info">Info</TabsTrigger>
              <TabsTrigger value="employment">Employment</TabsTrigger>
              <TabsTrigger value="education">Education</TabsTrigger>
              <TabsTrigger value="awards">Awards</TabsTrigger>
              <TabsTrigger value="schedule">Schedule</TabsTrigger>
            </TabsList>

            {/* Info */}
            <TabsContent value="info">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">
                    Faculty Information
                  </CardTitle>
                </CardHeader>
                <CardContent className="grid grid-cols-2 gap-3 text-sm">
                  {[
                    ["Department", member.department ?? "—"],
                    ["Position", member.position ?? "—"],
                    ["Employment Type", member.employmentType ?? "—"],
                    ["Employment Date", member.employmentDate ?? "—"],
                    ["Birth Date", member.birthDate ?? "—"],
                    ["Birth Province", member.birthProvince ?? "—"],
                    ["City", member.city ?? "—"],
                    ["Province", member.province ?? "—"],
                    ["Postal Code", member.postalCode ?? "—"],
                    ["Mobile", member.mobileNumber ?? "—"],
                  ].map(([label, value]) => (
                    <div key={label}>
                      <p className="text-muted-foreground text-xs">{label}</p>
                      <p className="font-medium">{value}</p>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </TabsContent>

            {/* Employment */}
            <TabsContent value="employment">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">Job History</CardTitle>
                </CardHeader>
                <CardContent>
                  {!member.jobHistory || member.jobHistory.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No job history recorded.
                    </p>
                  ) : (
                    <div className="space-y-3">
                      {member.jobHistory.map((j) => (
                        <div
                          key={j.id}
                          className="border-l-2 border-primary pl-3 text-sm"
                        >
                          <p className="font-medium">{j.position}</p>
                          {j.company && (
                            <p className="text-muted-foreground">
                              {j.company}
                              {j.workLocation ? ` — ${j.workLocation}` : ""}
                            </p>
                          )}
                          <p className="text-muted-foreground text-xs">
                            {j.employmentDate ?? "?"} –{" "}
                            {j.employmentEndDate ?? "present"}
                            {j.employmentType ? ` · ${j.employmentType}` : ""}
                          </p>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            {/* Education */}
            <TabsContent value="education">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">
                    Educational Background
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  {!member.eduBackground ||
                  member.eduBackground.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No educational background recorded.
                    </p>
                  ) : (
                    <div className="space-y-3">
                      {member.eduBackground.map((e) => (
                        <div
                          key={e.id}
                          className="border-l-2 border-primary pl-3 text-sm"
                        >
                          <p className="font-medium">
                            {e.type ? `${e.type} — ` : ""}
                            {e.schoolUniversity}
                          </p>
                          <p className="text-muted-foreground">
                            {e.startYear ?? "?"} – {e.graduateYear ?? "present"}
                            {e.award ? ` · ${e.award}` : ""}
                          </p>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            {/* Awards */}
            <TabsContent value="awards">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">
                    Awards & Recognition
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  {!member.awards || member.awards.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No awards recorded.
                    </p>
                  ) : (
                    <ul className="space-y-2">
                      {member.awards.map((a) => (
                        <li
                          key={a.id}
                          className="text-sm border-l-2 border-primary pl-3"
                        >
                          <p className="font-medium">{a.title}</p>
                          {a.awardingOrganization && (
                            <p className="text-muted-foreground">
                              {a.awardingOrganization}
                              {a.awardingDate ? ` · ${a.awardingDate}` : ""}
                            </p>
                          )}
                          {a.awardingLocation && (
                            <p className="text-muted-foreground text-xs">
                              {a.awardingLocation}
                            </p>
                          )}
                        </li>
                      ))}
                    </ul>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            {/* Schedule */}
            <TabsContent value="schedule">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">My Schedule</CardTitle>
                </CardHeader>
                <CardContent>
                  {schedulesLoading ? (
                    <div className="space-y-2">
                      <Skeleton className="h-12 w-full" />
                      <Skeleton className="h-12 w-full" />
                    </div>
                  ) : schedules.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No schedule found.
                    </p>
                  ) : (
                    <div className="space-y-3">
                      {schedules.map((s) => (
                        <div
                          key={s.id}
                          className="border-l-2 border-primary pl-3 text-sm"
                        >
                          <p className="font-medium">
                            {s.course?.courseName ?? `Course #${s.courseId}`}
                          </p>
                          <p className="text-muted-foreground">
                            {s.timeStart} – {s.timeEnd}
                            {s.room ? ` · Room ${s.room}` : ""}
                          </p>
                          {s.section && (
                            <p className="text-muted-foreground text-xs">
                              {s.section.sectionName} — AY{" "}
                              {s.section.academicYear}
                            </p>
                          )}
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
}
