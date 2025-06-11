/**
 * Gas rate pricing information in euros (converted from micro-units)
 */
export type PriceInEuros = {
  market_price: number;
  total_price_tax_included: number;
  price_incl_handling_vat: number;
  price_tax_with_vat: number;
};

/**
 * Metadata object for gas rates - flexible key-value pairs
 */
export type GasRateMetadata = {
  [key: string]: string | number | boolean | null;
};

/**
 * Supported currency codes
 */
export type Currency = "EUR" | string;

/**
 * Complete gas rate response from the API
 */
export type GasRatesResponse = {
  /** ISO 8601 datetime string for period start */
  period_start: string;
  /** ISO 8601 datetime string for period end */
  period_end: string;
  /** Date string in YYYY-MM-DD format */
  rate_date: string;
  /** Time period string (e.g., "14:00-15:00") */
  period: string;
  /** Market price in micro-units (e.g., 80000 = €0.08) */
  market_price: number;
  /** Total price including tax in micro-units */
  total_price_tax_included: number;
  /** Price including handling and VAT in micro-units */
  price_incl_handling_vat: number;
  /** Tax price with VAT in micro-units */
  price_tax_with_vat: number;
  /** Currency code */
  currency: Currency;
  /** Additional metadata */
  metadata: GasRateMetadata | null;
  /** Prices converted to euros for convenience */
  prices_in_euros: PriceInEuros;
}[];

/**
 * API response wrapper for multiple gas rates
 */
export type GasRatesApiResponse = {
  data: GasRatesResponse;
};
