<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Services\EnergyDataService;
use App\Services\ZonneplanEnergyApiService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class EnergyDataController
{
    public function __construct(
        private EnergyDataService $energyDataService,
        private readonly ZonneplanEnergyApiService $zonneplanEnergyApiService
    ) {}

    public function getElectricityRates(Request $request): JsonResponse
    {
        $dateString = $request->query('date', now()->format('Y-m-d'));

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

            if ($rates->isEmpty()) {
                $foo = $this->zonneplanEnergyApiService->getElectricityRates($date);
            }

            if ($rates->isE) {
                return response()->json([
                    'success' => true,
                    'data' => $rates,
                    'meta' => [
                        'type' => 'electricity_rates',
                        'date' => $date->format('Y-m-d'),
                    ],
                ]);
            }
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
        $dateString = $request->query('date', now()->format('Y-m-d'));

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
                'data' => $rates,
                'meta' => [
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
}
