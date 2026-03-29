<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'workout_id',
        'user_id',
        'completed_at',
        'feedback',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followUps()
    {
        return $this->hasMany(WorkoutFollowUp::class);
    }
}
