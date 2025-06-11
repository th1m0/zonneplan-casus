import { format } from "date-fns";
import { env } from "~/env";
import { tryCatch } from "~/lib/tryCatch";
import type {
  ElectricityRatesApiResponse,
  ElectricityRatesResponse,
} from "~/types/ElectricityRateApiResponse";
import type {
  GasRatesApiResponse,
  GasRatesResponse,
} from "~/types/GasRateApiResponse";

export const fetchElectricityPrices = async (
  date?: Date,
): Promise<ElectricityRatesResponse> => {
  const formattedDate = date ? format(date, "yyyy-MM-dd") : "today";
  console.log(`Fetching electricity prices for: ${formattedDate}`);

  const url = new URL("/api/v1/energy/electricity", env.NEXT_PUBLIC_API_URL);
  if (date) url.searchParams.append("date", formattedDate);

  const { data: prices, error: errorResposne } = await tryCatch(
    fetch(url).then<ElectricityRatesApiResponse>((r) => r.json()),
  );

  if (errorResposne) {
    // TODO: return an error instead of throwing an error
    throw new Error(
      `Kon electriciteitsprijzen niet ophalen voor ${formattedDate}`,
    );
  }

  if (!prices.data || prices.data.length === 0) {
    // TODO: return an error instead of throwing an error
    throw new Error(
      `Kon electriciteitsprijzen niet ophalen voor ${formattedDate}`,
    );
  }

  return prices.data;
};

export const fetchGasPrices = async (
  date?: Date,
): Promise<GasRatesResponse> => {
  const formattedDate = date ? format(date, "yyyy-MM-dd") : "today";
  console.log(`Fetching gas prices for: ${formattedDate}`);

  const url = new URL("/api/v1/energy/gas", env.NEXT_PUBLIC_API_URL);
  if (date) url.searchParams.append("date", formattedDate);

  const { data: prices, error: errorResposne } = await tryCatch(
    fetch(url).then<GasRatesApiResponse>((r) => r.json()),
  );

  if (errorResposne) {
    // TODO: return an error instead of throwing an error
    throw new Error(`Kon gasprijzen niet ophalen voor ${formattedDate}`);
  }

  if (!prices.data || prices.data.length === 0) {
    // TODO: return an error instead of throwing an error
    throw new Error(`Kon gasprijzen niet ophalen voor ${formattedDate}`);
  }

  return prices.data;
};

export const energyQueryKeys = {
  all: ["energy"] as const,
  electricity: (date?: Date) =>
    [
      ...energyQueryKeys.all,
      "electricity",
      date ? format(date, "yyyy-MM-dd") : "current",
    ] as const,
  gas: (date?: Date) =>
    [
      ...energyQueryKeys.all,
      "gas",
      date ? format(date, "yyyy-MM-dd") : "current",
    ] as const,
};
