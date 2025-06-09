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

/**
 * @phpstan-import-type ZonneplanApiElectricityResponse from \App\Types\ZonneplanApiTypes
 * @phpstan-import-type ZonneplanApiGasResponse from \App\Types\ZonneplanApiTypes
 */
final readonly class ZonneplanEnergyApiService implements EnergyDataServiceInterface
{
    private const TIMEZONE = 'Europe/Amsterdam';

    public function __construct(
        private HttpClient $httpClient,
        private string $baseUrl,
        private string $apiKey
    ) {}

    /**
     * @return array<int, ElectricityRateDTO>
     *
     * @throws Exception
     */
    public function getElectricityRates(Carbon $date): array
    {
        try {
            $response = $this->httpClient->withHeaders([
                'Accept' => 'application/json',
            ])->get($this->baseUrl.'/electricity/upcoming', $this->getQueryParams($date));

            if (! $response->successful()) {
                throw new Exception('API request failed: '.$response->status());
            }

            /** @var ZonneplanApiElectricityResponse $data
             */
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

    /**
     * @return array<int, GasRateDTO>
     *
     * @throws Exception
     */
    public function getGasRates(Carbon $date): array
    {
        try {
            $response = $this->httpClient->withHeaders([
                'Accept' => 'application/json',
            ])->get($this->baseUrl.'/gas/upcoming', $this->getQueryParams($date));

            if (! $response->successful()) {
                throw new Exception('API request failed: '.$response->status());
            }

            /** @var ZonneplanApiGasResponse $data
             */
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

    /**
     * @param  ZonneplanApiElectricityResponse  $data
     * @return array<int, ElectricityRateDTO>
     */
    private function transformElectricityData(array $data): array
    {
        $result = [];

        foreach ($data['data'] as $rate) {
            $result[] = new ElectricityRateDTO(
                periodStart: Carbon::createFromTimestamp((int) $rate['start_date'], self::TIMEZONE),
                periodEnd: Carbon::createFromTimestamp((int) $rate['end_date'], self::TIMEZONE),
                period: (string) $rate['period'],
                marketPrice: (int) $rate['market_price'],
                totalPriceTaxIncluded: (int) $rate['total_price_tax_included'],
                priceInclHandlingVat: (int) $rate['price_incl_handling_vat'],
                priceTaxWithVat: (int) $rate['price_tax_with_vat'],
                pricingProfile: $rate['pricing_profile'] ?? null,
                carbonFootprintInGram: isset($rate['carbon_footprint_in_gram']) ? (int) $rate['carbon_footprint_in_gram'] : null,
                sustainabilityScore: isset($rate['sustainability_score']) ? (int) $rate['sustainability_score'] : null,
                metadata: [
                    'start_date_datetime' => $rate['start_date_datetime'] ?? null,
                    'source' => 'zonneplan_api',
                ]
            );
        }

        return $result;
    }

    /**
     * @param  ZonneplanApiGasResponse  $data
     * @return array<int, GasRateDTO>
     */
    private function transformGasData(array $data): array
    {
        $result = [];

        foreach ($data['data'] as $rate) {
            $result[] = new GasRateDTO(
                periodStart: Carbon::createFromTimestamp((int) $rate['start_date'], self::TIMEZONE),
                periodEnd: Carbon::createFromTimestamp((int) $rate['end_date'], self::TIMEZONE),
                period: (string) $rate['period'],
                marketPrice: (int) $rate['market_price'],
                totalPriceTaxIncluded: (int) $rate['total_price_tax_included'],
                priceInclHandlingVat: (int) $rate['price_incl_handling_vat'],
                priceTaxWithVat: (int) $rate['price_tax_with_vat'],
                metadata: [
                    'start_date_datetime' => $rate['start_date_datetime'] ?? null,
                    'source' => 'zonneplan_api',
                ]
            );
        }

        return $result;
    }

    /**
     * @param  Carbon  $date
     * @return array<string, string>
     */
    private function getQueryParams(Carbon $date): array
    {
        $queryParams = [
            'secret' => $this->apiKey,
        ];

        // don't include date if it's today -- broken gas endpoint not showing anything when date is added
        if (!$date->isToday()) {
            $queryParams['date'] = $date->format('Y-m-d');
        }

        return $queryParams;
    }
}
