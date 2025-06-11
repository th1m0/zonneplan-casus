"use client";

import { useState } from "react";
import { format, subDays } from "date-fns";
import { nl } from "date-fns/locale";
import { CalendarIcon, AlertCircle, RefreshCw, History } from "lucide-react";

import { Button } from "~/components/ui/button";
import { Calendar } from "~/components/ui/calendar";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "~/components/ui/popover";
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { ElectricityPriceDisplay } from "./ElectricityPriceDisplay";
import { GasPriceDisplay } from "./GasPriceDisplay";
import {
  useProcessedElectricityPrices,
  useProcessedGasPrices,
} from "~/hooks/useEnergyData";
import Link from "next/link";

export default function PriceHistoryDashboard() {
  const [selectedDate, setSelectedDate] = useState<Date | undefined>(
    subDays(new Date(), 1),
  );

  const electricityHook = useProcessedElectricityPrices(selectedDate);
  const gasHook = useProcessedGasPrices(selectedDate);

  const isLoading = electricityHook.isLoading || gasHook.isLoading;
  const isFetching = electricityHook.isFetching || gasHook.isFetching;
  const error = electricityHook.error || gasHook.error;

  const handleDateSelect = (date: Date | undefined) => {
    setSelectedDate(date);
  };

  const handleRefresh = () => {
    if (selectedDate) {
      electricityHook.refetch();
      gasHook.refetch();
    }
  };

  const electricityDataForDisplay =
    selectedDate && electricityHook.data?.allPrices.length === 0
      ? {
          ...electricityHook.data,
          current: null,
          cheapestUpcoming: null,
          mostExpensiveUpcoming: null,
          mostSustainableUpcoming: null,
        }
      : electricityHook.data;

  return (
    <div className="container mx-auto p-4 md:p-8">
      <div className="mb-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div className="flex items-center gap-2">
          <History className="text-primary h-8 w-8" />
          <h1 className="text-3xl font-bold tracking-tight">
            Prijsgeschiedenis
          </h1>
        </div>
        <div className="flex items-center gap-2">
          <Popover>
            <PopoverTrigger asChild>
              <Button
                variant={"outline"}
                className="w-[280px] justify-start text-left font-normal"
              >
                <CalendarIcon className="mr-2 h-4 w-4" />
                {selectedDate ? (
                  format(selectedDate, "PPP", { locale: nl })
                ) : (
                  <span>Kies een datum</span>
                )}
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0">
              <Calendar
                mode="single"
                selected={selectedDate}
                onSelect={handleDateSelect}
                disabled={(date) =>
                  date > new Date() || date < new Date("2000-01-01")
                }
                autoFocus
              />
            </PopoverContent>
          </Popover>
          <Button
            onClick={handleRefresh}
            variant="outline"
            size="icon"
            disabled={isFetching || !selectedDate}
            title="Vernieuwen"
          >
            <RefreshCw
              className={`h-4 w-4 ${isFetching ? "animate-spin" : ""}`}
            />
          </Button>
        </div>
      </div>

      {error && (
        <Alert variant="destructive" className="mb-6">
          <AlertCircle className="h-4 w-4" />
          <AlertTitle>Fout</AlertTitle>
          <AlertDescription>
            Kon gegevens niet laden voor{" "}
            {selectedDate ? format(selectedDate, "dd-MM-yyyy") : ""}:{" "}
            {error.message}.
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

      {!isLoading &&
        !error &&
        selectedDate &&
        electricityHook.data?.allPrices.length === 0 &&
        gasHook.data?.current === null && (
          <Alert className="mb-6">
            <AlertCircle className="h-4 w-4" />
            <AlertTitle>Geen gegevens</AlertTitle>
            <AlertDescription>
              Er zijn geen prijsgegevens gevonden voor{" "}
              {format(selectedDate, "dd MMMM yyyy", { locale: nl })}.
            </AlertDescription>
          </Alert>
        )}

      {selectedDate ? (
        <div className="grid gap-8 lg:grid-cols-1">
          <ElectricityPriceDisplay
            data={electricityDataForDisplay}
            isLoading={isLoading}
            upcoming={false}
          />
          <GasPriceDisplay data={gasHook.data} isLoading={isLoading} />
        </div>
      ) : (
        <p className="text-muted-foreground py-10 text-center">
          Selecteer een datum om prijzen te bekijken.
        </p>
      )}
      <div className="mt-8 text-center">
        <Button asChild variant="link">
          <Link href="/">Terug naar huidig dashboard</Link>
        </Button>
      </div>
    </div>
  );
}
