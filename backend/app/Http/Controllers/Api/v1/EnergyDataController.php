<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Resources\v1\ElectricityRateResource;
use App\Http\Resources\v1\GasRateResource;
use App\Services\EnergyDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class EnergyDataController
{
    public function __construct(
        private EnergyDataService $energyDataService,
    ) {}

    public function getElectricityRates(Request $request): JsonResponse
    {
        /** @var string $dateString */
        $dateString = $request->query('date', now()->format('Y-m-d'));

        if (! $dateString) {
            return response()->json([
                'success' => false,
                'message' => 'Date parameter is required (format: YYYY-MM-DD)',
            ], 400);
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateString);

            if (! $date instanceof Carbon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD',
                ], 400);
            }
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD',
            ], 400);
        }

        try {
            $rates = $this->energyDataService->getElectricityRatesWithFreshness($date);

            return response()->json([
                'success' => true,
                'data' => ElectricityRateResource::collection($rates),
                'meta' => [
                    'type' => 'electricity_rates',
                    'date' => $date->format('Y-m-d'),
                    'count' => $rates->count(),
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
        /** @var string $dateString */
        $dateString = $request->query('date', now()->format('Y-m-d'));

        if (! $dateString) {
            return response()->json([
                'success' => false,
                'message' => 'Date parameter is required (format: YYYY-MM-DD)',
            ], 400);
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateString);

            if (! $date instanceof Carbon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD',
                ], 400);
            }
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD',
            ], 400);
        }

        try {
            $rates = $this->energyDataService->getGasRatesWithFreshness($date);

            return response()->json([
                'success' => true,
                'data' => GasRateResource::collection($rates),
                'meta' => [
                    'type' => 'gas_rates',
                    'date' => $date->format('Y-m-d'),
                    'count' => $rates->count(),
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
}
