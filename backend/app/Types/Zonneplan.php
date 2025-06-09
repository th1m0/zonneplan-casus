<?php

declare(strict_types=1);

namespace App\Types;

/**
 * @phpstan-type ZonneplanApiElectricityResponse array{data: array<int, array{
 *     start_date: int|string,
 *     end_date: int|string,
 *     period: string,
 *     market_price: int|string,
 *     total_price_tax_included: int|string,
 *     price_incl_handling_vat: int|string,
 *     price_tax_with_vat: int|string,
 *     pricing_profile?: string|null,
 *     carbon_footprint_in_gram?: int|string|null,
 *     sustainability_score?: int|string|null,
 *     start_date_datetime?: string|null
 * }>}
 * @phpstan-type ZonneplanApiGasResponse array{data: array<int, array{
 *     start_date: int|string,
 *     end_date: int|string,
 *     period: string,
 *     market_price: int|string,
 *     total_price_tax_included: int|string,
 *     price_incl_handling_vat: int|string,
 *     price_tax_with_vat: int|string,
 *     start_date_datetime?: string|null
 * }>}
 */
final class ZonneplanApiTypes
{
    // Type container - no implementation needed
}
