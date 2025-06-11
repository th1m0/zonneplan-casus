import { HighlightedPriceCard } from "./HighlightedPriceCard";
import { formatDisplayTime } from "~/lib/dateUtils";
import { TrendingDown, TrendingUp, Leaf } from "lucide-react";
import type { useProcessedElectricityPrices } from "~/hooks/useEnergyData";

export function ElectricityPricesOverview({
  data,
  isLoading,
  unit,
  upcoming,
}: Pick<
  ReturnType<typeof useProcessedElectricityPrices>,
  "data" | "isLoading"
> & {
  unit: string;
  upcoming: boolean;
}) {
  const cheapest = upcoming ? data?.cheapestUpcoming : data?.cheapestCurrent;
  const mostExpensive = upcoming
    ? data?.mostExpensiveUpcoming
    : data?.mostExpensiveCurrent;
  const mostSustainable = upcoming
    ? data?.mostSustainableUpcoming
    : data?.mostSustainableCurrent;

  if (isLoading && !data) {
    return (
      <div>
        <h3 className="mb-3 text-xl font-semibold tracking-tight">
          {(upcoming && "Komende Hoogtepunten (volgende 24u)") ||
            "Hoogtepunten"}
        </h3>
        <div className="grid gap-4 md:grid-cols-3">
          <HighlightedPriceCard
            label="Goedkoopste"
            unit={unit}
            time=""
            Icon={TrendingDown}
            iconColor="text-green-500"
            isLoading={true}
          />
          <HighlightedPriceCard
            label="Duurste"
            unit={unit}
            time=""
            Icon={TrendingUp}
            iconColor="text-red-500"
            isLoading={true}
          />
          <HighlightedPriceCard
            label="Meest Duurzaam"
            unit={unit}
            time=""
            Icon={Leaf}
            iconColor="text-teal-500"
            isLoading={true}
          />
        </div>
      </div>
    );
  }

  if (!cheapest && !mostExpensive && !mostSustainable && !isLoading) {
    return (
      <div>
        <h3 className="mb-3 text-xl font-semibold tracking-tight">
          {(upcoming && "Komende Hoogtepunten (volgende 24u)") ||
            "Hoogtepunten"}
        </h3>
        <p className="text-muted-foreground">
          Geen komende elektriciteitsprijs hoogtepunten beschikbaar.
        </p>
      </div>
    );
  }

  return (
    <div>
      <h3 className="mb-3 text-xl font-semibold tracking-tight">
        {(upcoming && "Komende Hoogtepunten (volgende 24u)") || "Hoogtepunten"}
      </h3>
      <div className="grid gap-4 md:grid-cols-3">
        {cheapest ? (
          <HighlightedPriceCard
            label="Goedkoopste"
            price={cheapest.prices_in_euros.total_price_tax_included}
            unit={unit}
            time={formatDisplayTime(cheapest.period_start)}
            Icon={TrendingDown}
            iconColor="text-green-500"
          />
        ) : (
          <div className="text-muted-foreground rounded-lg border p-4 text-sm">
            Geen goedkoopste prijs gegevens.
          </div>
        )}
        {mostExpensive ? (
          <HighlightedPriceCard
            label="Duurste"
            price={mostExpensive.prices_in_euros.total_price_tax_included}
            unit={unit}
            time={formatDisplayTime(mostExpensive.period_start)}
            Icon={TrendingUp}
            iconColor="text-red-500"
          />
        ) : (
          <div className="text-muted-foreground rounded-lg border p-4 text-sm">
            Geen duurste prijs gegevens.
          </div>
        )}
        {mostSustainable ? (
          <HighlightedPriceCard
            label="Meest Duurzaam"
            price={mostSustainable.prices_in_euros.total_price_tax_included}
            unit={unit}
            time={formatDisplayTime(mostSustainable.period_start)}
            sustainabilityScore={mostSustainable.sustainability_score}
            Icon={Leaf}
            iconColor="text-teal-500"
          />
        ) : (
          <div className="text-muted-foreground rounded-lg border p-4 text-sm">
            Geen meest duurzame prijs gegevens.
          </div>
        )}
      </div>
    </div>
  );
}
