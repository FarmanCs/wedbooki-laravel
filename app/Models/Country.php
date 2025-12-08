<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'countries',
    ];

    protected $casts = [
        'countries' => 'array', // Cast JSON to array automatically
    ];
}
