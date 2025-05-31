<?php

declare(strict_types=1);

namespace App\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface EnergyDataRepositoryInterface
{
    public function storeElectricityRates(array $rates): void;

    public function storeGasRates(array $rates): void;

    public function getElectricityRatesForDay(Carbon $date): Collection;

    public function getGasRatesForDay(Carbon $date): Collection;

    public function getAvailableElectricityDays(): Collection;

    public function getAvailableGasDays(): Collection;
}
