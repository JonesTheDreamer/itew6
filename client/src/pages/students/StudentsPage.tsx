import { useState, useMemo } from "react";
import { useNavigate } from "react-router";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  Plus,
  Pencil,
  Trash2,
  Eye,
  Search,
  ChevronLeft,
  ChevronRight,
  ChevronsLeft,
  ChevronsRight,
} from "lucide-react";
import { getStudents, deleteStudent } from "@/api/students";
import { getPrograms } from "@/api/programs";
import type { Student } from "@/types";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import StatusBadge from "@/components/shared/StatusBadge";

const PAGE_SIZE_OPTIONS = [10, 25, 50, 100];

const STATUS_COLORS: Record<string, string> = {
  Active: "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
  Graduated: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
  Dropped: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
  Inactive: "bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300",
};

export default function StudentsPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  // Filters
  const [search, setSearch] = useState("");
  const [programId, setProgramId] = useState<string>("all");
  const [statusFilter, setStatusFilter] = useState<string>("all");

  // Pagination
  const [page, setPage] = useState(1);
  const [pageSize, setPageSize] = useState(25);

  const [deleteTarget, setDeleteTarget] = useState<Student | null>(null);

  const { data: students = [], isLoading } = useQuery({
    queryKey: ["students"],
    queryFn: () => getStudents(),
  });

  const { data: programs = [] } = useQuery({
    queryKey: ["programs"],
    queryFn: getPrograms,
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteStudent(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["students"] });
      setDeleteTarget(null);
    },
  });

  // ── Stats ────────────────────────────────────────────────────────────────
  const stats = useMemo(() => {
    const counts: Record<string, number> = {
      Active: 0,
      Graduated: 0,
      Dropped: 0,
      Inactive: 0,
    };
    students.forEach((s) => {
      if (counts[s.status] !== undefined) counts[s.status]++;
    });
    return { total: students.length, ...counts };
  }, [students]);

  // ── Filtered list ────────────────────────────────────────────────────────
  const filtered = useMemo(() => {
    return students.filter((s) => {
      const fullName = `${s.firstName} ${s.lastName}`.toLowerCase();
      const matchName =
        fullName.includes(search.toLowerCase()) ||
        s.studentId.toLowerCase().includes(search.toLowerCase());
      const matchProgram =
        programId === "all" || String(s.programId) === programId;
      const matchStatus = statusFilter === "all" || s.status === statusFilter;
      return matchName && matchProgram && matchStatus;
    });
  }, [students, search, programId, statusFilter]);

  // Reset to page 1 whenever filters change
  const handleFilterChange = (setter: (v: string) => void) => (v: string) => {
    setter(v);
    setPage(1);
  };

  // ── Pagination ───────────────────────────────────────────────────────────
  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const safePage = Math.min(page, totalPages);
  const startIdx = (safePage - 1) * pageSize;
  const endIdx = Math.min(startIdx + pageSize, filtered.length);
  const paginated = filtered.slice(startIdx, endIdx);

  const pageWindow = useMemo(() => {
    const delta = 2;
    const range: (number | "…")[] = [];
    for (let i = 1; i <= totalPages; i++) {
      if (
        i === 1 ||
        i === totalPages ||
        (i >= safePage - delta && i <= safePage + delta)
      ) {
        range.push(i);
      } else if (range[range.length - 1] !== "…") {
        range.push("…");
      }
    }
    return range;
  }, [safePage, totalPages]);

  return (
    <div className="space-y-4">
      {/* ── Header ── */}
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Students</h1>
        <Button onClick={() => navigate("/admin/students/add")}>
          <Plus className="h-4 w-4 mr-2" /> Add Student
        </Button>
      </div>

      {/* ── Summary stat cards ── */}
      {!isLoading && (
        <div className="grid grid-cols-2 sm:grid-cols-5 gap-3">
          {[
            { label: "Total", value: stats.total, color: "bg-muted" },
            {
              label: "Active",
              value: stats.Active,
              color: "bg-green-50 dark:bg-green-950",
            },
            {
              label: "Graduated",
              value: stats.Graduated,
              color: "bg-blue-50 dark:bg-blue-950",
            },
            {
              label: "Dropped",
              value: stats.Dropped,
              color: "bg-red-50 dark:bg-red-950",
            },
            {
              label: "Inactive",
              value: stats.Inactive,
              color: "bg-gray-100 dark:bg-gray-800",
            },
          ].map(({ label, value, color }) => (
            <div
              key={label}
              className={`rounded-lg p-3 ${color} cursor-pointer transition-opacity hover:opacity-80`}
              onClick={() =>
                label !== "Total"
                  ? handleFilterChange(setStatusFilter)(label)
                  : handleFilterChange(setStatusFilter)("all")
              }
            >
              <p className="text-xs text-muted-foreground mb-1">{label}</p>
              <p className="text-2xl font-semibold tabular-nums">
                {value.toLocaleString()}
              </p>
            </div>
          ))}
        </div>
      )}

      {/* ── Filters ── */}
      <Card>
        <CardHeader className="pb-3">
          <CardTitle className="text-base font-medium">Filter</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex gap-3 flex-wrap">
            <div className="relative flex-1 min-w-48">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                className="pl-8"
                placeholder="Search name or ID…"
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value);
                  setPage(1);
                }}
              />
            </div>
            <Select
              value={programId}
              onValueChange={handleFilterChange(setProgramId)}
            >
              <SelectTrigger className="w-52">
                <SelectValue placeholder="All Programs" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Programs</SelectItem>
                {programs.map((p) => (
                  <SelectItem key={p.id} value={String(p.id)}>
                    {p.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            <Select
              value={statusFilter}
              onValueChange={handleFilterChange(setStatusFilter)}
            >
              <SelectTrigger className="w-36">
                <SelectValue placeholder="All Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Status</SelectItem>
                {["Active", "Graduated", "Dropped", "Inactive"].map((s) => (
                  <SelectItem key={s} value={s}>
                    {s}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </CardContent>
      </Card>

      {/* ── Table card ── */}
      <Card>
        {/* Table meta bar */}
        <div className="flex items-center justify-between px-4 py-3 border-b">
          <p className="text-sm text-muted-foreground">
            {isLoading ? (
              "Loading…"
            ) : filtered.length === 0 ? (
              "No students found"
            ) : (
              <>
                Showing{" "}
                <span className="font-medium text-foreground">
                  {startIdx + 1}–{endIdx}
                </span>{" "}
                of{" "}
                <span className="font-medium text-foreground">
                  {filtered.length.toLocaleString()}
                </span>{" "}
                {filtered.length !== stats.total && (
                  <span className="text-xs">
                    (filtered from {stats.total.toLocaleString()} total)
                  </span>
                )}
              </>
            )}
          </p>
          <div className="flex items-center gap-2 text-sm">
            <span className="text-muted-foreground hidden sm:inline">
              Rows per page
            </span>
            <Select
              value={String(pageSize)}
              onValueChange={(v) => {
                setPageSize(Number(v));
                setPage(1);
              }}
            >
              <SelectTrigger className="h-8 w-16">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {PAGE_SIZE_OPTIONS.map((n) => (
                  <SelectItem key={n} value={String(n)}>
                    {n}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Student ID</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Program</TableHead>
                <TableHead>Year</TableHead>
                <TableHead>GPA</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {isLoading && (
                <TableRow>
                  <TableCell
                    colSpan={7}
                    className="text-center py-8 text-muted-foreground"
                  >
                    Loading…
                  </TableCell>
                </TableRow>
              )}
              {!isLoading && paginated.length === 0 && (
                <TableRow>
                  <TableCell
                    colSpan={7}
                    className="text-center py-8 text-muted-foreground"
                  >
                    No students found.
                  </TableCell>
                </TableRow>
              )}
              {paginated.map((s) => (
                <TableRow key={s.id}>
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
                    {s.gpa != null ? Number(s.gpa).toFixed(2) : "—"}
                  </TableCell>
                  <TableCell>
                    <StatusBadge status={s.status} />
                  </TableCell>
                  <TableCell className="text-right">
                    <div className="flex justify-end gap-1">
                      <Button
                        size="icon"
                        variant="ghost"
                        onClick={() => navigate(`/admin/students/${s.id}`)}
                      >
                        <Eye className="h-4 w-4" />
                      </Button>
                      <Button
                        size="icon"
                        variant="ghost"
                        onClick={() => navigate(`/admin/students/${s.id}/edit`)}
                      >
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button
                        size="icon"
                        variant="ghost"
                        className="text-destructive hover:text-destructive"
                        onClick={() => setDeleteTarget(s)}
                      >
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>

        {/* ── Pagination footer ── */}
        {!isLoading && filtered.length > 0 && (
          <div className="flex items-center justify-between px-4 py-3 border-t gap-2 flex-wrap">
            <p className="text-sm text-muted-foreground">
              Page{" "}
              <span className="font-medium text-foreground">{safePage}</span> of{" "}
              <span className="font-medium text-foreground">{totalPages}</span>
            </p>

            <div className="flex items-center gap-1">
              {/* First */}
              <Button
                size="icon"
                variant="outline"
                className="h-8 w-8"
                disabled={safePage === 1}
                onClick={() => setPage(1)}
              >
                <ChevronsLeft className="h-4 w-4" />
              </Button>
              {/* Prev */}
              <Button
                size="icon"
                variant="outline"
                className="h-8 w-8"
                disabled={safePage === 1}
                onClick={() => setPage((p) => p - 1)}
              >
                <ChevronLeft className="h-4 w-4" />
              </Button>

              {/* Page numbers */}
              {pageWindow.map((item, i) =>
                item === "…" ? (
                  <span
                    key={`ellipsis-${i}`}
                    className="px-2 text-sm text-muted-foreground select-none"
                  >
                    …
                  </span>
                ) : (
                  <Button
                    key={item}
                    size="icon"
                    variant={item === safePage ? "default" : "outline"}
                    className="h-8 w-8 text-sm"
                    onClick={() => setPage(item as number)}
                  >
                    {item}
                  </Button>
                ),
              )}

              {/* Next */}
              <Button
                size="icon"
                variant="outline"
                className="h-8 w-8"
                disabled={safePage === totalPages}
                onClick={() => setPage((p) => p + 1)}
              >
                <ChevronRight className="h-4 w-4" />
              </Button>
              {/* Last */}
              <Button
                size="icon"
                variant="outline"
                className="h-8 w-8"
                disabled={safePage === totalPages}
                onClick={() => setPage(totalPages)}
              >
                <ChevronsRight className="h-4 w-4" />
              </Button>
            </div>
          </div>
        )}
      </Card>

      {/* ── Delete dialog ── */}
      <Dialog open={!!deleteTarget} onOpenChange={() => setDeleteTarget(null)}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Student</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to delete{" "}
            <strong>
              {deleteTarget?.firstName} {deleteTarget?.lastName}
            </strong>
            ? This cannot be undone.
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
