<?php

declare(strict_types=1);

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class GasRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'period_start' => $this->period_start->toISOString(),
            'period_end' => $this->period_end->toISOString(),
            'period' => $this->period,
            'prices' => [
                'market_price' => [
                    'micro_euros' => $this->market_price,
                    'euros' => $this->market_price_in_euros,
                ],
                'total_price_tax_included' => [
                    'micro_euros' => $this->total_price_tax_included,
                    'euros' => $this->total_price_in_euros,
                ],
                'price_incl_handling_vat' => [
                    'micro_euros' => $this->price_incl_handling_vat,
                    'euros' => $this->price_incl_handling_vat_in_euros,
                ],
                'price_tax_with_vat' => [
                    'micro_euros' => $this->price_tax_with_vat,
                    'euros' => $this->price_tax_with_vat_in_euros,
                ],
            ],
            'currency' => $this->currency,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
