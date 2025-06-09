<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GasRate;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GasRate>
 */
final class GasRateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periodStart = $this->faker->dateTimeBetween('-1 year', 'now');
        $periodEnd = (clone $periodStart)->modify('+1 hour');

        return [
            'currency' => 'EUR',
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'rate_date' => $periodStart->format('Y-m-d'),
            'period' => $this->generatePeriodString($periodStart, $periodEnd),
            'market_price' => $this->faker->numberBetween(30000, 120000),
            'total_price_tax_included' => $this->faker->numberBetween(60000, 180000),
            'price_incl_handling_vat' => $this->faker->numberBetween(50000, 150000),
            'price_tax_with_vat' => $this->faker->numberBetween(8000, 30000),
            'metadata' => $this->faker->optional()->randomElement([
                ['source' => 'natural_gas', 'supplier' => 'main_grid'],
                ['source' => 'biogas', 'renewable_percentage' => 15],
                ['market_conditions' => 'volatile', 'demand_level' => 'high'],
                ['storage_level' => 'adequate', 'import_dependency' => 'medium'],
                null,
            ]),
        ];
    }

    public function withCurrency(string $currency): static
    {
        return $this->state(fn (): array => ['currency' => $currency]);
    }

    public function forPeriod(DateTimeInterface $start, DateTimeInterface $end): static
    {
        return $this->state(fn (): array => [
            'period_start' => $start,
            'period_end' => $end,
            'rate_date' => $start->format('Y-m-d'),
            'period' => $this->generatePeriodString($start, $end),
        ]);
    }

    public function biogas(): static
    {
        return $this->state(fn (): array => [
            'market_price' => $this->faker->numberBetween(40000, 140000), // Slightly higher for biogas
            'metadata' => [
                'source' => 'biogas',
                'renewable_percentage' => $this->faker->numberBetween(20, 100),
                'certification' => 'green_gas',
            ],
        ]);
    }

    public function highDemand(): static
    {
        return $this->state(fn (): array => [
            'market_price' => $this->faker->numberBetween(80000, 150000), // Higher prices during high demand
            'total_price_tax_included' => $this->faker->numberBetween(120000, 220000),
            'metadata' => [
                'demand_level' => 'high',
                'market_conditions' => 'tight',
                'season' => 'winter',
            ],
        ]);
    }

    public function lowDemand(): static
    {
        return $this->state(fn (): array => [
            'market_price' => $this->faker->numberBetween(20000, 60000), // Lower prices during low demand
            'total_price_tax_included' => $this->faker->numberBetween(40000, 100000),
            'metadata' => [
                'demand_level' => 'low',
                'market_conditions' => 'stable',
                'season' => 'summer',
            ],
        ]);
    }

    private function generatePeriodString(
        DateTimeInterface $start,
        DateTimeInterface $end
    ): string {
        return sprintf(
            '%s-%s',
            $start->format('H:i'),
            $end->format('H:i')
        );
    }
}
