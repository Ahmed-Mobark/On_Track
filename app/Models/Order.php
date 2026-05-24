<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_number', 'user_id', 'address_id', 'status', 'payment_method',
        'payment_status', 'subtotal', 'shipping_cost', 'discount', 'total',
        'coupon_id', 'notes', 'shipping_company', 'tracking_number', 'shipment_status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public static function generateOrderNumber(): string
    {
        return 'OT-' . time() . '-' . strtoupper(substr(uniqid(), -5));
    }

    public function user() { return $this->belongsTo(User::class); }
    public function address() { return $this->belongsTo(Address::class); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
}
