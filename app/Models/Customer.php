<?php

namespace App\Models;

use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'address', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function hasPurchased(int $productId): bool
    {
        return $this->orders()
            ->whereIn('status', [1, 2, 3]) // confirmed, shipping, delivered
            ->whereHas('details', fn($q) => $q->where('product_id', $productId))
            ->exists();
    }

    public function hasRated(int $productId): bool
    {
        return $this->ratings()->where('product_id', $productId)->exists();
    }
}
