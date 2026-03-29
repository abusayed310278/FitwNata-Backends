<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $guarded=[];


    public function workouts()
    {
        return $this->belongsToMany(Workout::class, 'workout_exercise')
                    ->withPivot(['sets', 'reps', 'duration_seconds', 'order']);
    }
}
