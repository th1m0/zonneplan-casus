<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\EnergyDataRepositoryInterface;
use App\Contracts\EnergyDataServiceInterface;
use App\Repositories\EnergyDataRepository;
use App\Services\ZonneplanEnergyApiService;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\ServiceProvider;

final class EnergyDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EnergyDataRepositoryInterface::class,
            EnergyDataRepository::class
        );

        $this->app->bind(
            EnergyDataServiceInterface::class,
            fn ($app): ZonneplanEnergyApiService => new ZonneplanEnergyApiService(
                $app->make(HttpClient::class),
                config('energy.api_base_url', ''),
                config('energy.api_key', '')
            )
        );
    }

    public function boot(): void
    {
        //
    }
}
