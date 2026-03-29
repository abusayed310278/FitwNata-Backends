<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusUpdate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the status update.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin user who made the update.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
