<?php

namespace App\Models\Host;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuestGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'host_id',
    ];

    /**
     * A guest group belongs to a host.
     */
    public function host()
    {
        return $this->belongsTo(Host::class);
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class, 'guest_group_guest');
    }
}
