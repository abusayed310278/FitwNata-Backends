<?php

namespace App\Models;

use App\Models\UserWorkoutAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Workout extends Model
{
    protected $guarded=[''];

    protected $fillable = [
        'title',
        'description',
        'tips',
        'level',
        'duration',
        'duration_minutes',
        'type',
        'equipment',
        'calories_burned',
        'is_premium',
        'instructions',
        'image_url',
        'thumbnail_url',
        'published_at',
        'fitness_goals', 'training_location', 'health_conditions', 'gender_preference', 'user_id'
    ];

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'workout_exercise')
                    ->withPivot(['sets', 'reps', 'duration_seconds', 'order'])
                    ->orderBy('order');
    }

    public function assignments()
    {
        return $this->hasMany(UserWorkoutAssignment::class);
    }

    public function logs()
    {
        return $this->hasMany(WorkoutLog::class);
    }

    // public function logs()
    // {
    //     return $this->morphMany(UserLog::class, 'loggable');
    // }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function plans()
    {
        return $this->morphToMany(Plan::class, 'planable');
    }

    public function getDescriptionAttribute($value)
    {
        // If you want to return plain text (removing HTML)
        return strip_tags($value);

        // If you want to return clean HTML (keeping HTML tags but sanitizing them)
        // return HTMLPurifier::clean($value);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return asset('assets/images/default-workout.jpg');
                }

                // If full URL already
                if (preg_match('/^https?:\/\//', $value)) {
                    return $value;
                }

                // Otherwise, return relative public path
                return asset($value);
            }
        );
    }
}
