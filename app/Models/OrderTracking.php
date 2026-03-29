<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    protected $guarded = [];

    protected $table = 'order_tracking'; 

    protected $casts = [
        'estimated_delivery' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the order that owns the tracking.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get tracking updates.
     */
    public function updates()
    {
        return $this->hasMany(TrackingUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get current status.
     */
    public function getCurrentStatusAttribute()
    {
        return $this->updates()->first()?->status ?? 'pending';
    }
}
