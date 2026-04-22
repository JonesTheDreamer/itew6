import { Badge } from '@/components/ui/badge';

const statusConfig: Record<string, string> = {
  Active: 'bg-green-100 text-green-800 border-green-200',
  Enrolled: 'bg-green-100 text-green-800 border-green-200',
  Graduated: 'bg-blue-100 text-blue-800 border-blue-200',
  Dropped: 'bg-red-100 text-red-800 border-red-200',
  Inactive: 'bg-gray-100 text-gray-600 border-gray-200',
};

export default function StatusBadge({ status }: { status: string }) {
  const classes = statusConfig[status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
  return <Badge variant="outline" className={classes}>{status}</Badge>;
}
