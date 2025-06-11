"use client";

import { Card, CardContent, CardHeader, CardTitle } from "~/components/ui/card";
import { Button } from "~/components/ui/button";
import { Undo2 } from "lucide-react";
import type { LucideIcon } from "lucide-react";

type PriceCardProps = {
  title: string;
  iconColor?: string;
  Icon: LucideIcon;
} & (
  | {
      price: number;
      unit: string;
      timeLabel: string;
      isSelectedPrice?: boolean;
      onResetToCurrent?: () => void;
      isLoading?: false;
    }
  | {
      isLoading: true;
    }
);

export function PriceCard({
  title,
  Icon,
  iconColor = "text-primary",
  ...priceProps
}: PriceCardProps) {
  if (priceProps.isLoading) {
    return (
      <Card className="w-full">
        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle className="text-sm font-medium">{title}</CardTitle>
          <Icon className={`h-5 w-5 ${iconColor}`} />
        </CardHeader>
        <CardContent>
          <div className="bg-muted mb-1 h-8 w-24 animate-pulse rounded-md"></div>
          <div className="bg-muted h-4 w-32 animate-pulse rounded-md"></div>
        </CardContent>
      </Card>
    );
  }

  return (
    <Card className="w-full">
      <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
        <CardTitle className="text-sm font-medium">{title}</CardTitle>
        {priceProps.isSelectedPrice && priceProps.onResetToCurrent ? (
          <Button
            variant="ghost"
            size="sm"
            onClick={priceProps.onResetToCurrent}
            className="h-auto p-1 text-xs"
          >
            <Undo2 className="mr-1 h-3 w-3" />
            Huidige
          </Button>
        ) : (
          <Icon className={`h-5 w-5 ${iconColor}`} />
        )}
      </CardHeader>
      <CardContent>
        <div className="text-2xl font-bold">
          {priceProps.price.toFixed(3)}{" "}
          <span className="text-muted-foreground text-xs">
            {priceProps.unit}
          </span>
        </div>
        <p className="text-muted-foreground text-xs">{priceProps.timeLabel}</p>
      </CardContent>
    </Card>
  );
}
