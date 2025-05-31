<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\EnergyDataRepositoryInterface;
use App\Contracts\EnergyDataServiceInterface;
use App\DTOs\ElectricityRateDTO;
use App\Exceptions\EnergyDataException;
use App\Services\EnergyDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

final class EnergyDataServiceTest extends TestCase
{
    private $mockApiService;

    private $mockRepository;

    private EnergyDataService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockApiService = Mockery::mock(EnergyDataServiceInterface::class);
        $this->mockRepository = Mockery::mock(EnergyDataRepositoryInterface::class);
        $this->service = new EnergyDataService(
            $this->mockApiService,
            $this->mockRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_sync_electricity_rates_for_day_success(): void
    {
        Log::shouldReceive('info')->once();

        $date = Carbon::parse('2025-06-01');
        $dummyRates = [
            new ElectricityRateDTO(
                periodStart: $date->copy()->startOfDay(),
                periodEnd: $date->copy()->startOfDay()->addHour(),
                period: 'hourly',
                marketPrice: 202500,
                totalPriceTaxIncluded: 250000,
                priceInclHandlingVat: 240000,
                priceTaxWithVat: 230000,
                currency: 'EUR'
            ),
        ];

        $this->mockApiService->shouldReceive('getElectricityRates')
            ->once()
            ->with($date)
            ->andReturn($dummyRates);

        $this->mockRepository->shouldReceive('storeElectricityRates')
            ->once()
            ->with($dummyRates);

        $this->service->syncElectricityRatesForDay($date);

        $this->assertTrue(true); // Test passes if no exception is thrown
    }

    public function test_sync_electricity_rates_for_day_throws_exception_on_api_failure(): void
    {
        Log::shouldReceive('error')->once();

        $date = Carbon::parse('2025-06-01');
        $apiException = new Exception('API connection failed');

        $this->mockApiService->shouldReceive('getElectricityRates')
            ->once()
            ->with($date)
            ->andThrow($apiException);

        $this->expectException(EnergyDataException::class);
        $this->expectExceptionMessage('Failed to sync electricity rates for 2025-06-01');

        $this->service->syncElectricityRatesForDay($date);
    }

    public function test_sync_gas_rates_for_day_success(): void
    {
        Log::shouldReceive('info')->once();

        $date = Carbon::parse('2025-06-01');
        $dummyRates = []; // Assuming gas rates have their own DTO

        $this->mockApiService->shouldReceive('getGasRates')
            ->once()
            ->with($date)
            ->andReturn($dummyRates);

        $this->mockRepository->shouldReceive('storeGasRates')
            ->once()
            ->with($dummyRates);

        $this->service->syncGasRatesForDay($date);

        $this->assertTrue(true);
    }

    public function test_sync_all_rates_for_day(): void
    {
        Log::shouldReceive('info')->twice(); // Once for electricity, once for gas

        $date = Carbon::parse('2025-06-01');

        // Mock electricity rates
        $this->mockApiService->shouldReceive('getElectricityRates')
            ->once()
            ->with($date)
            ->andReturn([]);

        $this->mockRepository->shouldReceive('storeElectricityRates')
            ->once()
            ->with([]);

        // Mock gas rates
        $this->mockApiService->shouldReceive('getGasRates')
            ->once()
            ->with($date)
            ->andReturn([]);

        $this->mockRepository->shouldReceive('storeGasRates')
            ->once()
            ->with([]);

        $this->service->syncAllRatesForDay($date);

        $this->assertTrue(true);
    }

    public function test_sync_rates_for_date_range(): void
    {
        Log::shouldReceive('info')->times(4); // 2 days × 2 rate types

        $startDate = Carbon::parse('2025-06-01');
        $endDate = Carbon::parse('2025-06-02');

        // Mock for first day
        $this->mockApiService->shouldReceive('getElectricityRates')
            ->with($startDate)
            ->andReturn([]);
        $this->mockApiService->shouldReceive('getGasRates')
            ->with($startDate)
            ->andReturn([]);

        // Mock for second day
        $this->mockApiService->shouldReceive('getElectricityRates')
            ->with($endDate)
            ->andReturn([]);
        $this->mockApiService->shouldReceive('getGasRates')
            ->with($endDate)
            ->andReturn([]);

        $this->mockRepository->shouldReceive('storeElectricityRates')
            ->twice()
            ->with([]);
        $this->mockRepository->shouldReceive('storeGasRates')
            ->twice()
            ->with([]);

        $this->service->syncRatesForDateRange($startDate, $endDate);

        $this->assertTrue(true);
    }

    public function test_get_electricity_rates_for_day(): void
    {
        $date = Carbon::parse('2025-06-01');
        $expectedCollection = collect([]);

        $this->mockRepository->shouldReceive('getElectricityRatesForDay')
            ->once()
            ->with($date)
            ->andReturn($expectedCollection);

        $result = $this->service->getElectricityRatesForDay($date);

        $this->assertEquals($expectedCollection, $result);
    }

    public function test_get_available_electricity_days(): void
    {
        $expectedCollection = collect(['2025-06-01', '2025-06-02']);

        $this->mockRepository->shouldReceive('getAvailableElectricityDays')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->service->getAvailableElectricityDays();

        $this->assertEquals($expectedCollection, $result);
    }
}
