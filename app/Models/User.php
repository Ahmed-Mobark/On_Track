<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable;

    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'phone', 'role', 'avatar', 'is_active',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['ADMIN', 'SUPER_ADMIN']);
    }

    public function isCashier(): bool
    {
        return $this->role === 'CASHIER';
    }

    public function addresses() { return $this->hasMany(Address::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function wishlistItems() { return $this->hasMany(WishlistItem::class); }
    public function cartItems() { return $this->hasMany(CartItem::class); }
    public function posSessions() { return $this->hasMany(PosSession::class, 'cashier_id'); }
    public function posTransactions() { return $this->hasMany(PosTransaction::class, 'cashier_id'); }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet ?? $this->wallet()->create(['balance' => 0, 'points' => 0]);
    }
}
