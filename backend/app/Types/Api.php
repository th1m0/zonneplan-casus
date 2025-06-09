<?php

declare(strict_types=1);

namespace App\Types;

/**
 * @phpstan-type PriceInEuros array{
 *     market_price: float,
 *     total_price_tax_included: float,
 *     price_incl_handling_vat: float,
 *     price_tax_with_vat: float
 * }
 * @phpstan-type ElectricityRateResponse array{
 *     period_start: string,
 *     period_end: string,
 *     rate_date: string,
 *     period: string,
 *     market_price: int,
 *     total_price_tax_included: int,
 *     price_incl_handling_vat: int,
 *     price_tax_with_vat: int,
 *     pricing_profile: string|null,
 *     carbon_footprint_in_gram: int|null,
 *     sustainability_score: int|null,
 *     currency: string,
 *     metadata: array<int|string, mixed>|null,
 *     prices_in_euros: PriceInEuros
 * }
 * @phpstan-type GasRateResponse array{
 *     period_start: string,
 *     period_end: string,
 *     rate_date: string,
 *     period: string,
 *     market_price: int,
 *     total_price_tax_included: int,
 *     price_incl_handling_vat: int,
 *     price_tax_with_vat: int,
 *     currency: string,
 *     metadata: array<int|string, mixed>|null,
 *     prices_in_euros: PriceInEuros
 * }
 */
final class ApiTypes
{
    // Type container - no implementation needed
}
