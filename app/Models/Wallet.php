<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasUuids;

    protected $fillable = ['user_id', 'balance', 'points'];

    protected function casts(): array
    {
        return ['balance' => 'decimal:2', 'points' => 'integer'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(WalletTransaction::class)->latest(); }

    // ========== Balance Operations ==========

    public function addBalance(float $amount, string $description, ?string $refType = null, ?string $refId = null): WalletTransaction
    {
        $this->increment('balance', $amount);
        return $this->transactions()->create([
            'type' => 'CREDIT',
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    public function deductBalance(float $amount, string $description, ?string $refType = null, ?string $refId = null): ?WalletTransaction
    {
        if ($this->balance < $amount) return null;
        $this->decrement('balance', $amount);
        return $this->transactions()->create([
            'type' => 'DEBIT',
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    // ========== Points Operations ==========

    public function addPoints(int $points, string $description, ?string $refType = null, ?string $refId = null): WalletTransaction
    {
        $this->increment('points', $points);
        return $this->transactions()->create([
            'type' => 'CREDIT',
            'points' => $points,
            'description' => $description,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    public function deductPoints(int $points, string $description, ?string $refType = null, ?string $refId = null): ?WalletTransaction
    {
        if ($this->points < $points) return null;
        $this->decrement('points', $points);
        return $this->transactions()->create([
            'type' => 'DEBIT',
            'points' => $points,
            'description' => $description,
            'reference_type' => $refType,
            'reference_id' => $refId,
        ]);
    }

    // ========== Points Calculation ==========

    public static function calculatePoints(float $amount, string $paymentType = 'SHIPPING_ONLY'): int
    {
        $pointsPerEgp = (float) SiteSetting::get('points_per_egp', 1);
        $fullPaymentMultiplier = (float) SiteSetting::get('full_payment_points_multiplier', 2);

        $points = (int) floor($amount * $pointsPerEgp);
        if ($paymentType === 'FULL') {
            $points = (int) floor($points * $fullPaymentMultiplier);
        }
        return $points;
    }

    public static function pointsToEgp(int $points): float
    {
        $pointsPerEgp = (float) SiteSetting::get('points_redemption_rate', 10); // 10 points = 1 EGP
        return $pointsPerEgp > 0 ? round($points / $pointsPerEgp, 2) : 0;
    }

    public static function egpToPoints(float $egp): int
    {
        $pointsPerEgp = (float) SiteSetting::get('points_redemption_rate', 10);
        return (int) floor($egp * $pointsPerEgp);
    }
}
