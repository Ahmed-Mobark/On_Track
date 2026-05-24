<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasUuids;

    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'images', 'is_verified'];

    protected function casts(): array
    {
        return ['images' => 'array', 'is_verified' => 'boolean'];
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function user() { return $this->belongsTo(User::class); }
}
