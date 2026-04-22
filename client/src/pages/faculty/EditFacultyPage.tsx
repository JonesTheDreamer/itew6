import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { getFacultyMember, updateFaculty } from '@/api/faculty';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';

export default function EditFacultyPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: member, isLoading } = useQuery({
    queryKey: ['faculty', Number(id)],
    queryFn: () => getFacultyMember(Number(id)),
    enabled: !!id,
  });

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    department: '', position: '', employmentType: '', employmentDate: '',
  });
  const [error, setError] = useState('');

  useEffect(() => {
    if (member) {
      setForm({
        firstName: member.firstName ?? '',
        lastName: member.lastName ?? '',
        middleName: member.middleName ?? '',
        email: member.email ?? '',
        mobileNumber: member.mobileNumber ?? '',
        birthDate: member.birthDate ?? '',
        city: member.city ?? '',
        province: member.province ?? '',
        department: member.department ?? '',
        position: member.position ?? '',
        employmentType: member.employmentType ?? '',
        employmentDate: member.employmentDate ?? '',
      });
    }
  }, [member]);

  const mutation = useMutation({
    mutationFn: (payload: any) => updateFaculty(Number(id), payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['faculty'] });
      queryClient.invalidateQueries({ queryKey: ['faculty', Number(id)] });
      navigate(`/admin/faculty/${id}`);
    },
    onError: () => setError('Failed to update faculty.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate(form);
  };

  if (isLoading) return <Skeleton className="h-96 w-full" />;

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate(`/admin/faculty/${id}`)}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Edit Faculty</h1>
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
          <CardHeader><CardTitle className="text-base">Faculty Information</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <div className="space-y-1">
              <Label htmlFor="department">Department</Label>
              <Input id="department" value={form.department} onChange={e => set('department', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label htmlFor="position">Position</Label>
              <Input id="position" value={form.position} onChange={e => set('position', e.target.value)} />
            </div>
            <div className="space-y-1">
              <Label>Employment Type</Label>
              <Select value={form.employmentType} onValueChange={v => set('employmentType', v)}>
                <SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger>
                <SelectContent>
                  {['Full-Time', 'Part-Time', 'Contractual'].map(t => <SelectItem key={t} value={t}>{t}</SelectItem>)}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1">
              <Label htmlFor="employmentDate">Employment Date</Label>
              <Input id="employmentDate" type="date" value={form.employmentDate} onChange={e => set('employmentDate', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2 justify-end">
          <Button type="button" variant="outline" onClick={() => navigate(`/admin/faculty/${id}`)}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Save Changes'}
          </Button>
        </div>
      </form>
    </div>
  );
}
