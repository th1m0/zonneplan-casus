import { Card, CardContent, CardHeader, CardTitle } from "~/components/ui/card";
import type { LucideIcon } from "lucide-react";

interface HighlightedPriceCardProps {
  label: string;
  price?: number;
  unit: string;
  time: string;
  Icon: LucideIcon;
  iconColor?: string;
  sustainabilityScore?: number | null;
  isLoading?: boolean;
}

export function HighlightedPriceCard({
  label,
  price,
  unit,
  time,
  Icon,
  iconColor = "text-primary",
  sustainabilityScore,
  isLoading,
}: HighlightedPriceCardProps) {
  if (isLoading) {
    return (
      <Card>
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">{label}</CardTitle>
          <Icon className={`h-4 w-4 ${iconColor} animate-pulse`} />
        </CardHeader>
        <CardContent>
          <div className="bg-muted mb-1 h-6 w-20 animate-pulse rounded-md"></div>
          <div className="bg-muted mb-1 h-4 w-24 animate-pulse rounded-md"></div>
          {sustainabilityScore !== undefined && (
            <div className="bg-muted h-4 w-28 animate-pulse rounded-md"></div>
          )}
        </CardContent>
      </Card>
    );
  }

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium">{label}</CardTitle>
        <Icon className={`h-4 w-4 ${iconColor}`} />
      </CardHeader>
      <CardContent>
        <div className="text-xl font-bold">
          {!!price && price.toFixed(3)}{" "}
          <span className="text-muted-foreground text-xs">{unit}</span>
        </div>
        <p className="text-muted-foreground text-xs">om {time}</p>
      </CardContent>
    </Card>
  );
}
