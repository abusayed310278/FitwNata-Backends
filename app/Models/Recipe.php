<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Recipe extends Model
{
    protected $guarded=[''];

    protected $fillable = [
        'title',
        'description',
        'prep_time',
        'cook_time',
        'servings',
        'calories',
        'protein',
        'carbs',
        'fat',
        'difficulty',
        'ingredients',
        'instructions',
        'tags',
        'is_premium',
        'image_url',
        'user_id'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'tags' => 'array',
        'is_premium' => 'boolean',
    ];

    public function mealPlans()
    {
        return $this->belongsToMany(MealPlan::class, 'meal_plan_recipe');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return asset('assets/images/default-meal.jpg');
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
