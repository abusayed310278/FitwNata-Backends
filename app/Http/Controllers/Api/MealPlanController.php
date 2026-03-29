<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\MealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MealPlanController extends BaseApiController
{
    /**
     * Get all meal plans.
     */
    public function index(Request $request): JsonResponse
    {
        $mealPlans = MealPlan::paginate(10);
        return $this->paginatedSuccess($mealPlans, 'Meal plans retrieved successfully');
    }

    /**
     * Get recommended meal plans.
     */
    public function recommended(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->userProfile;

        $query = MealPlan::query();

        // Filter by user preferences if available
        if ($profile && $profile->preferred_recipe_type !== 'both') {
            // Add filtering logic based on recipe type
        }

        $mealPlans = $query->take(3)->get();

        return $this->success($mealPlans, 'Recommended meal plans retrieved');
    }

    /**
     * Get meal plan details.
     */
    public function show(MealPlan $mealPlan): JsonResponse
    {
        $mealPlan->load('recipes');
        return $this->success($mealPlan, 'Meal plan details retrieved');
    }

    /**
     * Get all recipes.
     */
    public function recipes(Request $request): JsonResponse
    {
        $query = Recipe::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('max_calories')) {
            $query->where('calories', '<=', $request->max_calories);
        }

        $recipes = $query->paginate(10);

        return $this->paginatedSuccess($recipes, 'Recipes retrieved successfully');
    }

    /**
     * Get recipe details.
     */
    public function recipeDetails(Recipe $recipe): JsonResponse
    {
        return $this->success($recipe, 'Recipe details retrieved');
    }

    /**
     * Get weekly meal plan.
     */
    public function weeklyPlan(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get user's current meal plan or default plan
        $mealPlan = MealPlan::first(); // You might want to implement user-specific meal plans

        if (!$mealPlan) {
            return $this->error('No meal plan available');
        }

        $weeklyPlan = [];

        for ($day = 1; $day <= 7; $day++) {
            $dayRecipes = $mealPlan->recipes()
                ->wherePivot('day_of_week', $day)
                ->get()
                ->groupBy('pivot.meal_type');

            $weeklyPlan['day_' . $day] = $dayRecipes;
        }

        return $this->success([
            'meal_plan' => $mealPlan,
            'weekly_schedule' => $weeklyPlan
        ], 'Weekly meal plan retrieved');
    }

    /**
     * Toggle recipe favorite.
     */
    public function toggleFavorite(Request $request, Recipe $recipe): JsonResponse
    {
        $user = $request->user();

        // You might want to create a UserFavoriteRecipe model
        // For now, we'll just return success

        return $this->success([
            'recipe_id' => $recipe->id,
            'is_favorite' => true // Toggle logic here
        ], 'Recipe favorite status updated');
    }
}
