<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkoutAssignment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'scheduled_for' => 'date',
        'started_at'    => 'datetime',
        'completed_at'  => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function workout() { return $this->belongsTo(Workout::class); }
    public function exerciseLogs() { return $this->hasMany(UserWorkoutExerciseLog::class, 'assignment_id'); }

    public function scopeIncomplete($q) { return $q->whereIn('status',['pending','started']); }
}