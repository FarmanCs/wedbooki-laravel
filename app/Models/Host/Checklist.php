<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'wedding_date',
        'checklist_items',
    ];


    protected $casts = [
        'wedding_date'    => 'date',
        'checklist_items' => 'array', // Cast JSON to array automatically
    ];


    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id', 'id');
    }

    /**
     * Accessor to get checklist items as collection
     */
    public function getChecklistItemsAttribute($value)
    {
        return collect(json_decode($value, true));
    }

    /**
     * Mutator to ensure checklist items are stored as JSON
     */
    public function setChecklistItemsAttribute($value)
    {
        $this->attributes['checklist_items'] = json_encode($value);
    }
}

