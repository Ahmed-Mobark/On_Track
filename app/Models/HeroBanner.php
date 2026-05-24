<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class HeroBanner extends Model
{
    use HasUuids;

    protected $fillable = ['title', 'subtitle', 'image', 'video_url', 'link', 'button_text', 'is_active', 'sort_order'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
}
