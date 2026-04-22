import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { ArrowLeft } from 'lucide-react';
import { createOrganization } from '@/api/organizations';
import { getColleges } from '@/api/colleges';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function AddOrganizationPage() {
  const navigate = useNavigate();
  const queryClient = useQueryClient();

  const { data: colleges = [] } = useQuery({
    queryKey: ['colleges'],
    queryFn: getColleges,
  });

  const [form, setForm] = useState({
    organizationName: '',
    organizationDescription: '',
    dateCreated: '',
    collegeId: '',
  });
  const [error, setError] = useState('');

  const mutation = useMutation({
    mutationFn: createOrganization,
    onSuccess: (org) => {
      setError('');
      queryClient.invalidateQueries({ queryKey: ['organizations'] });
      navigate(`/admin/organizations/${org.id}`);
    },
    onError: () => setError('Failed to create organization. Check required fields.'),
  });

  const set = (key: keyof typeof form, value: string) => {
    setError('');
    setForm(f => ({ ...f, [key]: value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    mutation.mutate({
      organizationName: form.organizationName,
      organizationDescription: form.organizationDescription || null,
      dateCreated: form.dateCreated || null,
      collegeId: form.collegeId ? Number(form.collegeId) : null,
      isActive: true,
    });
  };

  return (
    <div className="space-y-4 max-w-2xl">
      <div className="flex items-center gap-2">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/organizations')}>
          <ArrowLeft className="h-4 w-4 mr-1" /> Back
        </Button>
        <h1 className="text-2xl font-bold">Add Organization</h1>
      </div>

      <form onSubmit={handleSubmit} className="space-y-4">
        <Card>
          <CardHeader>
            <CardTitle className="text-base">Organization Details</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-1">
              <Label htmlFor="orgName">
                Organization Name <span className="text-destructive">*</span>
              </Label>
              <Input
                id="orgName"
                value={form.organizationName}
                onChange={e => set('organizationName', e.target.value)}
                required
              />
            </div>

            <div className="space-y-1">
              <Label htmlFor="orgDesc">Description</Label>
              <Textarea
                id="orgDesc"
                value={form.organizationDescription}
                onChange={e => set('organizationDescription', e.target.value)}
                rows={3}
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <Label htmlFor="dateCreated">Date Created</Label>
                <Input
                  id="dateCreated"
                  type="date"
                  value={form.dateCreated}
                  onChange={e => set('dateCreated', e.target.value)}
                />
              </div>
              <div className="space-y-1">
                <Label>College</Label>
                <Select value={form.collegeId} onValueChange={v => set('collegeId', v)}>
                  <SelectTrigger>
                    <SelectValue placeholder="Select college (optional)" />
                  </SelectTrigger>
                  <SelectContent>
                    {colleges.map(c => (
                      <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>

        {error && <p className="text-sm text-destructive">{error}</p>}

        <div className="flex gap-2">
          <Button type="submit" disabled={mutation.isPending}>
            {mutation.isPending ? 'Creating…' : 'Create Organization'}
          </Button>
          <Button type="button" variant="outline" onClick={() => navigate('/admin/organizations')}>
            Cancel
          </Button>
        </div>
      </form>
    </div>
  );
}
