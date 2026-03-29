<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'gender',
        'date_of_birth',
        'health_conditions',
        'preferred_workout_types',
        'training_location',
        'fitness_goals',
        'training_level',
        'weekly_training_objective',
        'equipment_availability',
        'nutrition_knowledge_level',
        'preferred_recipe_type',
        'weight_kg',
        'height_cm',
        'profile_image_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'health_conditions' => 'array',
        'preferred_workout_types' => 'array',
        'fitness_goals' => 'array',
        'equipment_availability' => 'array',
        'date_of_birth' => 'date',
    ];

    protected function profileImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) =>  is_null($value) ? asset('assets/images/default.png') : $value,
        );
    }

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
