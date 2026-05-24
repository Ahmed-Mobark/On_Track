<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['product_id', 'tag'];
    public function product() { return $this->belongsTo(Product::class); }
}
