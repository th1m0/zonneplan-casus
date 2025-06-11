<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\GasRateFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class GasRate extends Model
{
    /** @use HasFactory<GasRateFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'period_start',
        'period_end',
        'rate_date',
        'period',
        'market_price',
        'total_price_tax_included',
        'price_incl_handling_vat',
        'price_tax_with_vat',
        'currency',
        'metadata',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'rate_date' => 'date',
        'market_price' => 'integer',
        'total_price_tax_included' => 'integer',
        'price_incl_handling_vat' => 'integer',
        'price_tax_with_vat' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * @param  Builder<GasRate>  $query
     * @return Builder<GasRate>
     */
    public function scopeForDay(Builder $query, Carbon $date): Builder
    {
        return $query->where('rate_date', $date->toDateString())
            ->orderBy('period_start'); // Added ordering for consistency
    }

    /**
     * @param  Builder<GasRate>  $query
     * @return Builder<GasRate>
     */
    public function scopeCompleteDay(Builder $query, Carbon $date): Builder
    {
        return $query->forDay($date)->havingRaw('COUNT(*) = 24');
    }

    /**
     * @param  Builder<GasRate>  $query
     * @return Builder<GasRate>
     */
    public function scopeLatestDay(Builder $query): Builder
    {
        return $query->orderBy('rate_date', 'desc')->limit(24);
    }

    // Accessors for euro conversion
    public function getMarketPriceInEurosAttribute(): float
    {
        return $this->market_price / 10000000;
    }

    public function getTotalPriceInEurosAttribute(): float
    {
        return $this->total_price_tax_included / 10000000;
    }

    public function getPriceInclHandlingVatInEurosAttribute(): float
    {
        return $this->price_incl_handling_vat / 10000000;
    }

    public function getPriceTaxWithVatInEurosAttribute(): float
    {
        return $this->price_tax_with_vat / 10000000;
    }

    // Method accessors
    public function getMarketPriceInEuros(): float
    {
        return $this->market_price_in_euros;
    }

    public function getTotalPriceInEuros(): float
    {
        return $this->total_price_in_euros;
    }

    public function getPriceInclHandlingVatInEuros(): float
    {
        return $this->price_incl_handling_vat_in_euros;
    }

    public function getPriceTaxWithVatInEuros(): float
    {
        return $this->price_tax_with_vat_in_euros;
    }
}
