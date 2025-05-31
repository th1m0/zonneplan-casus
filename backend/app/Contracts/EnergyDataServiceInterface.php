<?php

declare(strict_types=1);

namespace App\Contracts;

use Carbon\Carbon;

interface EnergyDataServiceInterface
{
    public function getElectricityRates(Carbon $date): array;

    public function getGasRates(Carbon $date): array;
}
