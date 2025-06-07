<?php

declare(strict_types=1);

namespace App\Contracts;

use Carbon\Carbon;
use App\DTOs\ElectricityRateDTO;
use App\DTOs\GasRateDTO;

interface EnergyDataServiceInterface
{
    /**
     * @return array<int, ElectricityRateDTO>
     */
    public function getElectricityRates(Carbon $date): array;

    /**
     * @return array<int, GasRateDTO>
     */
    public function getGasRates(Carbon $date): array;
}
