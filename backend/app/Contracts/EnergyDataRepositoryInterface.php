<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\ElectricityRateDTO;
use App\DTOs\GasRateDTO;
use App\Models\ElectricityRate;
use App\Models\GasRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface EnergyDataRepositoryInterface
{
    /**
     * @param  array<int, ElectricityRateDTO>  $rates
     */
    public function upsertElectricityRates(array $rates): void;

    /**
     * @param  array<int, GasRateDTO>  $rates
     */
    public function upsertGasRates(array $rates): void;

    /**
     * @return Collection<int, ElectricityRate>
     */
    public function getElectricityRatesForDay(Carbon $date): Collection;

    /**
     * @return Collection<int, GasRate>
     */
    public function getGasRatesForDay(Carbon $date): Collection;
}
