<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkoutExerciseLog extends Model
{
    protected $guarded = [];

    public function assignment() { return $this->belongsTo(UserWorkoutAssignment::class,'assignment_id'); }
    public function exercise()   { return $this->belongsTo(Exercise::class); }
}
