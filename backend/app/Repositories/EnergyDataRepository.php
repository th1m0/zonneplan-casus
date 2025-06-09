<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\EnergyDataRepositoryInterface;
use App\DTOs\ElectricityRateDTO;
use App\DTOs\GasRateDTO;
use App\Models\ElectricityRate;
use App\Models\GasRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class EnergyDataRepository implements EnergyDataRepositoryInterface
{
    /**
     * @param  array<int, ElectricityRateDTO>  $rates
     */
    public function upsertElectricityRates(array $rates): void
    {
        $data = [];

        foreach ($rates as $dto) {
            $rateData = $dto->toArray();

            // Convert Carbon objects to database-compatible strings
            $rateData['period_start'] = $rateData['period_start']->format('Y-m-d H:i:s');
            $rateData['period_end'] = $rateData['period_end']->format('Y-m-d H:i:s');

            // Handle metadata array to JSON conversion for bulk insert
            if (is_array($rateData['metadata'])) {
                $rateData['metadata'] = json_encode($rateData['metadata']);
            }

            // Add timestamps for bulk insert
            $now = now()->format('Y-m-d H:i:s');
            $rateData['created_at'] = $now;
            $rateData['updated_at'] = $now;

            $data[] = $rateData;
        }

        ElectricityRate::upsert(
            $data,
            ['period_start', 'period_end'],
            [
                'rate_date',
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

    /**
     * @param  array<int, GasRateDTO>  $rates
     */
    public function upsertGasRates(array $rates): void
    {
        $data = [];

        foreach ($rates as $dto) {
            $rateData = $dto->toArray();

            // Convert Carbon objects to database-compatible strings
            $rateData['period_start'] = $rateData['period_start']->format('Y-m-d H:i:s');
            $rateData['period_end'] = $rateData['period_end']->format('Y-m-d H:i:s');

            // Handle metadata array to JSON conversion for bulk insert
            if (is_array($rateData['metadata'])) {
                $rateData['metadata'] = json_encode($rateData['metadata']);
            }

            // Add timestamps for bulk insert
            $now = now()->format('Y-m-d H:i:s');
            $rateData['created_at'] = $now;
            $rateData['updated_at'] = $now;

            $data[] = $rateData;
        }

        GasRate::upsert(
            $data,
            ['period_start', 'period_end'],
            [
                'rate_date',
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

    /**
     * @return Collection<int, ElectricityRate>
     */
    public function getElectricityRatesForDay(Carbon $date): Collection
    {
        return ElectricityRate::forDay($date)->get();
    }

    /**
     * @return Collection<int, GasRate>
     */
    public function getGasRatesForDay(Carbon $date): Collection
    {
        return GasRate::forDay($date)->get();
    }
}
