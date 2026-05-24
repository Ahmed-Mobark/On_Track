<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasUuids;

    protected $fillable = ['product_id', 'size', 'color', 'color_hex', 'quantity', 'price', 'sku'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function cartItems() { return $this->hasMany(CartItem::class, 'variant_id'); }
    public function orderItems() { return $this->hasMany(OrderItem::class, 'variant_id'); }
    public function inventoryLogs() { return $this->hasMany(InventoryLog::class, 'variant_id'); }
}
