<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EnergyDataServiceInterface;
use App\DTOs\ElectricityRateDTO;
use App\DTOs\GasRateDTO;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Log;

final readonly class ZonneplanEnergyApiService implements EnergyDataServiceInterface
{
    public function __construct(
        private HttpClient $httpClient,
        private string $baseUrl,
        private string $apiKey
    ) {}

    public function getElectricityRates(Carbon $date): array
    {
        // TODO: Uncomment and implement actual API call
        try {
            $response = $this->httpClient->withHeaders([
                // 'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl.'/electricity/upcoming', [
                'date' => $date->format('Y-m-d'),
                'secret' => $this->apiKey,
            ]);

            if (! $response->successful()) {
                throw new Exception('API request failed: '.$response->status());
            }

            $data = $response->json();

            return $this->transformElectricityData($data);
        } catch (Exception $exception) {
            Log::error('Failed to fetch electricity rates', [
                'error' => $exception->getMessage(),
                'date' => $date->format('Y-m-d'),
            ]);
            throw $exception;
        }
    }

    public function getGasRates(Carbon $date): array
    {

        try {
            $response = $this->httpClient->withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl.'/electricity/upcoming', [
                'date' => $date->format('Y-m-d'),
                'secret' => 'mzRqGxv9dzaatsFgcXoVJbh6pdMDwDxEWBhbZ89r',
            ]);

            if (! $response->successful()) {
                throw new Exception('API request failed: '.$response->status());
            }

            $data = $response->json();

            return $this->transformGasData($data);
        } catch (Exception $exception) {
            Log::error('Failed to fetch gas rates', [
                'error' => $exception->getMessage(),
                'date' => $date->format('Y-m-d'),
            ]);
            throw $exception;
        }
    }

    private function transformElectricityData(array $data): array
    {
        return collect($data['data'] ?? [])->map(fn ($rate): ElectricityRateDTO => new ElectricityRateDTO(
            periodStart: Carbon::createFromTimestamp($rate['start_date']),
            periodEnd: Carbon::createFromTimestamp($rate['end_date']),
            period: $rate['period'],
            marketPrice: $rate['market_price'],
            totalPriceTaxIncluded: $rate['total_price_tax_included'],
            priceInclHandlingVat: $rate['price_incl_handling_vat'],
            priceTaxWithVat: $rate['price_tax_with_vat'],
            pricingProfile: $rate['pricing_profile'] ?? null,
            carbonFootprintInGram: $rate['carbon_footprint_in_gram'],
            sustainabilityScore: $rate['sustainability_score'] ?? null,
            metadata: [
                'start_date_datetime' => $rate['start_date_datetime'] ?? null,
                'source' => 'zonneplan_api',
            ]
        ))->toArray();
    }

    private function transformGasData(array $data): array
    {
        return collect($data['data'] ?? [])->map(fn ($rate): GasRateDTO => new GasRateDTO(
            periodStart: Carbon::createFromTimestamp($rate['start_date']),
            periodEnd: Carbon::createFromTimestamp($rate['end_date']),
            period: $rate['period'],
            marketPrice: $rate['market_price'],
            totalPriceTaxIncluded: $rate['total_price_tax_included'],
            priceInclHandlingVat: $rate['price_incl_handling_vat'],
            priceTaxWithVat: $rate['price_tax_with_vat'],
            metadata: [
                'start_date_datetime' => $rate['start_date_datetime'] ?? null,
                'source' => 'zonneplan_api',
            ]
        ))->toArray();
    }
}
