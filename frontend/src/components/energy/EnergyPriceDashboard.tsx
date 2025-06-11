"use client";

import { useEnergyData } from "~/hooks/useEnergyData";
import { ElectricityPriceDisplay } from "./ElectricityPriceDisplay";
import { GasPriceDisplay } from "./GasPriceDisplay";
import { Button } from "~/components/ui/button";
import { AlertCircle, RefreshCw, Wifi, WifiOff, History } from "lucide-react";
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { format } from "date-fns";
import { nl } from "date-fns/locale";
import Link from "next/link";

export default function EnergyPriceDashboard() {
  const {
    electricity,
    gas,
    isLoading,
    isFetching,
    error,
    refetch,
    lastUpdated,
  } = useEnergyData();

  const handleRefresh = () => {
    refetch();
  };

  const isOnline = typeof navigator !== "undefined" ? navigator.onLine : true;

  return (
    <div className="container mx-auto p-4 md:p-8">
      <div className="mb-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div className="flex items-center gap-2">
          <h1 className="text-3xl font-bold tracking-tight">
            Zonneplan Energieprijzen
          </h1>
          {!isOnline && <WifiOff className="text-muted-foreground h-5 w-5" />}
          {isOnline && !isFetching && !error && (
            <Wifi className="h-5 w-5 text-green-500" />
          )}
        </div>
        <div className="flex items-center gap-2">
          <Button asChild variant="outline" size="sm">
            <Link href="/historie">
              <History className="mr-2 h-4 w-4" />
              Prijsgeschiedenis
            </Link>
          </Button>
          {lastUpdated && !isFetching && (
            <span className="text-muted-foreground text-xs">
              Laatst bijgewerkt:{" "}
              {format(lastUpdated, "d MMM yyyy HH:mm:ss", { locale: nl })}
            </span>
          )}
          <div className="flex gap-1">
            <Button
              onClick={handleRefresh}
              variant="outline"
              size="sm"
              disabled={isFetching}
            >
              <RefreshCw
                className={`mr-2 h-4 w-4 ${isFetching ? "animate-spin" : ""}`}
              />
              Vernieuwen
            </Button>
          </div>
        </div>
      </div>

      {error && (
        <Alert variant="destructive" className="mb-6">
          <AlertCircle className="h-4 w-4" />
          <AlertTitle>Fout</AlertTitle>
          <AlertDescription>
            Kon energiegegevens niet laden: {error}.
            {!isOnline && " Controleer je internetverbinding."}
            <Button
              onClick={handleRefresh}
              variant="link"
              size="sm"
              className="ml-2 h-auto p-0"
            >
              Opnieuw proberen
            </Button>
          </AlertDescription>
        </Alert>
      )}

      {!isOnline && (
        <Alert className="mb-6">
          <WifiOff className="h-4 w-4" />
          <AlertTitle>Offline</AlertTitle>
          <AlertDescription>
            Je bent momenteel offline. De getoonde gegevens kunnen verouderd
            zijn.
          </AlertDescription>
        </Alert>
      )}

      <div className="grid gap-8 lg:grid-cols-1">
        <ElectricityPriceDisplay
          data={electricity}
          isLoading={isLoading}
          upcoming={true}
        />
        <GasPriceDisplay data={gas} isLoading={isLoading} />
      </div>
    </div>
  );
}
