import { Badge } from '@/components/ui/badge';
import type { Skill } from '@/types';

export default function SkillBadge({ skill }: { skill: Skill }) {
  return (
    <Badge
      variant="secondary"
      className={skill.isAcademic
        ? 'bg-primary/10 text-primary border-primary/20'
        : 'bg-gray-100 text-gray-600 border-gray-200'}
    >
      {skill.name}
    </Badge>
  );
}
