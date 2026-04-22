import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createAward } from "@/api/award";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";

export function AddAwardModal({ studentId }: { studentId: number }) {
  const queryClient = useQueryClient();
  const [open, setOpen] = useState(false);

  const [form, setForm] = useState({
    title: "",
    awardingDate: "",
    awardingOrganization: "",
    awardingLocation: "",
  });

  const mutation = useMutation({
    mutationFn: () =>
      createAward({
        studentId,
        ...form,
      }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["students", studentId] });
      setOpen(false);
      setForm({
        title: "",
        awardingDate: "",
        awardingOrganization: "",
        awardingLocation: "",
      });
    },
  });

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button size="sm">Add Award</Button>
      </DialogTrigger>

      <DialogContent>
        <DialogHeader>
          <DialogTitle>Add Award</DialogTitle>
        </DialogHeader>

        <div className="space-y-2">
          <Input
            placeholder="Title"
            value={form.title}
            onChange={(e) => setForm((p) => ({ ...p, title: e.target.value }))}
          />

          <Input
            placeholder="Awarding Date"
            type="date"
            value={form.awardingDate}
            onChange={(e) =>
              setForm((p) => ({ ...p, awardingDate: e.target.value }))
            }
          />

          <Input
            placeholder="Organization"
            value={form.awardingOrganization}
            onChange={(e) =>
              setForm((p) => ({
                ...p,
                awardingOrganization: e.target.value,
              }))
            }
          />

          <Input
            placeholder="Location"
            value={form.awardingLocation}
            onChange={(e) =>
              setForm((p) => ({
                ...p,
                awardingLocation: e.target.value,
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
