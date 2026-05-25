<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'name_ar', 'slug', 'description', 'description_ar',
        'materials', 'care_instructions', 'sku', 'base_price', 'sale_price',
        'gender', 'is_active', 'is_featured', 'is_best_seller',
        'total_sold', 'avg_rating', 'total_reviews', 'seo_title', 'seo_desc',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'avg_rating' => 'decimal:2',
            'gender' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
        ];
    }

    public function categories() { return $this->belongsToMany(Category::class, 'product_categories'); }
    public function images() { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function tags() { return $this->hasMany(ProductTag::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function wishlistItems() { return $this->hasMany(WishlistItem::class); }
    public function cartItems() { return $this->hasMany(CartItem::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopeBestSeller($query) { return $query->where('is_best_seller', true); }
}
