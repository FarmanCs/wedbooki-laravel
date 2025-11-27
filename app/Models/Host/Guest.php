<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'full_name',
        'phone_no',
        'mobile_no',
        'address',
        'state',
        'city',
        'zipcode',
        'is_joining',
    ];

    /**
     * A guest can belong to many guest groups.
     */
    public function groups()
    {
        return $this->belongsToMany(GuestGroup::class, 'guest_group_guest', 'guest_id', 'guest_group_id')
            ->withTimestamps();
    }
}
