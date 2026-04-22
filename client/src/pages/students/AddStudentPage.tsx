import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft, X } from 'lucide-react';
import { createStudent } from '@/api/students';
import { getPrograms } from '@/api/programs';
import { getSkills } from '@/api/skills';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

export default function AddStudentPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: programs = [] } = useQuery({ queryKey: ['programs'], queryFn: getPrograms });
  const { data: skills = [] } = useQuery({ queryKey: ['skills'], queryFn: getSkills });

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    programId: '', yearLevel: '1', status: 'Active', dateEnrolled: '',
  });
  const [selectedSkillIds, setSelectedSkillIds] = useState<number[]>([]);
  const [error, setError] = useState('');

  const mutation = useMutation({
    mutationFn: createStudent,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['students'] });
      navigate('/admin/students');
    },
    onError: () => setError('Failed to create student. Check all required fields.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const toggleSkill = (id: number) => {
    setSelectedSkillIds(prev =>
      prev.includes(id) ? prev.filter(s => s !== id) : [...prev, id]
    );
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate({
      ...form,
      programId: Number(form.programId),
      yearLevel: Number(form.yearLevel),
      skillIds: selectedSkillIds,
    });
  };

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/students')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Add Student</h1>
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
              <Select value={form.programId} onValueChange={v => set('programId', v)} required>
                <SelectTrigger><SelectValue placeholder="Select program" /></SelectTrigger>
                <SelectContent>
                  {programs.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.name}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label>Year Level <span className="text-destructive">*</span></Label>
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
              <Label htmlFor="dateEnrolled">Date Enrolled</Label>
              <Input id="dateEnrolled" type="date" value={form.dateEnrolled} onChange={e => set('dateEnrolled', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle className="text-base">Skills</CardTitle></CardHeader>
          <CardContent className="space-y-2">
            {selectedSkillIds.length > 0 && (
              <div className="flex flex-wrap gap-1 mb-2">
                {selectedSkillIds.map(id => {
                  const skill = skills.find(s => s.id === id);
                  return skill ? (
                    <Badge key={id} variant="secondary" className="bg-primary/10 text-primary gap-1">
                      {skill.name}
                      <button type="button" onClick={() => toggleSkill(id)} aria-label={`Remove ${skill.name}`}><X className="h-3 w-3" /></button>
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
          <Button type="button" variant="outline" onClick={() => navigate('/admin/students')}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Add Student'}
          </Button>
        </div>
      </form>
    </div>
  );
}
