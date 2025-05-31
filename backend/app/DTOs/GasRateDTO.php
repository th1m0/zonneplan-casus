<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

final readonly class GasRateDTO
{
    public function __construct(
        public Carbon $periodStart,
        public Carbon $periodEnd,
        public string $period,
        public int $marketPrice, // in micro-units
        public int $totalPriceTaxIncluded,
        public int $priceInclHandlingVat,
        public int $priceTaxWithVat,
        public string $currency = 'EUR',
        public ?array $metadata = null
    ) {}

    public function toArray(): array
    {
        return [
            'period_start' => $this->periodStart,
            'period_end' => $this->periodEnd,
            'rate_date' => $this->periodStart->toDateString(),
            'period' => $this->period,
            'market_price' => $this->marketPrice,
            'total_price_tax_included' => $this->totalPriceTaxIncluded,
            'price_incl_handling_vat' => $this->priceInclHandlingVat,
            'price_tax_with_vat' => $this->priceTaxWithVat,
            'currency' => $this->currency,
            'metadata' => $this->metadata,
        ];
    }

    // Helper methods to convert micro-units to euros
    public function getMarketPriceInEuros(): float
    {
        return $this->marketPrice / 1000000;
    }

    public function getTotalPriceInEuros(): float
    {
        return $this->totalPriceTaxIncluded / 1000000;
    }

    public function getPriceInclHandlingVatInEuros(): float
    {
        return $this->priceInclHandlingVat / 1000000;
    }

    public function getPriceTaxWithVatInEuros(): float
    {
        return $this->priceTaxWithVat / 1000000;
    }
}
