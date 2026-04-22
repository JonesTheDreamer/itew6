import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { Plus, Pencil, ChevronDown, ChevronUp } from "lucide-react";
import {
  getViolations,
  createViolation,
  updateViolation,
  addViolationNote,
} from "@/api/violations";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

interface Props {
  studentId: number;
}

export default function ViolationsTab({ studentId }: Props) {
  const queryClient = useQueryClient();

  const [addOpen, setAddOpen] = useState(false);
  const [formTitle, setFormTitle] = useState("");
  const [formDate, setFormDate] = useState("");
  const [formDesc, setFormDesc] = useState("");
  const [formError, setFormError] = useState("");

  const [expandedIds, setExpandedIds] = useState<Set<number>>(new Set());
  const [editingId, setEditingId] = useState<number | null>(null);
  const [editDesc, setEditDesc] = useState("");
  const [noteTexts, setNoteTexts] = useState<Record<number, string>>({});

  const { data: violations = [], isLoading } = useQuery({
    queryKey: ["violations", studentId],
    queryFn: () => getViolations(studentId),
    enabled: !!studentId,
  });

  const createMutation = useMutation({
    mutationFn: () =>
      createViolation(studentId, {
        title: formTitle.trim(),
        violationDate: formDate,
        description: formDesc.trim() || undefined,
      }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["violations", studentId] });
      setAddOpen(false);
      setFormTitle("");
      setFormDate("");
      setFormDesc("");
      setFormError("");
    },
    onError: () => setFormError("Failed to save. Please try again."),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, description }: { id: number; description: string }) =>
      updateViolation(id, { description }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["violations", studentId] });
      setEditingId(null);
    },
  });

  const noteMutation = useMutation({
    mutationFn: ({
      violationId,
      note,
    }: {
      violationId: number;
      note: string;
    }) => addViolationNote(violationId, { note }),
    onSuccess: (_, { violationId }) => {
      queryClient.invalidateQueries({ queryKey: ["violations", studentId] });
      setNoteTexts((prev) => ({ ...prev, [violationId]: "" }));
    },
  });

  const toggleExpand = (id: number) => {
    setExpandedIds((prev) => {
      const next = new Set(prev);
      next.has(id) ? next.delete(id) : next.add(id);
      return next;
    });
  };

  if (isLoading) {
    return (
      <div className="space-y-2">
        {Array.from({ length: 2 }).map((_, i) => (
          <Skeleton key={i} className="h-20 w-full" />
        ))}
      </div>
    );
  }

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <p className="text-sm text-muted-foreground">
          {violations.length} violation{violations.length !== 1 ? "s" : ""} on
          record
        </p>
        <Button size="sm" onClick={() => setAddOpen(true)}>
          <Plus className="h-4 w-4 mr-1" /> Add Violation
        </Button>
      </div>

      {violations.length === 0 ? (
        <p className="text-sm text-muted-foreground text-center py-6">
          No violations recorded.
        </p>
      ) : (
        violations.map((v) => (
          <Card key={v.id}>
            <CardHeader className="pb-2 pt-4 px-4">
              <div className="flex items-start justify-between gap-2">
                <div className="flex-1 min-w-0">
                  <p className="font-medium text-sm">{v.title}</p>
                  <Badge variant="outline" className="text-xs mt-1">
                    {v.violationDate}
                  </Badge>
                </div>
                <Button
                  size="icon"
                  variant="ghost"
                  className="h-7 w-7 shrink-0"
                  onClick={() => {
                    setEditingId(v.id);
                    setEditDesc(v.description ?? "");
                  }}
                >
                  <Pencil className="h-3.5 w-3.5" />
                </Button>
              </div>
            </CardHeader>
            <CardContent className="px-4 pb-3 space-y-2">
              {editingId === v.id ? (
                <div className="space-y-2">
                  <Textarea
                    value={editDesc}
                    onChange={(e) => setEditDesc(e.target.value)}
                    rows={3}
                    placeholder="Description…"
                  />
                  <div className="flex gap-2">
                    <Button
                      size="sm"
                      onClick={() =>
                        updateMutation.mutate({
                          id: v.id,
                          description: editDesc,
                        })
                      }
                      disabled={updateMutation.isPending}
                    >
                      {updateMutation.isPending ? "Saving…" : "Save"}
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => setEditingId(null)}
                    >
                      Cancel
                    </Button>
                  </div>
                </div>
              ) : (
                <p className="text-sm text-muted-foreground">
                  {v.description ? v.description : <em>No description.</em>}
                </p>
              )}

              <button
                type="button"
                className="flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground transition-colors"
                onClick={() => toggleExpand(v.id)}
              >
                {expandedIds.has(v.id) ? (
                  <ChevronUp className="h-3 w-3" />
                ) : (
                  <ChevronDown className="h-3 w-3" />
                )}
                Notes ({v.notes?.length ?? 0})
              </button>

              {expandedIds.has(v.id) && (
                <div className="space-y-2 pt-1 border-t">
                  {v.notes && v.notes.length > 0 ? (
                    v.notes.map((n) => (
                      <div key={n.id} className="text-xs space-y-0.5 py-1">
                        <p>{n.note}</p>
                        <p className="text-muted-foreground">
                          {n.addedByName} ·{" "}
                          {new Date(n.created_at).toLocaleString()}
                        </p>
                      </div>
                    ))
                  ) : (
                    <p className="text-xs text-muted-foreground italic">
                      No notes yet.
                    </p>
                  )}
                  <div className="flex gap-2 pt-1 items-end">
                    <Textarea
                      rows={2}
                      placeholder="Add a note…"
                      className="text-xs"
                      value={noteTexts[v.id] ?? ""}
                      onChange={(e) =>
                        setNoteTexts((prev) => ({
                          ...prev,
                          [v.id]: e.target.value,
                        }))
                      }
                    />
                    <Button
                      size="sm"
                      disabled={
                        !noteTexts[v.id]?.trim() || noteMutation.isPending
                      }
                      onClick={() =>
                        noteMutation.mutate({
                          violationId: v.id,
                          note: noteTexts[v.id].trim(),
                        })
                      }
                    >
                      Add
                    </Button>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        ))
      )}

      <Dialog
        open={addOpen}
        onOpenChange={(open) => {
          if (!open) {
            setAddOpen(false);
            setFormError("");
          }
        }}
      >
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Add Violation</DialogTitle>
          </DialogHeader>
          <div className="rounded-md bg-amber-50 border border-amber-200 px-3 py-2 text-sm text-amber-800">
            Violations cannot be deleted once added.
          </div>
          <form
            onSubmit={(e) => {
              e.preventDefault();
              createMutation.mutate();
            }}
            className="space-y-3 pt-1"
          >
            <div className="space-y-1">
              <Label htmlFor="vTitle">
                Title <span className="text-destructive">*</span>
              </Label>
              <Input
                id="vTitle"
                value={formTitle}
                onChange={(e) => setFormTitle(e.target.value)}
                required
                autoFocus
              />
            </div>
            <div className="space-y-1">
              <Label htmlFor="vDate">
                Date <span className="text-destructive">*</span>
              </Label>
              <Input
                id="vDate"
                type="date"
                value={formDate}
                onChange={(e) => setFormDate(e.target.value)}
                required
              />
            </div>
            <div className="space-y-1">
              <Label htmlFor="vDesc">Description</Label>
              <Textarea
                id="vDesc"
                value={formDesc}
                onChange={(e) => setFormDesc(e.target.value)}
                rows={3}
                placeholder="Optional details…"
              />
            </div>
            {formError && (
              <p className="text-sm text-destructive">{formError}</p>
            )}
            <DialogFooter>
              <Button
                type="button"
                variant="outline"
                onClick={() => setAddOpen(false)}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={createMutation.isPending}>
                {createMutation.isPending ? "Adding…" : "Add Violation"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>
  );
}
