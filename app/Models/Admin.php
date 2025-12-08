<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Mass assignable fields
    protected $fillable = [
        'first_name',
        'email',
        'password',
        'role',
        'two_factor_code',
        'two_factor_code_expires',
    ];

    // Hidden fields for arrays / JSON
    protected $hidden = [
        'password',
        'two_factor_code',
    ];

    // Attribute casting
    protected $casts = [
        'two_factor_code_expires' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
