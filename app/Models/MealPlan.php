<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MealPlan extends Model
{
    protected $guarded=[];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'meal_plan_recipe')
                    ->withPivot(['day_of_week', 'meal_type']);
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

    public function mealRecipes()
    {
        return $this->hasMany(MealPlanRecipe::class);
    }

}
