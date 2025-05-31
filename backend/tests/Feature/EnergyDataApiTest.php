<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EnergyDataApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_electricity_rates_endpoint(): void
    {
        $response = $this->getJson('/api/v1/energy/electricity');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['count', 'type'],
            ]);
    }
}
