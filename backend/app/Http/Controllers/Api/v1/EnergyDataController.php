<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\EnergyDataException;
use App\Http\Resources\v1\ElectricityRateResource;
use App\Http\Resources\v1\GasRateResource;
use App\Services\EnergyDataService;
use App\Services\ZonneplanEnergyApiService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class EnergyDataController
{
    public function __construct(
        private EnergyDataService $energyDataService
    ) {}

    public function getElectricityRates(Request $request): JsonResponse
    {
        $dateString = $request->query('date');

        if (! $dateString) {
            return response()->json([
                'success' => false,
                'message' => 'Date parameter is required (format: YYYY-MM-DD)',
            ], 400);
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateString);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD',
            ], 400);
        }

        try {
            $rates = $this->energyDataService->getElectricityRatesForDay($date);

            // TODO:
            // if ($rates->isEmpty() || $rates->) {
            //     $rates = $this->zonneplanEnergyApiService->getElectricityRates($date);
            // }

            return response()->json([
                'success' => true,
                // 'data' => ElectricityRateResource::collection($rates),
                'data' => $rates,
                'meta' => [
                    // 'count' => $rates->count(),
                    'type' => 'electricity_rates',
                    'date' => $date->format('Y-m-d'),
                    // 'complete_day' => $rates->count() === 24
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve electricity rates',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getGasRates(Request $request): JsonResponse
    {
        $dateString = $request->query('date');

        if (! $dateString) {
            return response()->json([
                'success' => false,
                'message' => 'Date parameter is required (format: YYYY-MM-DD)',
            ], 400);
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateString);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD',
            ], 400);
        }

        try {
            $rates = $this->energyDataService->getGasRatesForDay($date);

            return response()->json([
                'success' => true,
                'data' => GasRateResource::collection($rates),
                'meta' => [
                    'count' => $rates->count(),
                    'type' => 'gas_rates',
                    'date' => $date->format('Y-m-d'),
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gas rates',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function getAvailableDays(): JsonResponse
    {
        try {
            $electricityDays = $this->energyDataService->getAvailableElectricityDays();
            $gasDays = $this->energyDataService->getAvailableGasDays();

            return response()->json([
                'success' => true,
                'data' => [
                    'electricity_days' => $electricityDays->toArray(),
                    'gas_days' => $gasDays->toArray(),
                    'common_days' => $electricityDays->intersect($gasDays)->values()->toArray(),
                ],
                'meta' => [
                    'electricity_count' => $electricityDays->count(),
                    'gas_count' => $gasDays->count(),
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available days',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function syncRates(Request $request): JsonResponse
    {
        $dateString = $request->input('date');
        $type = $request->input('type', 'all');

        if (! $dateString) {
            return response()->json([
                'success' => false,
                'message' => 'Date parameter is required (format: YYYY-MM-DD)',
            ], 400);
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateString);
        } catch (Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD',
            ], 400);
        }

        try {
            match ($type) {
                'electricity' => $this->energyDataService->syncElectricityRatesForDay($date),
                'gas' => $this->energyDataService->syncGasRatesForDay($date),
                default => $this->energyDataService->syncAllRatesForDay($date),
            };

            return response()->json([
                'success' => true,
                'message' => ucfirst((string) $type).' rates synced successfully for '.$date->format('Y-m-d'),
                'date' => $date->format('Y-m-d'),
                'type' => $type,
            ]);
        } catch (EnergyDataException $energyDataException) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync '.$type.' rates',
                'error' => $energyDataException->getMessage(),
            ], 500);
        }
    }
}
