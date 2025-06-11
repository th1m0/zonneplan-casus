import { useMemo } from "react";
import { parseApiDateTime, isSameHour, isSameDay } from "./dateUtils";
import type { ElectricityRatesResponse } from "~/types/ElectricityRateApiResponse";
import type { GasRatesResponse } from "~/types/GasRateApiResponse";

export const getCurrentElectricityPrice = (
  prices: ElectricityRatesResponse,
  currentTime: Date,
): ElectricityRatesResponse[number] | null => {
  return (
    prices.find((price) => {
      const startTime = parseApiDateTime(price.period_start);
      return isSameHour(startTime, currentTime);
    }) || null
  );
};

export const getUpcomingElectricityPrices = (
  prices: ElectricityRatesResponse,
): ElectricityRatesResponse => {
  const date = new Date();
  return prices.filter((p) => {
    const startTime = parseApiDateTime(p.period_start);
    startTime.setHours(startTime.getHours() - 1);
    return startTime > date;
  });
};

export const findCheapestPrice = (
  prices: ElectricityRatesResponse,
): ElectricityRatesResponse[number] | null => {
  if (prices.length === 0) return null;

  const minPrice = Math.min(...prices.map((p) => p.total_price_tax_included));
  return prices.find((p) => p.total_price_tax_included === minPrice)!;
};

export const findMostExpensivePrice = (
  prices: ElectricityRatesResponse,
): ElectricityRatesResponse[number] | null => {
  if (prices.length === 0) return null;

  const maxPrice = Math.max(...prices.map((p) => p.total_price_tax_included));
  return prices.find((p) => p.total_price_tax_included === maxPrice)!;
};

export const findMostSustainablePrice = (
  prices: ElectricityRatesResponse,
): ElectricityRatesResponse[number] | null => {
  if (prices.length === 0) return null;

  const maxSustainabilityScore = Math.max(
    ...prices.map((p) => p.sustainability_score ?? Number.MIN_VALUE),
  );

  return (
    prices.find((p) => p.sustainability_score === maxSustainabilityScore) ||
    null
  );
};

export const getCurrentGasPrice = (
  prices: GasRatesResponse,
): GasRatesResponse[number] | null => {
  const date = new Date();
  return (
    prices.find((price) => {
      const startTime = parseApiDateTime(price.period_start);
      return isSameDay(startTime, date);
    }) || null
  );
};
