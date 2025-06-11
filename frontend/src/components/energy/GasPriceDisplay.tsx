import { CurrentPriceCard } from "./CurrentPriceCard";
import { formatDisplayDate } from "~/lib/dateUtils";
import { Flame } from "lucide-react";
import type { useProcessedGasPrices } from "~/hooks/useEnergyData";

export function GasPriceDisplay({
  data,
  isLoading,
}: Pick<ReturnType<typeof useProcessedGasPrices>, "data" | "isLoading">) {
  const currentPrice = data?.current;
  const unit = "€/m³";

  return (
    <div className="space-y-6">
      <h2 className="flex items-center text-2xl font-semibold tracking-tight">
        <Flame className="mr-2 h-6 w-6 text-orange-500" />
        Gas
      </h2>
      {isLoading && !data && (
        <CurrentPriceCard
          title="Huidige Gasprijs"
          Icon={Flame}
          iconColor="text-orange-500"
          isLoading={true}
        />
      )}
      {currentPrice ? (
        <CurrentPriceCard
          title="Huidige Gasprijs"
          price={currentPrice.total_price_tax_included}
          unit={unit}
          timeLabel={`Voor vandaag, ${formatDisplayDate(currentPrice.period_start)}`}
          Icon={Flame}
          iconColor="text-orange-500"
        />
      ) : (
        !isLoading && (
          <p className="text-muted-foreground">
            Huidige gasprijs gegevens zijn niet beschikbaar voor vandaag.
          </p>
        )
      )}
    </div>
  );
}
