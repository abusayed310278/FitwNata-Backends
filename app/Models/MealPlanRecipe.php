<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlanRecipe extends Model
{
    protected $table = 'meal_plan_recipe';
    
    protected $guarded=[];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
