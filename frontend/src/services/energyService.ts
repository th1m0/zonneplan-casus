import { format } from "date-fns";
import { tryCatch } from "~/lib/tryCatch";
import type {
  ElectricityRatesApiResponse,
  ElectricityRatesResponse,
} from "~/types/ElectricityRateApiResponse";
import type {
  GasRatesApiResponse,
  GasRatesResponse,
} from "~/types/GasRateApiResponse";

// Helper om mock data voor een specifieke dag te genereren
// const generateMockElectricityPricesForDate = (
//   targetDate: Date,
// ): ElectricityPriceEntry[] => {
//   const prices: ElectricityPriceEntry[] = [];
//   const dateToUse = new Date(targetDate);
//   dateToUse.setHours(0, 0, 0, 0); // Begin van de dag

//   for (let i = 0; i < 24; i++) {
//     // 24 uur voor de geselecteerde dag
//     const startDate = new Date(dateToUse.getTime() + i * 60 * 60 * 1000);
//     const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);

//     const pad = (num: number) => num.toString().padStart(2, "0");
//     const startDateDatetime = `${startDate.getFullYear()}-${pad(startDate.getMonth() + 1)}-${pad(startDate.getDate())} ${pad(startDate.getHours())}:${pad(startDate.getMinutes())}:${pad(startDate.getSeconds())}`;

//     // Simuleer variatie in prijzen
//     const basePrice = (targetDate.getDate() % 10) * 100000 + 1500000; // Prijs varieert per dag
//     const hourFluctuation = Math.sin((i * Math.PI) / 12) * 500000; // Sinusgolf voor uurvariatie

//     prices.push({
//       start_date: Math.floor(startDate.getTime() / 1000),
//       end_date: Math.floor(endDate.getTime() / 1000),
//       period: "PT60M",
//       market_price: Math.floor(Math.random() * 500000) + 500000,
//       total_price_tax_included: Math.max(
//         500000,
//         Math.floor(
//           basePrice + hourFluctuation + (Math.random() - 0.5) * 200000,
//         ),
//       ),
//       price_incl_handling_vat: Math.floor(Math.random() * 300000) + 700000,
//       price_tax_with_vat: Math.floor(Math.random() * 700000) + 800000,
//       pricing_profile: Math.random() > 0.5 ? "low" : "normal",
//       carbon_footprint_in_gram:
//         Math.random() > 0.3 ? Math.floor(Math.random() * 300) + 50 : null,
//       sustainability_score: Math.floor(Math.random() * 400) + 600,
//       start_date_datetime: startDateDatetime,
//     });
//   }
//   // Simuleer soms lege data voor een dag
//   if (targetDate.getDate() % 5 === 0) return [];
//   return prices;
// };

// const generateMockGasPricesForDate = (targetDate: Date): GasPriceEntry[] => {
//   const dateToUse = new Date(targetDate);
//   dateToUse.setHours(6, 0, 0, 0);

//   const pad = (num: number) => num.toString().padStart(2, "0");
//   const startDateDatetime = `${dateToUse.getFullYear()}-${pad(dateToUse.getMonth() + 1)}-${pad(dateToUse.getDate())} ${pad(dateToUse.getHours())}:${pad(dateToUse.getMinutes())}:${pad(dateToUse.getSeconds())}`;

//   // Simuleer soms lege data
//   if (targetDate.getDate() % 6 === 0) return [];

//   return [
//     {
//       start_date: Math.floor(dateToUse.getTime() / 1000),
//       end_date: Math.floor(dateToUse.getTime() / 1000) + 24 * 60 * 60,
//       period: "PT24H",
//       market_price: Math.floor(Math.random() * 2000000) + 3000000,
//       total_price_tax_included:
//         Math.floor(Math.random() * 5000000) +
//         10000000 +
//         (targetDate.getDate() % 7) * 100000,
//       price_incl_handling_vat: Math.floor(Math.random() * 2000000) + 4000000,
//       price_tax_with_vat: Math.floor(Math.random() * 3000000) + 6000000,
//       start_date_datetime: startDateDatetime,
//     },
//   ];
// };

export const fetchElectricityPrices = async (
  date: Date = new Date(),
): Promise<ElectricityRatesResponse> => {
  console.log(`Fetching electricity prices for: ${format(date, "yyyy-MM-dd")}`);

  const url = new URL("/api/v1/energy/electricity", "http://localhost:8000");
  url.searchParams.append("date", format(date, "yyyy-MM-dd"));

  const { data: prices, error: errorResposne } = await tryCatch(
    fetch(url).then<ElectricityRatesApiResponse>((r) => r.json()),
  );

  if (errorResposne) {
    throw new Error(
      `Kon electriciteitsprijzen niet ophalen voor ${format(date, "dd-MM-yyyy")}`,
    );
  }

  if (!prices.data || prices.data.length === 0) {
    // TODO: return an error instead of throwing an error
    throw new Error(
      `Kon electriciteitsprijzen niet ophalen voor ${format(date, "dd-MM-yyyy")}`,
    );
  }

  return prices.data;
};

export const fetchGasPrices = async (
  date: Date = new Date(),
): Promise<GasRatesResponse> => {
  console.log(`Fetching gas prices for: ${format(date, "yyyy-MM-dd")}`);

  const url = new URL("/api/v1/energy/gas", "http://localhost:8000");
  url.searchParams.append("date", format(date, "yyyy-MM-dd"));

  const { data: prices, error: errorResposne } = await tryCatch(
    fetch(url).then<GasRatesApiResponse>((r) => r.json()),
  );

  if (errorResposne) {
    throw new Error(
      `Kon gasprijzen niet ophalen voor ${format(date, "dd-MM-yyyy")}`,
    );
  }

  if (!prices.data || prices.data.length === 0) {
    // TODO: return an error instead of throwing an error
    throw new Error(
      `Kon gasprijzen niet ophalen voor ${format(date, "dd-MM-yyyy")}`,
    );
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
