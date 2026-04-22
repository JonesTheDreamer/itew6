import { useState } from "react";
import { useNavigate, useParams } from "react-router";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { ArrowLeft, UserPlus } from "lucide-react";
import {
  getOrganization,
  updateOrganization,
  getOrgMembers,
  addOrgMember,
  removeOrgMember,
} from "@/api/organizations";
import { getStudents } from "@/api/students";
import { getFaculty } from "@/api/faculty";
import type { OrgMember, Student, Faculty } from "@/types";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Separator } from "@/components/ui/separator";
import { Skeleton } from "@/components/ui/skeleton";
import StatusBadge from "@/components/shared/StatusBadge";

type DialogType = "student" | "faculty" | null;

function MemberRow({
  m,
  cols,
  isRemoving,
  onRemove,
}: {
  m: OrgMember;
  cols: "student" | "faculty";
  isRemoving: boolean;
  onRemove: (id: number) => void;
}) {
  return (
    <TableRow className={m.dateLeft ? "opacity-60" : ""}>
      {cols === "student" && (
        <TableCell className="text-sm">
          {m.user?.student?.studentId ?? "—"}
        </TableCell>
      )}
      <TableCell className="font-medium">
        {m.user?.firstName} {m.user?.lastName}
      </TableCell>
      {cols === "student" && (
        <TableCell className="text-sm text-muted-foreground">
          {m.user?.student?.program?.name ?? "—"}
        </TableCell>
      )}
      {cols === "faculty" && (
        <>
          <TableCell className="text-sm text-muted-foreground">
            {m.user?.faculty?.position ?? "—"}
          </TableCell>
          <TableCell className="text-sm text-muted-foreground">
            {m.user?.faculty?.department ?? "—"}
          </TableCell>
        </>
      )}
      <TableCell className="text-sm">{m.role ?? "—"}</TableCell>
      <TableCell className="text-sm">{m.dateJoined ?? "—"}</TableCell>
      <TableCell className="text-sm">{m.dateLeft ?? "—"}</TableCell>
      <TableCell className="text-right">
        {!m.dateLeft && (
          <Button
            size="sm"
            variant="ghost"
            className="text-destructive hover:text-destructive"
            disabled={isRemoving}
            onClick={() => onRemove(m.id)}
          >
            Remove
          </Button>
        )}
      </TableCell>
    </TableRow>
  );
}

export default function OrganizationDetailPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const orgId = Number(id);

  const { data: org, isLoading } = useQuery({
    queryKey: ["organization", orgId],
    queryFn: () => getOrganization(orgId),
    enabled: !!orgId,
  });

  const { data: members = [] } = useQuery({
    queryKey: ["org-members", orgId],
    queryFn: () => getOrgMembers(orgId),
    enabled: !!orgId,
  });

  const { data: students = [] } = useQuery({
    queryKey: ["students"],
    queryFn: () => getStudents(),
  });

  const { data: faculty = [] } = useQuery({
    queryKey: ["faculty"],
    queryFn: () => getFaculty(),
  });

  const toggleMutation = useMutation({
    mutationFn: (isActive: boolean) => updateOrganization(orgId, { isActive }),
    onSuccess: () =>
      queryClient.invalidateQueries({ queryKey: ["organization", orgId] }),
  });

  const [removeTarget, setRemoveTarget] = useState<number | null>(null);
  const [removeError, setRemoveError] = useState("");

  const removeMutation = useMutation({
    mutationFn: (memberId: number) => removeOrgMember(memberId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["org-members", orgId] });
      setRemoveTarget(null);
    },
    onError: () => setRemoveError("Failed to remove member. Please try again."),
  });

  const addMutation = useMutation({
    mutationFn: addOrgMember,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["org-members", orgId] });
      closeAddDialog();
    },
    onError: () => setAddError("Failed to add member. Please try again."),
  });

  const studentMembers = members.filter((m) => m.user?.student != null);
  const facultyMembers = members.filter((m) => m.user?.faculty != null);

  const [showFormerStudents, setShowFormerStudents] = useState(false);
  const [showFormerFaculty, setShowFormerFaculty] = useState(false);

  const [addDialogType, setAddDialogType] = useState<DialogType>(null);
  const [memberSearch, setMemberSearch] = useState("");
  const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
  const [memberRole, setMemberRole] = useState("");
  const [memberDateJoined, setMemberDateJoined] = useState("");
  const [addError, setAddError] = useState("");

  const openAddDialog = (type: "student" | "faculty") => {
    setAddDialogType(type);
    setMemberSearch("");
    setSelectedUserId(null);
    setMemberRole("");
    setMemberDateJoined("");
    setAddError("");
  };

  const closeAddDialog = () => {
    setAddDialogType(null);
    setAddError("");
  };

  const handleAddMember = () => {
    if (!selectedUserId) {
      setAddError("Please select a member.");
      return;
    }
    addMutation.mutate({
      userId: selectedUserId,
      organizationId: orgId,
      role: memberRole || null,
      dateJoined: memberDateJoined || null,
    });
  };

  const existingUserIds = new Set(
    members.filter((m) => !m.dateLeft).map((m) => m.userId),
  );

  const filteredStudentsForDialog = students.filter((s) => {
    if (existingUserIds.has(s.userId)) return false;
    const name = `${s.firstName} ${s.lastName}`.toLowerCase();
    return (
      name.includes(memberSearch.toLowerCase()) ||
      (s.studentId ?? "").toLowerCase().includes(memberSearch.toLowerCase())
    );
  });

  const filteredFacultyForDialog = (faculty as Faculty[]).filter((f) => {
    if (existingUserIds.has(f.userId)) return false;
    const name = `${f.firstName} ${f.lastName}`.toLowerCase();
    return name.includes(memberSearch.toLowerCase());
  });

  if (isLoading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-8 w-64" />
        <Skeleton className="h-48" />
      </div>
    );
  }

  if (!org) {
    return (
      <p className="text-muted-foreground">
        Organization not found.{" "}
        <Button
          variant="link"
          className="p-0"
          onClick={() => navigate("/admin/organizations")}
        >
          Go back
        </Button>
      </p>
    );
  }

  const visibleStudents = showFormerStudents
    ? studentMembers
    : studentMembers.filter((m) => !m.dateLeft);

  const visibleFaculty = showFormerFaculty
    ? facultyMembers
    : facultyMembers.filter((m) => !m.dateLeft);

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-2">
        <Button
          variant="ghost"
          size="sm"
          onClick={() => navigate("/admin/organizations")}
        >
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">{org.organizationName}</h1>
        <StatusBadge status={org.isActive ? "Active" : "Inactive"} />
      </div>

      <Tabs defaultValue="overview">
        <TabsList>
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="students">
            Students ({studentMembers.filter((m) => !m.dateLeft).length})
          </TabsTrigger>
          <TabsTrigger value="faculty">
            Faculty ({facultyMembers.filter((m) => !m.dateLeft).length})
          </TabsTrigger>
        </TabsList>

        <TabsContent value="overview" className="space-y-4 mt-4">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Details</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-sm text-muted-foreground">College</p>
                  <p className="font-medium">{org.college?.name ?? "—"}</p>
                </div>
                <div>
                  <p className="text-sm text-muted-foreground">Date Created</p>
                  <p className="font-medium">{org.dateCreated ?? "—"}</p>
                </div>
              </div>
              <Separator />
              <div>
                <p className="text-sm text-muted-foreground">Description</p>
                <p className="mt-1 text-sm">
                  {org.organizationDescription ?? "—"}
                </p>
              </div>
              <Separator />
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-muted-foreground mb-1">Status</p>
                  <StatusBadge status={org.isActive ? "Active" : "Inactive"} />
                </div>
                <Button
                  size="sm"
                  variant={org.isActive ? "destructive" : "default"}
                  disabled={toggleMutation.isPending}
                  onClick={() => toggleMutation.mutate(!org.isActive)}
                >
                  {toggleMutation.isPending
                    ? "Updating…"
                    : org.isActive
                      ? "Deactivate"
                      : "Activate"}
                </Button>
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="students" className="space-y-4 mt-4">
          <div className="flex items-center justify-between">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setShowFormerStudents((v) => !v)}
            >
              {showFormerStudents
                ? "Hide Former Members"
                : "Show Former Members"}
            </Button>
            <Button size="sm" onClick={() => openAddDialog("student")}>
              <UserPlus className="h-4 w-4 mr-2" /> Add Student
            </Button>
          </div>
          <Card>
            <CardContent className="p-0">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Student ID</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Program</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Date Joined</TableHead>
                    <TableHead>Date Left</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {visibleStudents.length === 0 && (
                    <TableRow>
                      <TableCell
                        colSpan={7}
                        className="text-center py-8 text-muted-foreground"
                      >
                        No student members.
                      </TableCell>
                    </TableRow>
                  )}
                  {visibleStudents.map((m) => (
                    <MemberRow
                      key={m.id}
                      m={m}
                      cols="student"
                      isRemoving={removeMutation.isPending}
                      onRemove={(id) => setRemoveTarget(id)}
                    />
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="faculty" className="space-y-4 mt-4">
          <div className="flex items-center justify-between">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setShowFormerFaculty((v) => !v)}
            >
              {showFormerFaculty
                ? "Hide Former Members"
                : "Show Former Members"}
            </Button>
            <Button size="sm" onClick={() => openAddDialog("faculty")}>
              <UserPlus className="h-4 w-4 mr-2" /> Add Faculty
            </Button>
          </div>
          <Card>
            <CardContent className="p-0">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>Position</TableHead>
                    <TableHead>Department</TableHead>
                    <TableHead>Role</TableHead>
                    <TableHead>Date Joined</TableHead>
                    <TableHead>Date Left</TableHead>
                    <TableHead className="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {visibleFaculty.length === 0 && (
                    <TableRow>
                      <TableCell
                        colSpan={7}
                        className="text-center py-8 text-muted-foreground"
                      >
                        No faculty members.
                      </TableCell>
                    </TableRow>
                  )}
                  {visibleFaculty.map((m) => (
                    <MemberRow
                      key={m.id}
                      m={m}
                      cols="faculty"
                      isRemoving={removeMutation.isPending}
                      onRemove={(id) => setRemoveTarget(id)}
                    />
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <Dialog open={!!addDialogType} onOpenChange={closeAddDialog}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>
              Add {addDialogType === "student" ? "Student" : "Faculty"} Member
            </DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <div className="space-y-1">
              <Label>Search</Label>
              <Input
                placeholder={
                  addDialogType === "student"
                    ? "Search by name or student ID…"
                    : "Search by name…"
                }
                value={memberSearch}
                onChange={(e) => {
                  setMemberSearch(e.target.value);
                  setSelectedUserId(null);
                }}
              />
            </div>
            <div className="max-h-40 overflow-y-auto border rounded-md divide-y">
              {(addDialogType === "student"
                ? filteredStudentsForDialog
                : filteredFacultyForDialog
              ).map((p) => {
                const userId = p.userId;
                const isSelected = selectedUserId === userId;
                return (
                  <div
                    key={userId}
                    className={`px-3 py-2 cursor-pointer hover:bg-muted text-sm select-none ${isSelected ? "bg-muted font-medium" : ""}`}
                    onClick={() => setSelectedUserId(userId)}
                  >
                    {p.firstName} {p.lastName}
                    {addDialogType === "student" &&
                      (p as Student).studentId && (
                        <span className="ml-2 text-muted-foreground text-xs">
                          {(p as Student).studentId}
                        </span>
                      )}
                  </div>
                );
              })}
              {(addDialogType === "student"
                ? filteredStudentsForDialog
                : filteredFacultyForDialog
              ).length === 0 && (
                <p className="px-3 py-2 text-sm text-muted-foreground">
                  No results.
                </p>
              )}
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1">
                <Label>Role (optional)</Label>
                <Input
                  value={memberRole}
                  onChange={(e) => setMemberRole(e.target.value)}
                  placeholder="e.g. President"
                />
              </div>
              <div className="space-y-1">
                <Label>Date Joined (optional)</Label>
                <Input
                  type="date"
                  value={memberDateJoined}
                  onChange={(e) => setMemberDateJoined(e.target.value)}
                />
              </div>
            </div>
            {addError && <p className="text-sm text-destructive">{addError}</p>}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={closeAddDialog}>
              Cancel
            </Button>
            <Button onClick={handleAddMember} disabled={addMutation.isPending}>
              {addMutation.isPending ? "Adding…" : "Add Member"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <Dialog
        open={removeTarget !== null}
        onOpenChange={() => {
          setRemoveTarget(null);
          setRemoveError("");
        }}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Remove Member</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to remove this member? Their record will be
            kept with today as the leave date.
          </p>
          {removeError && (
            <p className="text-sm text-destructive">{removeError}</p>
          )}
          <DialogFooter>
            <Button
              variant="outline"
              onClick={() => {
                setRemoveTarget(null);
                setRemoveError("");
              }}
            >
              Cancel
            </Button>
            <Button
              variant="destructive"
              disabled={removeMutation.isPending}
              onClick={() =>
                removeTarget !== null && removeMutation.mutate(removeTarget)
              }
            >
              {removeMutation.isPending ? "Removing…" : "Remove"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
