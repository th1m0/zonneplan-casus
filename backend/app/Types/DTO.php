<?php

declare(strict_types=1);

namespace App\Types;

use Carbon\Carbon;

/**
 * @phpstan-type ElectricityRate array{
 *     period_start: Carbon,
 *     period_end: Carbon,
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
 *     metadata: array<string, scalar|null>|null
 * }
 * @phpstan-type GasRate array{
 *     period_start: Carbon,
 *     period_end: Carbon,
 *     rate_date: string,
 *     period: string,
 *     market_price: int,
 *     total_price_tax_included: int,
 *     price_incl_handling_vat: int,
 *     price_tax_with_vat: int,
 *     currency: string,
 *     metadata: array<string, scalar|null>|null
 * }
 */
final class DTOTypes
{
    // Type container - no implementation needed
}
