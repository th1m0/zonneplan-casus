<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ElectricityRate;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ElectricityRate>
 */
final class ElectricityRateFactory extends Factory
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
            'market_price' => $this->faker->numberBetween(50000, 200000), // 0.05-0.20 EUR in micro-units
            'total_price_tax_included' => $this->faker->numberBetween(80000, 300000),
            'price_incl_handling_vat' => $this->faker->numberBetween(70000, 250000),
            'price_tax_with_vat' => $this->faker->numberBetween(10000, 50000),
            'pricing_profile' => $this->faker->optional()->randomElement([
                'standard',
                'peak',
                'off-peak',
                'dynamic',
            ]),
            'carbon_footprint_in_gram' => $this->faker->optional()->numberBetween(100, 800),
            'sustainability_score' => $this->faker->optional()->numberBetween(1, 100),
            'metadata' => $this->faker->optional()->randomElement([
                ['source' => 'wind', 'region' => 'north'],
                ['source' => 'solar', 'region' => 'south'],
                ['weather_impact' => 'high', 'demand_level' => 'peak'],
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

    public function withPricingProfile(string $profile): static
    {
        return $this->state(fn (): array => ['pricing_profile' => $profile]);
    }

    public function sustainable(): static
    {
        return $this->state(fn (): array => [
            'carbon_footprint_in_gram' => $this->faker->numberBetween(50, 200),
            'sustainability_score' => $this->faker->numberBetween(80, 100),
            'metadata' => ['source' => 'renewable', 'certification' => 'green'],
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
