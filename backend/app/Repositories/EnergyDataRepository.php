<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\EnergyDataRepositoryInterface;
use App\DTOs\ElectricityRateDTO;
use App\DTOs\GasRateDTO;
use App\Models\ElectricityRate;
use App\Models\GasRate;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class EnergyDataRepository implements EnergyDataRepositoryInterface
{
    public function storeElectricityRates(array $rates): void
    {
        $data = collect($rates)->map(fn (ElectricityRateDTO $dto): array => $dto->toArray())->toArray();

        ElectricityRate::upsert(
            $data,
            ['rate_date', 'period_start'], // Unique constraint
            [
                'period_end',
                'period',
                'market_price',
                'total_price_tax_included',
                'price_incl_handling_vat',
                'price_tax_with_vat',
                'pricing_profile',
                'carbon_footprint_in_gram',
                'sustainability_score',
                'currency',
                'metadata',
                'updated_at',
            ]
        );
    }

    public function storeGasRates(array $rates): void
    {
        $data = collect($rates)->map(fn (GasRateDTO $dto): array => $dto->toArray())->toArray();

        GasRate::upsert(
            $data,
            ['rate_date'], // Unique constraint
            [
                'period_start',
                'period_end',
                'period',
                'market_price',
                'total_price_tax_included',
                'price_incl_handling_vat',
                'price_tax_with_vat',
                'currency',
                'metadata',
                'updated_at',
            ]
        );
    }

    public function getElectricityRatesForDay(Carbon $date): Collection
    {
        return ElectricityRate::forDay($date)->get();
    }

    public function getGasRatesForDay(Carbon $date): Collection
    {
        return GasRate::forDay($date)->get();
    }

    public function getAvailableElectricityDays(): Collection
    {
        return ElectricityRate::availableDays()->pluck('rate_date');
    }

    public function getAvailableGasDays(): Collection
    {
        return GasRate::availableDays()->pluck('rate_date');
    }
}
