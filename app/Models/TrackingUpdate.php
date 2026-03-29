<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingUpdate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /**
     * Get the order tracking that owns the update.
     */
    public function orderTracking()
    {
        return $this->belongsTo(OrderTracking::class);
    }
}
