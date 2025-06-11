<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EnergyDataRepositoryInterface;
use App\Contracts\EnergyDataServiceInterface;
use App\Exceptions\EnergyDataException;
use App\Models\ElectricityRate;
use App\Models\GasRate;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

final readonly class EnergyDataService
{
    public function __construct(
        private EnergyDataServiceInterface $apiService,
        private EnergyDataRepositoryInterface $repository
    ) {}

    public function syncElectricityRatesForDay(Carbon|null $date = null): void
    {
        try {
            $rates = $this->apiService->getElectricityRates($date);
            if ($date) {
                // Need to do this because the API cuts off the first 2 hours.
                $previousDayRates = $this->apiService->getElectricityRates($date->copy()->subDay());
                array_push($rates, ...$previousDayRates);
            }
            $this->repository->upsertElectricityRates($rates);

            Log::info('Successfully synced electricity rates for day', [
                'count' => count($rates),
                'date' => $date ? $date->format('Y-m-d') : 'today',
            ]);
        } catch (Exception $exception) {
            Log::error('Failed to sync electricity rates for day', [
                'error' => $exception->getMessage(),
                'date' => $date ? $date->format('Y-m-d') : 'today',
            ]);
            throw new EnergyDataException(
                'Failed to sync electricity rates for '.($date ? $date->format('Y-m-d') : 'today').': '.$exception->getMessage(),
                previous: $exception
            );
        }
    }

    public function syncGasRatesForDay(Carbon $date): void
    {
        try {
            // NOTE: we do not pass a date to the api as that would result in no data being returned by the api.
            $rates = $this->apiService->getGasRates();
            $this->repository->upsertGasRates($rates);

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

    /**
     * @return Collection<int, ElectricityRate>
     */
    public function getElectricityRatesForDay(Carbon $date): Collection
    {
        return $this->repository->getElectricityRatesForDay($date);
    }

    /**
     * @return Collection<int, GasRate>
     */
    public function getGasRatesForDay(Carbon $date): Collection
    {
        return $this->repository->getGasRatesForDay($date);
    }

    /**
     * @return Collection<int, ElectricityRate>
     */
    public function getElectricityRatesWithFreshness(Carbon $date): Collection
    {
        $rates = $this->getElectricityRatesForDay($date);

        if ($rates->isEmpty() || count($rates) < 23 || $this->isDataStale($rates)) {
            $this->syncElectricityRatesForDay($date);
            $rates = $this->getElectricityRatesForDay($date);
        }

        return $rates;
    }

    /**
     * @return Collection<int, GasRate>
     */
    public function getGasRatesWithFreshness(Carbon $date): Collection
    {
        $rates = $this->getGasRatesForDay($date);

        if ($rates->isEmpty() || $this->isDataStale($rates)) {
            $this->syncGasRatesForDay($date);
            $rates = $this->getGasRatesForDay($date);
        }

        return $rates;
    }

    public function syncAllRatesForDay(Carbon $date): void
    {
        $this->syncElectricityRatesForDay($date);
        $this->syncGasRatesForDay($date);
    }

    public function syncRatesForDateRange(Carbon $startDate, Carbon $endDate): void
    {
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $this->syncAllRatesForDay($currentDate);
            $currentDate->addDay();
        }
    }

    /**
     * @return Collection<int, ElectricityRate>
     */
    public function getUpcomingElectricityRates(): Collection
    {
        return $this->repository->getUpcomingElectricityRates();
    }

    /**
     * @return Collection<int, ElectricityRate>
     */
    public function getUpcomingElectricityRatesWithFreshness(): Collection
    {
        $rates = $this->getUpcomingElectricityRates();

        if ($rates->isEmpty() || $this->isDataStale($rates)) {
            $this->syncElectricityRatesForDay();
            $rates = $this->getUpcomingElectricityRates();

        }

        return $rates;
    }

    /**
     * Check if data needs refreshing based on age and date
     * - Today/future dates: refresh every 5 minutes
     * - Past dates: refresh every 12 hours
     *
     * @param  Collection<int, ElectricityRate>|Collection<int, GasRate>  $rates
     */
    private function isDataStale(Collection $rates): bool
    {
        if ($rates->isEmpty()) {
            return true;
        }

        // Check if any rate is for today or future dates
        $hasCurrentOrFutureDates = $rates->some(function (ElectricityRate|GasRate $rate): bool {
            if ($rate->rate_date->isToday()) {
                return true;
            }

            return (bool) $rate->rate_date->isFuture();
        });

        if ($hasCurrentOrFutureDates) {
            // For current/future dates, refresh every 5 minutes
            $fiveMinutesAgo = now()->subMinutes(5);

            return $rates->some(fn (ElectricityRate|GasRate $rate): bool => $rate->updated_at === null || $rate->updated_at->lt($fiveMinutesAgo));
        }

        // For past dates, refresh every 12 hours
        $twelveHoursAgo = now()->subHours(12);

        return $rates->some(fn (ElectricityRate|GasRate $rate): bool => $rate->updated_at === null || $rate->updated_at->lt($twelveHoursAgo));
    }
}
