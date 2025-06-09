<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\EnergyDataRepositoryInterface;
use App\Contracts\EnergyDataServiceInterface;
use App\Repositories\EnergyDataRepository;
use App\Services\ZonneplanEnergyApiService;
use Illuminate\Contracts\Foundation\Application;
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
            function (Application $app): ZonneplanEnergyApiService {
                $httpClient = $app->make(HttpClient::class);
                $baseUrl = config('energy.api_base_url');
                $apiKey = config('energy.api_key');

                return new ZonneplanEnergyApiService(
                    $httpClient,
                    is_string($baseUrl) ? $baseUrl : '',
                    is_string($apiKey) ? $apiKey : ''
                );
            }
        );
    }

    public function boot(): void
    {
        //
    }
}
