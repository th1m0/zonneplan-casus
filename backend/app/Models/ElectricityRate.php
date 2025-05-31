<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ElectricityRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_start',
        'period_end',
        'rate_date',
        'period',
        'market_price',
        'total_price_tax_included',
        'price_incl_handling_vat',
        'price_tax_with_vat',
        'pricing_profile',
        'carbon_footprint_in_gram',
        'sustainability_score',
        'currency',
        'metadata',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'rate_date' => 'date',
        'market_price' => 'integer',
        'total_price_tax_included' => 'integer',
        'price_incl_handling_vat' => 'integer',
        'price_tax_with_vat' => 'integer',
        'carbon_footprint_in_gram' => 'integer',
        'sustainability_score' => 'integer',
        'metadata' => 'array',
    ];

    // Primary scope - get all data for a specific day
    public function scopeForDay($query, Carbon $date)
    {
        return $query->where('rate_date', $date->toDateString())
            ->orderBy('period_start');
    }

    // Get all available days
    public function scopeAvailableDays($query)
    {
        return $query->select('rate_date')
            ->distinct()
            ->orderBy('rate_date', 'desc');
    }

    // Check if we have complete data for a day (should be 24 hours)
    public function scopeCompleteDay($query, Carbon $date)
    {
        return $query->forDay($date)->havingRaw('COUNT(*) = 24');
    }

    // Get latest available day
    public function scopeLatestDay($query)
    {
        return $query->orderBy('rate_date', 'desc')->limit(24);
    }

    // Accessors for euro conversion
    public function getMarketPriceInEurosAttribute(): float
    {
        return $this->market_price / 1000000;
    }

    public function getTotalPriceInEurosAttribute(): float
    {
        return $this->total_price_tax_included / 1000000;
    }

    public function getPriceInclHandlingVatInEurosAttribute(): float
    {
        return $this->price_incl_handling_vat / 1000000;
    }

    public function getPriceTaxWithVatInEurosAttribute(): float
    {
        return $this->price_tax_with_vat / 1000000;
    }
}
