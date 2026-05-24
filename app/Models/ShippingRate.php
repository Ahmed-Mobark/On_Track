<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasUuids;

    protected $fillable = ['governorate', 'city', 'cost', 'estimated_days', 'is_active'];

    protected function casts(): array
    {
        return ['cost' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public static function getCost(string $governorate, ?string $city = null): float
    {
        // Try exact city match first
        if ($city) {
            $rate = static::where('governorate', $governorate)
                ->where('city', $city)
                ->where('is_active', true)
                ->first();
            if ($rate) return (float) $rate->cost;
        }

        // Fallback to governorate default (city = null)
        $rate = static::where('governorate', $governorate)
            ->whereNull('city')
            ->where('is_active', true)
            ->first();

        return $rate ? (float) $rate->cost : 50; // Default 50 EGP if no rate set
    }
}
