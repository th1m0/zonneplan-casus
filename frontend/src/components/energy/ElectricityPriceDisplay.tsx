"use client";

import { useEffect, useState } from "react";
import { CurrentPriceCard } from "./CurrentPriceCard";
import { ElectricityPricesOverview } from "./ElectricityPricesOverview";
import { ElectricityPriceChart } from "./ElectricityPriceChart";
import { formatDisplayTime, parseApiDateTime } from "~/lib/dateUtils";
import { Bolt } from "lucide-react";
import type { useProcessedElectricityPrices } from "~/hooks/useEnergyData";
import type { ElectricityRatesResponse } from "~/types/ElectricityRateApiResponse";

export function ElectricityPriceDisplay({
  data,
  isLoading,
  upcoming,
}: Pick<
  ReturnType<typeof useProcessedElectricityPrices>,
  "data" | "isLoading"
> & {
  upcoming: boolean;
}) {
  const [selectedChartPrice, setSelectedChartPrice] = useState<
    ElectricityRatesResponse[number] | null
  >(data?.current ?? data?.allPrices?.[0] ?? null);

  useEffect(() => {
    console.log("useEffect data", data?.current);
    if (selectedChartPrice) return;

    if (data) {
      console.log("setting selectedChartPrice", data.current);
      setSelectedChartPrice(data?.current ?? data?.allPrices?.[0] ?? null);
    }
  }, [isLoading]);

  const actualCurrentPrice = data?.current;
  const displayPrice = selectedChartPrice || actualCurrentPrice;
  const unit = "€/kWh";

  const handleChartClick = (
    priceEntry: ElectricityRatesResponse[number] | null,
  ) => {
    console.log("setting selectedChartPrice", priceEntry);
    setSelectedChartPrice(priceEntry);
  };

  const handleResetToCurrent = () => {
    console.log("setting selectedChartPrice", data?.current || null);
    setSelectedChartPrice(data?.current || null);
  };

  const canGoBackToCurrent =
    data?.current &&
    selectedChartPrice?.period_start !== data?.current?.period_start;

  const cardTitle =
    selectedChartPrice &&
    selectedChartPrice.period_start === data?.current?.period_start
      ? "Huidige Elektriciteitsprijs"
      : "Geselecteerde Uurprijs";

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h2 className="flex items-center text-2xl font-semibold tracking-tight">
          <Bolt className="mr-2 h-6 w-6 text-yellow-500" />
          Elektriciteit
        </h2>
      </div>
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div className="lg:col-span-1">
          {isLoading && !displayPrice && (
            <CurrentPriceCard
              title={cardTitle}
              Icon={Bolt}
              iconColor="text-yellow-500"
              isLoading={true}
            />
          )}
          {displayPrice ? (
            <CurrentPriceCard
              title={cardTitle}
              price={displayPrice.prices_in_euros.total_price_tax_included}
              unit={unit}
              timeLabel={`Voor ${formatDisplayTime(displayPrice.period_start)} - ${formatDisplayTime(new Date(parseApiDateTime(displayPrice.period_start).getTime() + 60 * 60 * 1000))}`}
              Icon={Bolt}
              iconColor="text-yellow-500"
              isSelectedPrice={!!selectedChartPrice}
              {...(canGoBackToCurrent && {
                onResetToCurrent: handleResetToCurrent,
              })}
            />
          ) : (
            !isLoading && (
              <div className="text-muted-foreground flex h-full items-center justify-center rounded-lg border p-4 text-sm">
                {selectedChartPrice ? "Geselecteerde prijs" : "Huidige prijs"}{" "}
                niet beschikbaar.
              </div>
            )
          )}
        </div>
        <div className="md:col-span-2 lg:col-span-2">
          <ElectricityPriceChart
            data={data?.allPrices}
            isLoading={isLoading && !data}
            onChartClick={handleChartClick}
          />
        </div>
      </div>
      <ElectricityPricesOverview
        data={data}
        isLoading={isLoading}
        unit={unit}
        upcoming={upcoming}
      />
    </div>
  );
}
