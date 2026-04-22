import { useParams, useNavigate } from "react-router";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { ArrowLeft, Pencil, X } from "lucide-react";
import { getStudent, updateStudent } from "@/api/students";
import { getSkills } from "@/api/skills";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Separator } from "@/components/ui/separator";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import StatusBadge from "@/components/shared/StatusBadge";
import SkillBadge from "@/components/shared/SkillBadge";
import ViolationsTab from "./ViolationsTab";
import { AddAwardModal } from "@/components/modals/AddAwardModal";
import { AddExtraCurricularModal } from "@/components/modals/AddExtraCurricular";

export default function StudentDetailPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: student, isLoading } = useQuery({
    queryKey: ["students", Number(id)],
    queryFn: () => getStudent(Number(id)),
    enabled: !!id,
  });

  console.log(student);

  const { data: allSkills = [] } = useQuery({
    queryKey: ["skills"],
    queryFn: getSkills,
  });

  const skillMutation = useMutation({
    mutationFn: (skillIds: number[]) =>
      updateStudent(Number(id), {
        firstName: student!.firstName,
        lastName: student!.lastName,
        email: student!.email,
        programId: student!.programId,
        yearLevel: student!.yearLevel,
        status: student!.status,
        skillIds,
      }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["students", Number(id)] });
    },
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

  if (!student)
    return <p className="text-muted-foreground">Student not found.</p>;

  const initials =
    [student.firstName?.[0], student.lastName?.[0]]
      .filter(Boolean)
      .join("")
      .toUpperCase() || "?";

  const assignedIds = student.skills?.map((s) => s.id) ?? [];
  const availableSkills = allSkills.filter((s) => !assignedIds.includes(s.id));

  const removeSkill = (skillId: number) => {
    skillMutation.mutate(assignedIds.filter((sid) => sid !== skillId));
  };

  const addSkill = (value: string) => {
    skillMutation.mutate([...assignedIds, Number(value)]);
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <Button
          variant="ghost"
          size="sm"
          onClick={() => navigate("/admin/students")}
        >
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <Button
          size="sm"
          onClick={() => navigate(`/admin/students/${student.id}/edit`)}
        >
          <Pencil className="h-4 w-4 mr-1" /> Edit
        </Button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardContent className="pt-6 flex flex-col items-center text-center space-y-3">
            <Avatar className="h-20 w-20">
              <AvatarFallback className="text-2xl bg-primary/10 text-primary">
                {initials}
              </AvatarFallback>
            </Avatar>
            <div>
              <h2 className="font-bold text-lg">
                {student.firstName}{" "}
                {student.middleName ? student.middleName + " " : ""}
                {student.lastName}
              </h2>
              <p className="text-sm text-muted-foreground font-mono">
                {student.studentId}
              </p>
            </div>
            <StatusBadge status={student.status} />
            <Separator />
            <div className="w-full text-left space-y-1 text-sm">
              <p>
                <span className="text-muted-foreground">Email:</span>{" "}
                {student.email}
              </p>
              {student.mobileNumber && (
                <p>
                  <span className="text-muted-foreground">Mobile:</span>{" "}
                  {student.mobileNumber}
                </p>
              )}
              {student.city && (
                <p>
                  <span className="text-muted-foreground">City:</span>{" "}
                  {student.city}
                  {student.province ? `, ${student.province}` : ""}
                </p>
              )}
              {student.birthDate && (
                <p>
                  <span className="text-muted-foreground">Birth:</span>{" "}
                  {student.birthDate}
                </p>
              )}
            </div>
          </CardContent>
        </Card>

        <div className="md:col-span-2">
          <Tabs defaultValue="academic">
            <TabsList className="mb-4">
              <TabsTrigger value="academic">Academic</TabsTrigger>
              {/* <TabsTrigger value="grades">Grades</TabsTrigger> */}
              <TabsTrigger value="education">Education</TabsTrigger>
              <TabsTrigger value="skills">Skills</TabsTrigger>
              <TabsTrigger value="activities">Activities</TabsTrigger>
              <TabsTrigger value="organizations">Organizations</TabsTrigger>
              <TabsTrigger value="violations">Violations</TabsTrigger>
            </TabsList>

            <TabsContent value="academic">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">
                    Academic Information
                  </CardTitle>
                </CardHeader>
                <CardContent className="grid grid-cols-2 gap-3 text-sm">
                  {[
                    ["Program", student.programName],
                    ["Year Level", student.yearLevel],
                    [
                      "GPA",
                      student.gpa != null
                        ? Number(student.gpa).toFixed(2)
                        : "—",
                    ],
                    ["Units Taken", student.unitsTaken],
                    ["Units Left", student.unitsLeft],
                    ["Date Enrolled", student.dateEnrolled ?? "—"],
                    ["Date Graduated", student.dateGraduated ?? "—"],
                    ["Date Dropped", student.dateDropped ?? "—"],
                  ].map(([label, value]) => (
                    <div key={String(label)}>
                      <p className="text-muted-foreground text-xs">{label}</p>
                      <p className="font-medium">{String(value)}</p>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </TabsContent>

            {/* <TabsContent value="grades">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">Grades</CardTitle>
                </CardHeader>
                <CardContent>
                  {!student.grades || student.grades.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No grade records.
                    </p>
                  ) : (
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="border-b">
                          <th className="text-left py-2 font-medium text-muted-foreground">
                            Course
                          </th>
                          <th className="text-left py-2 font-medium text-muted-foreground">
                            Term
                          </th>
                          <th className="text-left py-2 font-medium text-muted-foreground">
                            Grade
                          </th>
                          <th className="text-left py-2 font-medium text-muted-foreground">
                            AY
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        {student.grades.map((g) => (
                          <tr key={g.id} className="border-b last:border-0">
                            <td className="py-1.5">
                              {g.course?.courseName ?? `Course #${g.courseId}`}
                            </td>
                            <td className="py-1.5 capitalize">{g.term}</td>
                            <td className="py-1.5 font-mono">
                              {Number(g.grade).toFixed(2)}
                            </td>
                            <td className="py-1.5 text-muted-foreground">
                              {g.academicYear}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  )}
                </CardContent>
              </Card>
            </TabsContent> */}
            <TabsContent value="education">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">
                    Educational Background
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  {!student.educationalBackground ||
                  student.educationalBackground.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No educational background recorded.
                    </p>
                  ) : (
                    <div className="space-y-3">
                      {student.educationalBackground.map((e) => (
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

            <TabsContent value="skills">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">Skills</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                  {student.skills && student.skills.length > 0 ? (
                    <div className="flex flex-wrap gap-2">
                      {student.skills.map((skill) => (
                        <div key={skill.id} className="flex items-center gap-1">
                          <SkillBadge skill={skill} />
                          <button
                            type="button"
                            aria-label={`Remove ${skill.name}`}
                            onClick={() => removeSkill(skill.id)}
                            disabled={skillMutation.isPending}
                            className="rounded-full hover:bg-muted p-0.5 text-muted-foreground hover:text-foreground disabled:opacity-50"
                          >
                            <X className="h-3 w-3" />
                          </button>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-sm text-muted-foreground">
                      No skills recorded.
                    </p>
                  )}

                  {availableSkills.length > 0 && (
                    <div className="pt-1">
                      <Select
                        key={assignedIds.join(",")}
                        onValueChange={addSkill}
                        disabled={skillMutation.isPending}
                      >
                        <SelectTrigger className="w-56">
                          <SelectValue placeholder="Add a skill…" />
                        </SelectTrigger>
                        <SelectContent>
                          {availableSkills.map((s) => (
                            <SelectItem key={s.id} value={String(s.id)}>
                              {s.name}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                  )}
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="activities">
              <div className="space-y-4">
                <div className="flex gap-2">
                  <AddAwardModal studentId={student.id} />
                  <AddExtraCurricularModal studentId={student.id} />
                </div>
                <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                  <CardHeader>
                    <CardTitle className="text-base">Awards</CardTitle>
                  </CardHeader>
                  <CardContent>
                    {!student.awards || student.awards.length === 0 ? (
                      <p className="text-sm text-muted-foreground">
                        No awards recorded.
                      </p>
                    ) : (
                      <ul className="space-y-2">
                        {student.awards.map((a) => (
                          <li
                            key={a.id}
                            className="text-sm border-l-2 border-primary pl-3"
                          >
                            <p className="font-medium">{a.title}</p>
                            {a.awardingOrganization && (
                              <p className="text-muted-foreground">
                                {a.awardingOrganization}{" "}
                                {a.awardingDate ? `· ${a.awardingDate}` : ""}
                              </p>
                            )}
                          </li>
                        ))}
                      </ul>
                    )}
                  </CardContent>
                </Card>
                <Card>
                  <CardHeader>
                    <CardTitle className="text-base">
                      Extracurricular Activities
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    {!student.extraCurriculars ||
                    student.extraCurriculars.length === 0 ? (
                      <p className="text-sm text-muted-foreground">
                        No extracurricular records.
                      </p>
                    ) : (
                      <ul className="space-y-2">
                        {student.extraCurriculars.map((e) => (
                          <li
                            key={e.id}
                            className="text-sm border-l-2 border-primary pl-3"
                          >
                            <p className="font-medium">
                              {e.activity} {e.role ? `— ${e.role}` : ""}
                            </p>
                            <p className="font-small">
                              Organized by: {e.organization}
                            </p>
                            {e.startDate && (
                              <p className="text-muted-foreground">
                                {e.startDate} – {e.endDate ?? "present"}
                              </p>
                            )}
                          </li>
                        ))}
                      </ul>
                    )}
                  </CardContent>
                </Card>
              </div>
            </TabsContent>
            <TabsContent value="organizations">
              <Card>
                <CardHeader>
                  <CardTitle className="text-base">Organizations</CardTitle>
                </CardHeader>

                <CardContent>
                  {!student.organizations ||
                  student.organizations.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                      No organization records.
                    </p>
                  ) : (
                    <ul className="space-y-2">
                      {student.organizations.map((uo) => (
                        <li
                          key={uo.id}
                          className="text-sm border-l-2 border-primary pl-3"
                        >
                          <p className="font-medium">
                            {uo.organization?.organizationName ??
                              "Unknown Organization"}
                          </p>

                          <p className="text-muted-foreground">
                            Role: {uo.role ?? "—"}
                          </p>

                          <p className="text-muted-foreground">
                            College: {uo.organization?.college?.name ?? "—"}
                          </p>

                          {(uo.dateJoined || uo.dateLeft) && (
                            <p className="text-muted-foreground">
                              {uo.dateJoined ?? "?"} –{" "}
                              {uo.dateLeft ?? "present"}
                            </p>
                          )}
                        </li>
                      ))}
                    </ul>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            <TabsContent value="violations">
              <Card className="h-[calc(90vh-10rem)] overflow-y-auto">
                <CardHeader>
                  <CardTitle className="text-base">Violations</CardTitle>
                </CardHeader>
                <CardContent>
                  <ViolationsTab studentId={student.id} />
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
}
