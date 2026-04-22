import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createExtraCurricular } from "@/api/extraCurricular";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";

export function AddExtraCurricularModal({ studentId }: { studentId: number }) {
  const queryClient = useQueryClient();
  const [open, setOpen] = useState(false);

  const [form, setForm] = useState({
    activity: "",
    role: "",
    organization: "",
    startDate: "",
    endDate: "",
  });

  const mutation = useMutation({
    mutationFn: () =>
      createExtraCurricular({
        studentId,
        ...form,
      }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["students", studentId] });
      setOpen(false);
      setForm({
        activity: "",
        role: "",
        organization: "",
        startDate: "",
        endDate: "",
      });
    },
  });

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button size="sm">Add Activity</Button>
      </DialogTrigger>

      <DialogContent>
        <DialogHeader>
          <DialogTitle>Add Extracurricular Activity</DialogTitle>
        </DialogHeader>

        <div className="space-y-2">
          <Input
            placeholder="Activity"
            value={form.activity}
            onChange={(e) =>
              setForm((p) => ({ ...p, activity: e.target.value }))
            }
          />

          <Input
            placeholder="Role"
            value={form.role}
            onChange={(e) => setForm((p) => ({ ...p, role: e.target.value }))}
          />

          <Input
            placeholder="Organization"
            value={form.organization}
            onChange={(e) =>
              setForm((p) => ({
                ...p,
                organization: e.target.value,
              }))
            }
          />

          <Input
            type="date"
            placeholder="Start Date"
            value={form.startDate}
            onChange={(e) =>
              setForm((p) => ({
                ...p,
                startDate: e.target.value,
              }))
            }
          />

          <Input
            type="date"
            placeholder="End Date"
            value={form.endDate}
            onChange={(e) =>
              setForm((p) => ({
                ...p,
                endDate: e.target.value,
              }))
            }
          />

          <Button
            className="w-full"
            onClick={() => mutation.mutate()}
            disabled={mutation.isPending}
          >
            Save
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
