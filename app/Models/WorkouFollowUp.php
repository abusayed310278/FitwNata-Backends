<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkouFollowUp extends Model
{
    protected $fillable = [
        'workout_log_id',
        'coach_id',
        'notes',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function log()
    {
        return $this->belongsTo(WorkoutLog::class, 'workout_log_id');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
