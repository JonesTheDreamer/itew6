import { useQuery } from "@tanstack/react-query";
import { getFacultyMember } from "../../api/faculty";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export default function FacultyAwardsPage() {
  const userRaw = localStorage.getItem("auth_user");
  const user = userRaw ? JSON.parse(userRaw) : null;
  const facultyId = user?.profile?.id as number | undefined;

  const { data: member, isLoading } = useQuery({
    queryKey: ["faculty", facultyId],
    queryFn: () => getFacultyMember(facultyId!),
    enabled: !!facultyId,
  });

  return (
    <div className="space-y-4">
      <h1 className="text-2xl font-bold">My Awards</h1>
      <Card>
        <CardHeader>
          <CardTitle className="text-base">Awards & Recognition</CardTitle>
        </CardHeader>
        <CardContent>
          {isLoading ? (
            <div className="space-y-2">
              <Skeleton className="h-12 w-full" />
              <Skeleton className="h-12 w-full" />
            </div>
          ) : !member?.awards || member.awards.length === 0 ? (
            <p className="text-sm text-muted-foreground">No awards recorded.</p>
          ) : (
            <ul className="space-y-3">
              {member.awards.map((a) => (
                <li
                  key={a.id}
                  className="border-l-2 border-primary pl-3 text-sm"
                >
                  <p className="font-medium">{a.title}</p>
                  {a.awardingOrganization && (
                    <p className="text-muted-foreground">
                      {a.awardingOrganization}
                      {a.awardingDate ? ` · ${a.awardingDate}` : ""}
                    </p>
                  )}
                  {a.awardingLocation && (
                    <p className="text-muted-foreground text-xs">
                      {a.awardingLocation}
                    </p>
                  )}
                </li>
              ))}
            </ul>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
