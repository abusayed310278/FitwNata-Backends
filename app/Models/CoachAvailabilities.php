<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CoachAvailabilities extends Model
{
    protected $guarded = [];

    protected $casts = [
        'blocked_date' => 'date',
        'is_blocked' => 'boolean',
    ];

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Scope for regular availability (not blocked times)
     */
    public function scopeRegular($query)
    {
        return $query->where('is_blocked', false);
    }

    /**
     * Scope for blocked times
     */
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }
}
