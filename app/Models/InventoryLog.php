<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $table = 'inventory_logs';

    protected $fillable = ['variant_id', 'action', 'quantity', 'previous_qty', 'new_qty', 'reference', 'notes', 'user_id'];

    public function variant() { return $this->belongsTo(ProductVariant::class, 'variant_id'); }
}
