<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    //
    protected $fillable = [
        'event_type',
        'checklist_items',
    ];

    protected $casts = [
        'checklist_items' => 'json'
    ];

}
