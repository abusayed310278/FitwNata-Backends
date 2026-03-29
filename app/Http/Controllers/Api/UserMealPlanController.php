<?php

namespace App\Http\Controllers\Api;

use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserMealPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check the active subscription status
        $active = userActiveSubscription($user->id);
        $plan = !$active ? 0 : ($active->plan->isFree() ? 0 : 1);

        $query = MealPlan::with(['mealRecipes.recipe'])
            ->where(function ($q) use ($plan) {
                $q->where('is_premium', $plan)
                ->orWhere('is_premium', 0);
            });

        // Apply filters (if any)
        if ($request->filled('premium')) {
            $query->where('is_premium', $request->premium);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('goal')) {
            $query->where('goal', $request->goal);
        }

        $mealPlans = $query->paginate(10);

        return response()->json([
            'message' => 'Meal plans retrieved successfully',
            'data' => $mealPlans->items(),
            'pagination' => [
                'total' => $mealPlans->total(),
                'per_page' => $mealPlans->perPage(),
                'current_page' => $mealPlans->currentPage(),
                'last_page' => $mealPlans->lastPage(),
                'from' => $mealPlans->firstItem(),
                'to' => $mealPlans->lastItem(),
            ]
        ]);
    }
}
