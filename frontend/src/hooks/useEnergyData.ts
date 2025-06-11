"use client";

import { useQuery, type QueryObserverResult } from "@tanstack/react-query";
import { useMemo } from "react";
import {
  fetchElectricityPrices,
  fetchGasPrices,
  energyQueryKeys,
} from "~/services/energyService";
import {
  getCurrentElectricityPrice,
  getUpcomingElectricityPrices,
  findCheapestPrice,
  findMostExpensivePrice,
  findMostSustainablePrice,
  getCurrentGasPrice,
} from "~/lib/priceCalculations";
import type { ElectricityRatesResponse } from "~/types/ElectricityRateApiResponse";
import type { GasRatesResponse } from "~/types/GasRateApiResponse";

export const useElectricityPrices = (date?: Date) => {
  const queryKey = energyQueryKeys.electricity(date);
  return useQuery({
    queryKey: queryKey,
    queryFn: () => fetchElectricityPrices(date),
    staleTime: date ? Number.POSITIVE_INFINITY : 5 * 60 * 1000,
    refetchInterval: date ? false : 5 * 60 * 1000,
    retry: 3,
    retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000),
  });
};

export const useGasPrices = (date?: Date) => {
  const queryKey = energyQueryKeys.gas(date);
  return useQuery({
    queryKey: queryKey,
    queryFn: () => fetchGasPrices(date),
    staleTime: date ? Number.POSITIVE_INFINITY : 15 * 60 * 1000,
    refetchInterval: date ? false : 15 * 60 * 1000,
    retry: 3,
    retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000),
  });
};

export const useProcessedElectricityPrices = (
  date?: Date,
): {
  data: {
    current: ElectricityRatesResponse[number] | null;
    upcoming: ElectricityRatesResponse | null;
    cheapestUpcoming: ElectricityRatesResponse[number] | null;
    mostExpensiveUpcoming: ElectricityRatesResponse[number] | null;
    mostSustainableUpcoming: ElectricityRatesResponse[number] | null;
    cheapestCurrent: ElectricityRatesResponse[number] | null;
    mostExpensiveCurrent: ElectricityRatesResponse[number] | null;
    mostSustainableCurrent: ElectricityRatesResponse[number] | null;
    allPrices: ElectricityRatesResponse;
  } | null;
  isLoading: boolean;
  isFetching: boolean;
  error: Error | null;
  refetch: () => Promise<QueryObserverResult<ElectricityRatesResponse, Error>>;
} => {
  const {
    data: prices,
    isLoading,
    isFetching,
    error,
    refetch,
  } = useElectricityPrices(date);

  const processedData = useMemo(() => {
    if (!prices) return null;

    const targetDate = new Date();

    const current = getCurrentElectricityPrice(prices, targetDate);

    // upcoming
    const upcoming = getUpcomingElectricityPrices(prices);
    const cheapestUpcoming = findCheapestPrice(upcoming);
    const mostExpensiveUpcoming = findMostExpensivePrice(upcoming);
    const mostSustainableUpcoming = findMostSustainablePrice(upcoming);

    // current
    const cheapestCurrent = findCheapestPrice(prices);
    const mostExpensiveCurrent = findMostExpensivePrice(prices);
    const mostSustainableCurrent = findMostSustainablePrice(prices);

    return {
      current,
      upcoming,
      cheapestUpcoming,
      mostExpensiveUpcoming,
      mostSustainableUpcoming,
      cheapestCurrent,
      mostExpensiveCurrent,
      mostSustainableCurrent,
      allPrices: prices,
    };
  }, [prices]);

  return {
    data: processedData,
    isLoading,
    isFetching,
    error,
    refetch,
  };
};

export const useProcessedGasPrices = (
  date?: Date,
): {
  data: { price: GasRatesResponse[number] | null } | null;
  isLoading: boolean;
  isFetching: boolean;
  error: Error | null;
  refetch: () => Promise<QueryObserverResult<GasRatesResponse, Error>>;
} => {
  const {
    data: rawPrices,
    isLoading,
    isFetching,
    error,
    refetch,
  } = useGasPrices(date);

  const processedData = useMemo(() => {
    if (!rawPrices) return null;

    const price =
      rawPrices.length > 1
        ? getCurrentGasPrice(rawPrices)
        : (rawPrices[0] ?? null);

    return { price };
  }, [rawPrices]);

  return {
    data: processedData,
    isLoading,
    isFetching,
    error,
    refetch,
  };
};

export const useEnergyData = () => {
  const electricity = useProcessedElectricityPrices();
  const gas = useProcessedGasPrices();

  const isLoading = electricity.isLoading || gas.isLoading;
  const isFetching = electricity.isFetching || gas.isFetching;
  const error = electricity.error ?? gas.error;

  const refetch = async () => {
    await electricity.refetch();
    await gas.refetch();
  };

  const lastUpdated =
    !isLoading && !isFetching && !error && (electricity.data || gas.data)
      ? new Date()
      : null;

  return {
    electricity: electricity.data,
    gas: gas.data,
    isLoading,
    isFetching,
    error: error?.message ?? null,
    lastUpdated,
    refetch,
  };
};
