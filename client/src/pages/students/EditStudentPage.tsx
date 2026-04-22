import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, X } from 'lucide-react';
import { getStudent, updateStudent } from '@/api/students';
import { getPrograms } from '@/api/programs';
import { getSkills } from '@/api/skills';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';

export default function EditStudentPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: student, isLoading } = useQuery({
    queryKey: ['students', Number(id)],
    queryFn: () => getStudent(Number(id)),
    enabled: !!id,
  });
  const { data: programs = [] } = useQuery({ queryKey: ['programs'], queryFn: getPrograms });
  const { data: skills = [] } = useQuery({ queryKey: ['skills'], queryFn: getSkills });

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    programId: '', yearLevel: '1', status: 'Active', dateEnrolled: '',
    dateGraduated: '', dateDropped: '', gpa: '',
  });
  const [selectedSkillIds, setSelectedSkillIds] = useState<number[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    if (student) {
      setForm({
        firstName: student.firstName ?? '',
        lastName: student.lastName ?? '',
        middleName: student.middleName ?? '',
        email: student.email ?? '',
        mobileNumber: student.mobileNumber ?? '',
        birthDate: student.birthDate ?? '',
        city: student.city ?? '',
        province: student.province ?? '',
        programId: String(student.programId),
        yearLevel: String(student.yearLevel),
        status: student.status,
        dateEnrolled: student.dateEnrolled ?? '',
        dateGraduated: student.dateGraduated ?? '',
        dateDropped: student.dateDropped ?? '',
        gpa: student.gpa != null ? String(student.gpa) : '',
      });
      setSelectedSkillIds(student.skills?.map(s => s.id) ?? []);
    }
  }, [student]);

  const mutation = useMutation({
    mutationFn: (payload: any) => updateStudent(Number(id), payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['students'] });
      queryClient.invalidateQueries({ queryKey: ['students', Number(id)] });
      navigate(`/admin/students/${id}`);
    },
    onError: () => setError('Failed to update student.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));
  const toggleSkill = (sid: number) => setSelectedSkillIds(prev => prev.includes(sid) ? prev.filter(x => x !== sid) : [...prev, sid]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate({
      ...form,
      programId: Number(form.programId),
      yearLevel: Number(form.yearLevel),
      gpa: form.gpa ? Number(form.gpa) : undefined,
      skillIds: selectedSkillIds,
    });
  };

  if (isLoading) return <Skeleton className="h-96 w-full" />;

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate(`/admin/students/${id}`)}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Edit Student</h1>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <Card>
          <CardHeader><CardTitle className="text-base">Personal Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            {[
              { key: 'firstName', label: 'First Name', required: true },
              { key: 'lastName', label: 'Last Name', required: true },
              { key: 'middleName', label: 'Middle Name' },
              { key: 'email', label: 'Email', type: 'email', required: true },
              { key: 'mobileNumber', label: 'Mobile Number' },
              { key: 'birthDate', label: 'Birth Date', type: 'date' },
              { key: 'city', label: 'City' },
              { key: 'province', label: 'Province' },
            ].map(({ key, label, type = 'text', required }) => (
              <div key={key} className="space-y-1">
                <Label htmlFor={key}>{label}{required && <span className="text-destructive ml-1">*</span>}</Label>
                <Input id={key} type={type} value={(form as any)[key]} onChange={e => set(key, e.target.value)} required={required} />
              </div>
            ))}
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Academic Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div className="space-y-1">
              <Label>Program <span className="text-destructive">*</span></Label>
              <Select value={form.programId} onValueChange={v => set('programId', v)}>
                <SelectTrigger><SelectValue placeholder="Select program" /></SelectTrigger>
                <SelectContent>
                  {programs.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Year Level</Label>
              <Select value={form.yearLevel} onValueChange={v => set('yearLevel', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  {[1,2,3,4].map(y => <SelectItem key={y} value={String(y)}>Year {y}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Status</Label>
              <Select value={form.status} onValueChange={v => set('status', v)}>
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  {['Active', 'Graduated', 'Dropped', 'Inactive'].map(s => <SelectItem key={s} value={s}>{s}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label htmlFor="gpa">GPA (1.0–3.0)</Label>
              <Input id="gpa" type="number" step="0.01" min="1" max="3" value={form.gpa} onChange={e => set('gpa', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="dateEnrolled">Date Enrolled</Label>
              <Input id="dateEnrolled" type="date" value={form.dateEnrolled} onChange={e => set('dateEnrolled', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="dateGraduated">Date Graduated</Label>
              <Input id="dateGraduated" type="date" value={form.dateGraduated} onChange={e => set('dateGraduated', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="dateDropped">Date Dropped</Label>
              <Input id="dateDropped" type="date" value={form.dateDropped} onChange={e => set('dateDropped', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Skills</CardTitle></CardHeader>
          <CardContent className="space-y-2">
            {selectedSkillIds.length > 0 && (
              <div className="flex flex-wrap gap-1 mb-2">
                {selectedSkillIds.map(sid => {
                  const skill = skills.find(s => s.id === sid);
                  return skill ? (
                    <Badge key={sid} variant="secondary" className="bg-primary/10 text-primary gap-1">
                      {skill.name}
                      <button type="button" onClick={() => toggleSkill(sid)} aria-label={`Remove ${skill.name}`}><X className="h-3 w-3" /></button>
                    </Badge>
                  ) : null;
                })}
              </div>
            )}
            <div className="flex flex-wrap gap-1">
              {skills.filter(s => !selectedSkillIds.includes(s.id)).map(s => (
                <Badge key={s.id} variant="outline" className="cursor-pointer hover:bg-primary/10" onClick={() => toggleSkill(s.id)}>
                  {s.name}
                </Badge>
              ))}
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2 justify-end">
          <Button type="button" variant="outline" onClick={() => navigate(`/admin/students/${id}`)}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Save Changes'}
          </Button>
        </div>
      </form>
    </div>
  );
}
