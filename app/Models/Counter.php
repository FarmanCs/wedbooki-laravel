<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    public $timestamps = false;
}
