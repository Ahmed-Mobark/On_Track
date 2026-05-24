<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PosSession extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $table = 'pos_sessions';

    protected $fillable = [
        'cashier_id', 'opening_cash', 'closing_cash', 'expected_cash',
        'total_sales', 'total_returns', 'total_cash', 'total_visa',
        'total_instapay', 'total_wallet', 'transaction_count',
        'notes', 'is_open', 'opened_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'opening_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_returns' => 'decimal:2',
        ];
    }

    public function cashier() { return $this->belongsTo(User::class, 'cashier_id'); }
    public function transactions() { return $this->hasMany(PosTransaction::class, 'session_id'); }
}
