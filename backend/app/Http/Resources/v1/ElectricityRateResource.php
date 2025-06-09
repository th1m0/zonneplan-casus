<?php

declare(strict_types=1);

namespace App\Http\Resources\v1;

use App\Models\ElectricityRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ElectricityRate $resource
 *
 * @phpstan-import-type ElectricityRateResponse from \App\Types\ApiTypes
 * @phpstan-import-type PriceInEuros from \App\Types\ApiTypes
 */
final class ElectricityRateResource extends JsonResource
{
    /**
     * @return ElectricityRateResponse
     */
    public function toArray(Request $request): array
    {
        return [
            'period_start' => $this->formatCarbonToIso($this->resource->period_start),
            'period_end' => $this->formatCarbonToIso($this->resource->period_end),
            'rate_date' => $this->resource->rate_date->toDateString(),
            'period' => $this->resource->period,
            'market_price' => $this->resource->market_price,
            'total_price_tax_included' => $this->resource->total_price_tax_included,
            'price_incl_handling_vat' => $this->resource->price_incl_handling_vat,
            'price_tax_with_vat' => $this->resource->price_tax_with_vat,
            'pricing_profile' => $this->resource->pricing_profile,
            'carbon_footprint_in_gram' => $this->resource->carbon_footprint_in_gram,
            'sustainability_score' => $this->resource->sustainability_score,
            'currency' => $this->resource->currency,
            'metadata' => $this->resource->metadata,
            'prices_in_euros' => $this->getPricesInEuros(),
        ];
    }

    /**
     * @return PriceInEuros
     */
    private function getPricesInEuros(): array
    {
        return [
            'market_price' => $this->resource->market_price_in_euros,
            'total_price_tax_included' => $this->resource->total_price_in_euros,
            'price_incl_handling_vat' => $this->resource->price_incl_handling_vat_in_euros,
            'price_tax_with_vat' => $this->resource->price_tax_with_vat_in_euros,
        ];
    }

    private function formatCarbonToIso(Carbon $carbon): string
    {
        return $carbon->toISOString() ?? $carbon->format('Y-m-d\TH:i:s.u\Z');
    }
}
