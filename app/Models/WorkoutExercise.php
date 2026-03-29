<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutExercise extends Model
{
   protected $guarded=[''];

   protected $table = 'workout_exercise';

   public $timestamps = false;
}
