<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PosTransaction extends Model
{
    use HasUuids;

    protected $table = 'pos_transactions';

    protected $fillable = [
        'transaction_number', 'session_id', 'cashier_id', 'customer_name',
        'customer_phone', 'subtotal', 'discount', 'tax', 'total',
        'payment_method', 'amount_paid', 'change_amount', 'status',
        'coupon_id', 'notes', 'return_reason', 'original_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    public static function generateNumber(string $prefix = 'POS'): string
    {
        return $prefix . '-' . time() . '-' . strtoupper(substr(uniqid(), -4));
    }

    public function session() { return $this->belongsTo(PosSession::class, 'session_id'); }
    public function cashier() { return $this->belongsTo(User::class, 'cashier_id'); }
    public function coupon() { return $this->belongsTo(Coupon::class); }
    public function items() { return $this->hasMany(PosTransactionItem::class, 'transaction_id'); }
}
