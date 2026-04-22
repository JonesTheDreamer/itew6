import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { createFaculty } from '@/api/faculty';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function AddFacultyPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const [form, setForm] = useState({
    firstName: '', lastName: '', middleName: '', email: '',
    mobileNumber: '', birthDate: '', city: '', province: '',
    department: '', position: '', employmentType: '', employmentDate: '',
  });
  const [error, setError] = useState('');

  const mutation = useMutation({
    mutationFn: createFaculty,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['faculty'] });
      navigate('/admin/faculty');
    },
    onError: () => setError('Failed to create faculty. Check all required fields.'),
  });

  const set = (key: string, value: string) => setForm(f => ({ ...f, [key]: value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate(form as any);
  };

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/faculty')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Add Faculty</h1>
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
              <Input id="department" value={form.department} onChange={e => set('department', e.target.value)} placeholder="e.g. Information Technology" />
            </div>
            <div className="space-y-1">
              <Label htmlFor="position">Position</Label>
              <Input id="position" value={form.position} onChange={e => set('position', e.target.value)} placeholder="e.g. Associate Professor" />
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
          <Button type="button" variant="outline" onClick={() => navigate('/admin/faculty')}>Cancel</Button>
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Saving…' : 'Add Faculty'}
          </Button>
        </div>
      </form>
    </div>
  );
}
