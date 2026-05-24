<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['product_id', 'url', 'public_id', 'alt', 'is_video', 'sort_order'];
    protected function casts(): array { return ['is_video' => 'boolean']; }
    public function product() { return $this->belongsTo(Product::class); }
}
