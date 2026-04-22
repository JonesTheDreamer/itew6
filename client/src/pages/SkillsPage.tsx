import { useState } from "react";
import { useNavigate } from "react-router";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { Plus, Pencil, Trash2, BookOpen, Users } from "lucide-react";
import { getSkills, createSkill, updateSkill, deleteSkill } from "@/api/skills";
import { getStudents } from "@/api/students";
import type { Skill } from "@/types";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Skeleton } from "@/components/ui/skeleton";
import StatusBadge from "@/components/shared/StatusBadge";

export default function SkillsPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const [selectedSkillId, setSelectedSkillId] = useState<number | null>(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [editTarget, setEditTarget] = useState<Skill | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<Skill | null>(null);
  const [formName, setFormName] = useState("");
  const [formIsAcademic, setFormIsAcademic] = useState(false);
  const [formError, setFormError] = useState("");

  const { data: skills = [], isLoading: skillsLoading } = useQuery({
    queryKey: ["skills"],
    queryFn: getSkills,
  });

  const { data: students = [], isLoading: studentsLoading } = useQuery({
    queryKey: ["students", { skillId: selectedSkillId }],
    queryFn: () => getStudents({ skillId: selectedSkillId! }),
    enabled: !!selectedSkillId,
  });

  const createMutation = useMutation({
    mutationFn: createSkill,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["skills"] });
      closeDialog();
    },
    onError: () => setFormError("Failed to save. Name may already exist."),
  });

  const updateMutation = useMutation({
    mutationFn: ({
      id,
      payload,
    }: {
      id: number;
      payload: { name: string; isAcademic: boolean };
    }) => updateSkill(id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["skills"] });
      closeDialog();
    },
    onError: () => setFormError("Failed to save. Name may already exist."),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteSkill(id),
    onSuccess: (_, id) => {
      queryClient.invalidateQueries({ queryKey: ["skills"] });
      if (selectedSkillId === id) setSelectedSkillId(null);
      setDeleteTarget(null);
    },
  });

  const openAdd = () => {
    setEditTarget(null);
    setFormName("");
    setFormIsAcademic(false);
    setFormError("");
    setDialogOpen(true);
  };

  const openEdit = (skill: Skill) => {
    setEditTarget(skill);
    setFormName(skill.name);
    setFormIsAcademic(skill.isAcademic);
    setFormError("");
    setDialogOpen(true);
  };

  const closeDialog = () => {
    setDialogOpen(false);
    setEditTarget(null);
    setFormError("");
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setFormError("");
    const payload = { name: formName.trim(), isAcademic: formIsAcademic };
    if (editTarget) {
      updateMutation.mutate({ id: editTarget.id, payload });
    } else {
      createMutation.mutate(payload);
    }
  };

  const selectedSkill = skills.find((s) => s.id === selectedSkillId);
  const isSaving = createMutation.isPending || updateMutation.isPending;

  return (
    <div className="space-y-4">
      <div>
        <h1 className="text-2xl font-bold">Skills</h1>
        <p className="text-sm text-muted-foreground mt-1">
          Manage the skill catalog and view students by skill.
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-4 items-start">
        {/* Left panel — Skill Catalog */}
        <Card className="h-[calc(100vh-10rem)] flex flex-col">
          <CardHeader className="pb-3 shrink-0">
            <div className="flex items-center justify-between">
              <CardTitle className="text-base">Skill Catalog</CardTitle>
              <Button size="sm" onClick={openAdd}>
                <Plus className="h-4 w-4 mr-1" /> Add Skill
              </Button>
            </div>
          </CardHeader>

          <CardContent className="p-0 flex-1 overflow-y-auto">
            {skillsLoading ? (
              <div className="space-y-1 p-3">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Skeleton key={i} className="h-10 w-full" />
                ))}
              </div>
            ) : skills.length === 0 ? (
              <p className="text-sm text-muted-foreground p-4 text-center">
                No skills yet. Add one above.
              </p>
            ) : (
              <div className="divide-y">
                {skills.map((skill) => (
                  <div
                    key={skill.id}
                    className={`flex items-center gap-2 px-3 py-2.5 cursor-pointer hover:bg-muted/50 transition-colors ${
                      selectedSkillId === skill.id
                        ? "bg-primary/10 border-l-2 border-primary"
                        : ""
                    }`}
                    onClick={() => setSelectedSkillId(skill.id)}
                  >
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium truncate">
                        {skill.name}
                      </p>
                      <div className="flex items-center gap-1.5 mt-0.5">
                        <Badge
                          variant="secondary"
                          className={`text-[10px] px-1 py-0 ${skill.isAcademic ? "bg-primary/10 text-primary" : ""}`}
                        >
                          {skill.isAcademic ? "Academic" : "Soft"}
                        </Badge>
                        <span className="text-xs text-muted-foreground flex items-center gap-0.5">
                          <Users className="h-3 w-3" />
                          {skill.students_count ?? 0}
                        </span>
                      </div>
                    </div>
                    <div
                      className="flex gap-0.5 shrink-0"
                      onClick={(e) => e.stopPropagation()}
                    >
                      <Button
                        size="icon"
                        variant="ghost"
                        className="h-7 w-7"
                        onClick={() => openEdit(skill)}
                      >
                        <Pencil className="h-3.5 w-3.5" />
                      </Button>
                      <Button
                        size="icon"
                        variant="ghost"
                        className="h-7 w-7 text-destructive hover:text-destructive"
                        onClick={() => setDeleteTarget(skill)}
                      >
                        <Trash2 className="h-3.5 w-3.5" />
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        {/* Right panel — Students with selected skill */}
        <Card className="h-[calc(100vh-10rem)] overflow-y-auto">
          {!selectedSkill ? (
            <CardContent className="flex flex-col items-center justify-center py-16 text-center">
              <BookOpen className="h-8 w-8 text-muted-foreground mb-3" />
              <p className="text-sm text-muted-foreground">
                Select a skill to see assigned students.
              </p>
            </CardContent>
          ) : (
            <>
              <CardHeader className="pb-3">
                <div className="flex items-center gap-2">
                  <CardTitle className="text-base">
                    {selectedSkill.name}
                  </CardTitle>
                  <Badge
                    variant="secondary"
                    className={
                      selectedSkill.isAcademic
                        ? "bg-primary/10 text-primary"
                        : ""
                    }
                  >
                    {selectedSkill.isAcademic ? "Academic" : "Soft"}
                  </Badge>
                </div>
                {!studentsLoading && (
                  <p className="text-xs text-muted-foreground">
                    {students.length} student{students.length !== 1 ? "s" : ""}
                  </p>
                )}
              </CardHeader>
              <CardContent className="p-0">
                {studentsLoading ? (
                  <div className="space-y-1 p-3">
                    {Array.from({ length: 3 }).map((_, i) => (
                      <Skeleton key={i} className="h-10 w-full" />
                    ))}
                  </div>
                ) : students.length === 0 ? (
                  <p className="text-sm text-muted-foreground p-4 text-center">
                    No students have this skill yet.
                  </p>
                ) : (
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Student ID</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Program</TableHead>
                        <TableHead>Year</TableHead>
                        <TableHead>Status</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {students.map((s) => (
                        <TableRow
                          key={s.id}
                          className="cursor-pointer hover:bg-muted/50"
                          onClick={() => navigate(`/admin/students/${s.id}`)}
                        >
                          <TableCell className="font-mono text-sm">
                            {s.studentId}
                          </TableCell>
                          <TableCell className="font-medium">
                            {s.firstName} {s.lastName}
                          </TableCell>
                          <TableCell className="text-sm text-muted-foreground">
                            {s.programName}
                          </TableCell>
                          <TableCell>{s.yearLevel}</TableCell>
                          <TableCell>
                            <StatusBadge status={s.status} />
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                )}
              </CardContent>
            </>
          )}
        </Card>
      </div>

      {/* Add / Edit Dialog */}
      <Dialog
        open={dialogOpen}
        onOpenChange={(open) => {
          if (!open) closeDialog();
        }}
      >
        <DialogContent className="max-w-sm">
          <DialogHeader>
            <DialogTitle>{editTarget ? "Edit Skill" : "Add Skill"}</DialogTitle>
          </DialogHeader>
          <form onSubmit={handleSubmit} className="space-y-4 pt-1">
            <div className="space-y-1">
              <Label htmlFor="skillName">
                Name <span className="text-destructive">*</span>
              </Label>
              <Input
                id="skillName"
                value={formName}
                onChange={(e) => setFormName(e.target.value)}
                placeholder="e.g. Python Programming"
                required
                autoFocus
              />
            </div>
            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="isAcademic"
                checked={formIsAcademic}
                onChange={(e) => setFormIsAcademic(e.target.checked)}
                className="h-4 w-4 accent-primary"
              />
              <Label
                htmlFor="isAcademic"
                className="cursor-pointer font-normal"
              >
                Academic skill
              </Label>
            </div>
            {formError && (
              <p className="text-sm text-destructive">{formError}</p>
            )}
            <DialogFooter>
              <Button type="button" variant="outline" onClick={closeDialog}>
                Cancel
              </Button>
              <Button type="submit" disabled={isSaving}>
                {isSaving
                  ? "Saving…"
                  : editTarget
                    ? "Save Changes"
                    : "Add Skill"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Dialog */}
      <Dialog open={!!deleteTarget} onOpenChange={() => setDeleteTarget(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Skill</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to delete{" "}
            <strong>{deleteTarget?.name}</strong>? It will be removed from all
            assigned students.
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeleteTarget(null)}>
              Cancel
            </Button>
            <Button
              variant="destructive"
              onClick={() =>
                deleteTarget && deleteMutation.mutate(deleteTarget.id)
              }
              disabled={deleteMutation.isPending}
            >
              {deleteMutation.isPending ? "Deleting…" : "Delete"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
