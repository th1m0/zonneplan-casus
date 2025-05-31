<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\EnergyDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('energy')->group(function (): void {
        Route::get('/electricity', [EnergyDataController::class, 'getElectricityRates']);
        Route::get('/gas', [EnergyDataController::class, 'getGasRates']);

        Route::get('/available-days', [EnergyDataController::class, 'getAvailableDays']);

        Route::post('/sync', [EnergyDataController::class, 'syncRates']);
    });
});
