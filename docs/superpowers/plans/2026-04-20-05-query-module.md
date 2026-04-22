# Query Module Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the Query/Filtering page with two working skill-based queries — "Students by Skill" and "Students by Program + Skill" — each displaying results in a table.

**Architecture:** Single QueryPage component with two independent query panels. Each panel has its own local state for selected filters and triggers a React Query fetch only when filters are selected (enabled flag). Results render in shadcn Tables below each panel. Uses the existing `GET /students?skillId=X` and `GET /students?skillId=X&programId=Y` endpoints added in Plan 02.

**Tech Stack:** React 19, TypeScript, React Query v5, shadcn/ui

**Prerequisites:**
- Plan 01 (Foundation) complete
- Plan 02 (Backend Additions) complete — skillId filter on `/students` must work

---

### Task 1: Build the Query page

**Files:**
- Modify: `client/src/pages/QueryPage.tsx`

- [ ] **Step 1: Write QueryPage**

Replace `client/src/pages/QueryPage.tsx`:
```tsx
import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { Search } from 'lucide-react';
import { getStudents } from '@/api/students';
import { getSkills } from '@/api/skills';
import { getPrograms } from '@/api/programs';
import { Student } from '@/types';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import StatusBadge from '@/components/shared/StatusBadge';

function ResultsTable({ students, isLoading }: { students: Student[]; isLoading: boolean }) {
  if (isLoading) {
    return (
      <div className="space-y-2 mt-4">
        {Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-10 w-full" />)}
      </div>
    );
  }

  if (students.length === 0) {
    return <p className="text-sm text-muted-foreground mt-4 text-center py-4">No students match this query.</p>;
  }

  return (
    <Table className="mt-4">
      <TableHeader>
        <TableRow>
          <TableHead>Student ID</TableHead>
          <TableHead>Name</TableHead>
          <TableHead>Program</TableHead>
          <TableHead>Year</TableHead>
          <TableHead>GPA</TableHead>
          <TableHead>Status</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {students.map((s) => (
          <TableRow key={s.id}>
            <TableCell className="font-mono text-sm">{s.studentId}</TableCell>
            <TableCell className="font-medium">{s.firstName} {s.lastName}</TableCell>
            <TableCell className="text-sm text-muted-foreground">{s.programName}</TableCell>
            <TableCell>{s.yearLevel}</TableCell>
            <TableCell>{s.gpa?.toFixed(2) ?? '—'}</TableCell>
            <TableCell><StatusBadge status={s.status} /></TableCell>
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}

export default function QueryPage() {
  const { data: skills = [] } = useQuery({ queryKey: ['skills'], queryFn: getSkills });
  const { data: programs = [] } = useQuery({ queryKey: ['programs'], queryFn: getPrograms });

  // Query 1: Students by Skill
  const [skillId1, setSkillId1] = useState<string>('');
  const [runQuery1, setRunQuery1] = useState(false);

  const query1 = useQuery({
    queryKey: ['students', 'query1', skillId1],
    queryFn: () => getStudents({ skillId: Number(skillId1) }),
    enabled: runQuery1 && !!skillId1,
  });

  const handleRunQuery1 = () => {
    if (!skillId1) return;
    setRunQuery1(true);
  };

  const handleResetQuery1 = () => {
    setSkillId1('');
    setRunQuery1(false);
  };

  // Query 2: Students by Program + Skill
  const [skillId2, setSkillId2] = useState<string>('');
  const [programId2, setProgramId2] = useState<string>('');
  const [runQuery2, setRunQuery2] = useState(false);

  const query2 = useQuery({
    queryKey: ['students', 'query2', skillId2, programId2],
    queryFn: () => getStudents({ skillId: Number(skillId2), programId: Number(programId2) }),
    enabled: runQuery2 && !!skillId2 && !!programId2,
  });

  const handleRunQuery2 = () => {
    if (!skillId2 || !programId2) return;
    setRunQuery2(true);
  };

  const handleResetQuery2 = () => {
    setSkillId2('');
    setProgramId2('');
    setRunQuery2(false);
  };

  const selectedSkill1 = skills.find(s => String(s.id) === skillId1);
  const selectedSkill2 = skills.find(s => String(s.id) === skillId2);
  const selectedProgram2 = programs.find(p => String(p.id) === programId2);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Skill Query</h1>
        <p className="text-sm text-muted-foreground mt-1">Filter students by skill or by program and skill combination.</p>
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {/* Query 1: Students by Skill */}
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <div className="p-1.5 rounded bg-primary/10">
                <Search className="h-4 w-4 text-primary" />
              </div>
              <div>
                <CardTitle className="text-base">Query 1 — Students by Skill</CardTitle>
                <CardDescription className="text-xs mt-0.5">Find all students who have a specific skill</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="flex gap-2 flex-wrap">
              <Select value={skillId1} onValueChange={(v) => { setSkillId1(v); setRunQuery1(false); }}>
                <SelectTrigger className="w-64">
                  <SelectValue placeholder="Select a skill…" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="" disabled>— Academic Skills —</SelectItem>
                  {skills.filter(s => s.isAcademic).map(s => (
                    <SelectItem key={s.id} value={String(s.id)}>
                      <span className="flex items-center gap-1.5">
                        <Badge variant="secondary" className="text-[10px] px-1 py-0 bg-primary/10 text-primary">Academic</Badge>
                        {s.name}
                      </span>
                    </SelectItem>
                  ))}
                  <SelectItem value="" disabled>— Soft Skills —</SelectItem>
                  {skills.filter(s => !s.isAcademic).map(s => (
                    <SelectItem key={s.id} value={String(s.id)}>
                      <span className="flex items-center gap-1.5">
                        <Badge variant="secondary" className="text-[10px] px-1 py-0">Soft</Badge>
                        {s.name}
                      </span>
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <Button onClick={handleRunQuery1} disabled={!skillId1}>
                <Search className="h-4 w-4 mr-1" /> Search
              </Button>
              {runQuery1 && (
                <Button variant="outline" onClick={handleResetQuery1}>Reset</Button>
              )}
            </div>

            {runQuery1 && selectedSkill1 && (
              <div className="mt-3 flex items-center gap-2 text-sm">
                <span className="text-muted-foreground">Showing students with skill:</span>
                <Badge variant="secondary" className={selectedSkill1.isAcademic ? 'bg-primary/10 text-primary' : ''}>
                  {selectedSkill1.name}
                </Badge>
                {!query1.isLoading && (
                  <span className="text-muted-foreground">({query1.data?.length ?? 0} result{query1.data?.length !== 1 ? 's' : ''})</span>
                )}
              </div>
            )}

            {runQuery1 && (
              <ResultsTable students={query1.data ?? []} isLoading={query1.isLoading} />
            )}
          </CardContent>
        </Card>

        {/* Query 2: Students by Program + Skill */}
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2">
              <div className="p-1.5 rounded bg-primary/10">
                <Search className="h-4 w-4 text-primary" />
              </div>
              <div>
                <CardTitle className="text-base">Query 2 — Students by Program + Skill</CardTitle>
                <CardDescription className="text-xs mt-0.5">Find students in a specific program who have a specific skill</CardDescription>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="flex gap-2 flex-wrap">
              <Select value={programId2} onValueChange={(v) => { setProgramId2(v); setRunQuery2(false); }}>
                <SelectTrigger className="w-52">
                  <SelectValue placeholder="Select program…" />
                </SelectTrigger>
                <SelectContent>
                  {programs.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
              <Select value={skillId2} onValueChange={(v) => { setSkillId2(v); setRunQuery2(false); }}>
                <SelectTrigger className="w-52">
                  <SelectValue placeholder="Select skill…" />
                </SelectTrigger>
                <SelectContent>
                  {skills.filter(s => s.isAcademic).map(s => (
                    <SelectItem key={s.id} value={String(s.id)}>
                      <span className="flex items-center gap-1.5">
                        <Badge variant="secondary" className="text-[10px] px-1 py-0 bg-primary/10 text-primary">Academic</Badge>
                        {s.name}
                      </span>
                    </SelectItem>
                  ))}
                  {skills.filter(s => !s.isAcademic).map(s => (
                    <SelectItem key={s.id} value={String(s.id)}>
                      <span className="flex items-center gap-1.5">
                        <Badge variant="secondary" className="text-[10px] px-1 py-0">Soft</Badge>
                        {s.name}
                      </span>
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <Button onClick={handleRunQuery2} disabled={!skillId2 || !programId2}>
                <Search className="h-4 w-4 mr-1" /> Search
              </Button>
              {runQuery2 && (
                <Button variant="outline" onClick={handleResetQuery2}>Reset</Button>
              )}
            </div>

            {runQuery2 && selectedSkill2 && selectedProgram2 && (
              <div className="mt-3 flex items-center gap-2 text-sm flex-wrap">
                <span className="text-muted-foreground">Showing</span>
                <Badge variant="outline">{selectedProgram2.name}</Badge>
                <span className="text-muted-foreground">students with skill:</span>
                <Badge variant="secondary" className={selectedSkill2.isAcademic ? 'bg-primary/10 text-primary' : ''}>
                  {selectedSkill2.name}
                </Badge>
                {!query2.isLoading && (
                  <span className="text-muted-foreground">({query2.data?.length ?? 0} result{query2.data?.length !== 1 ? 's' : ''})</span>
                )}
              </div>
            )}

            {runQuery2 && (
              <ResultsTable students={query2.data ?? []} isLoading={query2.isLoading} />
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Verify Query 1 in browser**

Navigate to `/admin/query`. Select any skill from the Query 1 dropdown and click Search. Expected: table appears below with students who have that skill. Count label shows number of results. Reset clears the table.

- [ ] **Step 3: Verify Query 2 in browser**

Select a program (e.g. "BS Information Technology") and a skill (e.g. "Python Programming") in Query 2. Click Search. Expected: table shows only BSIT students who have Python Programming. Results should be a subset of Query 1 results for the same skill.

- [ ] **Step 4: Verify empty state**

Select a skill that no students have (or an unlikely combination of program + skill). Expected: "No students match this query." message appears instead of an empty table.

- [ ] **Step 5: Commit**
```bash
git add client/src/pages/QueryPage.tsx
git commit -m "feat: implement skill query page with two filter panels and results tables"
```
