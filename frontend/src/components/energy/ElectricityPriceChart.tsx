"use client";

import {
  Area,
  AreaChart,
  CartesianGrid,
  ResponsiveContainer,
  XAxis,
  YAxis,
} from "recharts";
import type { CategoricalChartFunc } from "recharts/types/chart/generateCategoricalChart";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "~/components/ui/card";
import {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
  type ChartConfig,
} from "~/components/ui/chart";
import { formatDisplayTime, parseApiDateTime } from "~/lib/dateUtils";
import { useMemo } from "react";
import type { ElectricityRatesResponse } from "~/types/ElectricityRateApiResponse";

const chartConfig = {
  price: {
    label: "Prijs",
    color: "var(--chart-1)",
  },
} satisfies ChartConfig;

export function ElectricityPriceChart({
  data,
  isLoading,
  onChartClick,
}: {
  data: ElectricityRatesResponse | undefined;
  isLoading: boolean;
  onChartClick?: (priceEntry: ElectricityRatesResponse[number] | null) => void;
}) {
  const chartData = useMemo(() => {
    if (!data) return [];
    const sortedData = [...data].sort(
      (a, b) =>
        parseApiDateTime(a.period_start).getTime() -
        parseApiDateTime(b.period_start).getTime(),
    );
    return sortedData.map((price) => ({
      time: formatDisplayTime(price.period_start),
      price: price.prices_in_euros.total_price_tax_included.toFixed(3),
      originalEntry: price,
    }));
  }, [data]);

  const handleChartClick: CategoricalChartFunc = (event) => {
    if (
      event?.activePayload &&
      event.activePayload.length > 0 &&
      onChartClick
    ) {
      // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
      const clickedData = event?.activePayload?.[0]?.payload as {
        originalEntry?: ElectricityRatesResponse[number];
      };
      if (clickedData?.originalEntry) {
        onChartClick(clickedData.originalEntry);
      }
    }
  };

  if (isLoading) {
    return (
      <Card>
        <CardHeader>
          <div className="bg-muted mb-2 h-6 w-48 animate-pulse rounded-md"></div>
          <div className="bg-muted h-4 w-64 animate-pulse rounded-md"></div>
        </CardHeader>
        <CardContent>
          <div className="bg-muted h-[300px] w-full animate-pulse rounded-md"></div>
        </CardContent>
      </Card>
    );
  }

  if (!data || data.length === 0) {
    return (
      <Card>
        <CardHeader>
          <CardTitle>Prijsgrafiek per uur</CardTitle>
          <CardDescription>
            Visualisatie van de elektriciteitsprijs gedurende de dag.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="text-muted-foreground flex h-[300px] items-center justify-center">
            Geen prijsgegevens beschikbaar om een grafiek te tonen.
          </div>
        </CardContent>
      </Card>
    );
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Prijsgrafiek per uur</CardTitle>
        <CardDescription>
          Klik op de grafiek om een specifieke uurprijs te bekijken.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <ChartContainer config={chartConfig} className="h-[300px] w-full">
          <ResponsiveContainer>
            <AreaChart
              data={chartData}
              margin={{ top: 10, right: 30, left: 0, bottom: 0 }}
              onClick={handleChartClick}
              className={onChartClick ? "cursor-pointer" : ""}
            >
              <defs>
                <linearGradient id="colorPrice" x1="0" y1="0" x2="0" y2="1">
                  <stop
                    offset="5%"
                    stopColor="var(--color-price)"
                    stopOpacity={0.8}
                  />
                  <stop
                    offset="95%"
                    stopColor="var(--color-price)"
                    stopOpacity={0}
                  />
                </linearGradient>
              </defs>
              <CartesianGrid strokeDasharray="3 3" vertical={false} />
              <XAxis
                dataKey="time"
                tickLine={false}
                axisLine={false}
                tickMargin={8}
                tickFormatter={(value, index) => {
                  if (index % 3 === 0) {
                    return value as string;
                  }
                  return "";
                }}
              />
              <YAxis
                tickFormatter={(value) => `€${value}`}
                tickLine={false}
                axisLine={false}
                tickMargin={8}
                width={80}
              />
              <ChartTooltip
                cursor={true}
                content={
                  <ChartTooltipContent
                    formatter={(value) => [`€ ${value.toString()}`, " per kWh"]}
                    labelFormatter={(label) => `Tijd: ${label}`}
                  />
                }
              />
              <Area
                type="monotone"
                dataKey="price"
                stroke="var(--color-price)"
                fillOpacity={1}
                fill="url(#colorPrice)"
                strokeWidth={2}
                activeDot={{ r: 6 }}
              />
            </AreaChart>
          </ResponsiveContainer>
        </ChartContainer>
      </CardContent>
    </Card>
  );
}
