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

    /**
     * @return array<int, ElectricityRateDTO>
     *
     * @throws Exception
     */
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

            /** @var array{data: array<int, array{
             *     start_date: int|string,
             *     end_date: int|string,
             *     period: string,
             *     market_price: int|string,
             *     total_price_tax_included: int|string,
             *     price_incl_handling_vat: int|string,
             *     price_tax_with_vat: int|string,
             *     pricing_profile?: string|null,
             *     carbon_footprint_in_gram?: int|string|null,
             *     sustainability_score?: int|string|null,
             *     start_date_datetime?: string|null
             * }>} $data
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
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl.'/electricity/upcoming', [
                'date' => $date->format('Y-m-d'),
                'secret' => 'mzRqGxv9dzaatsFgcXoVJbh6pdMDwDxEWBhbZ89r',
            ]);

            if (! $response->successful()) {
                throw new Exception('API request failed: '.$response->status());
            }

            /** @var array{data: array<int, array{
             *     start_date: int|string,
             *     end_date: int|string,
             *     period: string,
             *     market_price: int|string,
             *     total_price_tax_included: int|string,
             *     price_incl_handling_vat: int|string,
             *     price_tax_with_vat: int|string,
             *     start_date_datetime?: string|null
             * }>} $data
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
     * @param array{data: array<int, array{
     *     start_date: int|string,
     *     end_date: int|string,
     *     period: string,
     *     market_price: int|string,
     *     total_price_tax_included: int|string,
     *     price_incl_handling_vat: int|string,
     *     price_tax_with_vat: int|string,
     *     pricing_profile?: string|null,
     *     carbon_footprint_in_gram?: int|string|null,
     *     sustainability_score?: int|string|null,
     *     start_date_datetime?: string|null
     * }>} $data
     * @return array<int, ElectricityRateDTO>
     */
    private function transformElectricityData(array $data): array
    {
        $result = [];

        foreach ($data['data'] as $rate) {
            $result[] = new ElectricityRateDTO(
                periodStart: Carbon::createFromTimestamp((int) $rate['start_date']),
                periodEnd: Carbon::createFromTimestamp((int) $rate['end_date']),
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
     * @param array{data: array<int, array{
     *     start_date: int|string,
     *     end_date: int|string,
     *     period: string,
     *     market_price: int|string,
     *     total_price_tax_included: int|string,
     *     price_incl_handling_vat: int|string,
     *     price_tax_with_vat: int|string,
     *     start_date_datetime?: string|null
     * }>} $data
     * @return array<int, GasRateDTO>
     */
    private function transformGasData(array $data): array
    {
        $result = [];

        foreach ($data['data'] as $rate) {
            $result[] = new GasRateDTO(
                periodStart: Carbon::createFromTimestamp((int) $rate['start_date']),
                periodEnd: Carbon::createFromTimestamp((int) $rate['end_date']),
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
}
