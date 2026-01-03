<?php

namespace App\Models\Host;

use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Model;
use App\Models\Host\Host;


class Favourites extends Model
{
    protected $table = 'favourites';

    // Mass assignable fields
    protected $fillable = [
        'host_id',
        'business_id',
    ];

    /**
     * A favorite belongs to one host
     */
    public function host()
    {
        return $this->belongsTo(Host::class, 'host_id');
    }

    /**
     * A favorite belongs to one vendor
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
