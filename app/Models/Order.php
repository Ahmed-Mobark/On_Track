<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_number', 'user_id', 'address_id', 'status', 'payment_method',
        'payment_type', 'payment_status', 'payment_proof', 'deposit_amount',
        'wallet_used', 'subtotal', 'shipping_cost', 'discount', 'total',
        'coupon_id', 'notes', 'shipping_company', 'tracking_number', 'shipment_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'wallet_used' => 'decimal:2',
        ];
    }

    public static function generateOrderNumber(): string
    {
        $last = static::orderBy('created_at', 'desc')->value('order_number');
        $nextNum = 1001;
        if ($last && preg_match('/OT-(\d+)/', $last, $m)) {
            $nextNum = (int) $m[1] + 1;
        }
        return 'OT-' . $nextNum;
    }

    public function user() { return $this->belongsTo(User::class); }
    public function address() { return $this->belongsTo(Address::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
}
