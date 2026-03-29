<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   protected $guarded=[''];

   protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'order_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order tracking information.
     */
    public function tracking()
    {
        return $this->hasOne(OrderTracking::class);
    }

    /**
     * Get order status updates.
     */
    public function statusUpdates()
    {
        return $this->hasMany(OrderStatusUpdate::class)->orderBy('created_at', 'desc');
    }

    /**
     * Calculate total amount.
     */
    public function getTotalAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get formatted status.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'secondary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Scope for specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for orders within date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }
}
