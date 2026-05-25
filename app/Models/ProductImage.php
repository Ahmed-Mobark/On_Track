<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['product_id', 'url', 'public_id', 'alt', 'color_name', 'color_hex', 'is_video', 'sort_order'];
    protected $appends = ['image_url'];

    protected function casts(): array { return ['is_video' => 'boolean']; }

    public function getImageUrlAttribute(): string
    {
        $url = $this->url;
        if (str_starts_with($url, 'http')) return $url;
        return asset('storage/' . $url);
    }

    public function product() { return $this->belongsTo(Product::class); }
}
