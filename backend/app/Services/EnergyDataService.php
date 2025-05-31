<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EnergyDataRepositoryInterface;
use App\Contracts\EnergyDataServiceInterface;
use App\Exceptions\EnergyDataException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final readonly class EnergyDataService
{
    public function __construct(
        private EnergyDataServiceInterface $apiService,
        private EnergyDataRepositoryInterface $repository
    ) {}

    public function syncElectricityRatesForDay(Carbon $date): void
    {
        try {
            $rates = $this->apiService->getElectricityRates($date);
            $this->repository->storeElectricityRates($rates);

            Log::info('Successfully synced electricity rates for day', [
                'count' => count($rates),
                'date' => $date->format('Y-m-d'),
            ]);
        } catch (Exception $exception) {
            Log::error('Failed to sync electricity rates for day', [
                'error' => $exception->getMessage(),
                'date' => $date->format('Y-m-d'),
            ]);
            throw new EnergyDataException(
                'Failed to sync electricity rates for '.$date->format('Y-m-d').': '.$exception->getMessage(),
                previous: $exception
            );
        }
    }

    public function syncGasRatesForDay(Carbon $date): void
    {
        try {
            $rates = $this->apiService->getGasRates($date);
            $this->repository->storeGasRates($rates);

            Log::info('Successfully synced gas rates for day', [
                'count' => count($rates),
                'date' => $date->format('Y-m-d'),
            ]);
        } catch (Exception $exception) {
            Log::error('Failed to sync gas rates for day', [
                'error' => $exception->getMessage(),
                'date' => $date->format('Y-m-d'),
            ]);
            throw new EnergyDataException(
                'Failed to sync gas rates for '.$date->format('Y-m-d').': '.$exception->getMessage(),
                previous: $exception
            );
        }
    }

    public function getElectricityRatesForDay(Carbon $date): Collection
    {
        return $this->repository->getElectricityRatesForDay($date);
    }

    public function getGasRatesForDay(Carbon $date): Collection
    {
        return $this->repository->getGasRatesForDay($date);
    }

    public function getAvailableElectricityDays(): Collection
    {
        return $this->repository->getAvailableElectricityDays();
    }

    public function getAvailableGasDays(): Collection
    {
        return $this->repository->getAvailableGasDays();
    }

    public function syncAllRatesForDay(Carbon $date): void
    {
        $this->syncElectricityRatesForDay($date);
        $this->syncGasRatesForDay($date);
    }

    // Convenience method to sync multiple days
    public function syncRatesForDateRange(Carbon $startDate, Carbon $endDate): void
    {
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $this->syncAllRatesForDay($currentDate);
            $currentDate->addDay();
        }
    }
}
