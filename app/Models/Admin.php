<?php

namespace App\Models;

use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }
}
