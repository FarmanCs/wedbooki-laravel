<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'email',
        'password',
        'role',
        'twoFactorCode',
        'twoFactorCodeExpires',
    ];

    protected $hidden = [
        'password',
        'twoFactorCode',
    ];

    protected $casts = [
        'twoFactorCodeExpires' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

