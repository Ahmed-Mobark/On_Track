<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['user_id', 'title', 'message', 'is_read', 'type'];
    protected function casts(): array { return ['is_read' => 'boolean']; }
    public function user() { return $this->belongsTo(User::class); }
}
