<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasUuids;

    protected $fillable = [
        'code', 'type', 'value', 'min_order_value', 'max_uses',
        'used_count', 'is_active', 'is_first_order', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'is_active' => 'boolean',
            'is_first_order' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function isValid(float $orderTotal = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if ($this->min_order_value && $orderTotal < $this->min_order_value) return false;
        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        return match ($this->type) {
            'PERCENTAGE' => $orderTotal * ($this->value / 100),
            'FIXED_AMOUNT' => (float) $this->value,
            default => 0,
        };
    }

    public function orders() { return $this->hasMany(Order::class); }
    public function posTransactions() { return $this->hasMany(PosTransaction::class); }
}
