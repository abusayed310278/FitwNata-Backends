<?php

namespace App\Http\Controllers\Api;

use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NutritionistMealPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = MealPlan::with('mealRecipes.recipe')->where('user_id', Auth::id());

        if ($request->filled('premium')) {
            $query->where('is_premium', $request->boolean('premium'));
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('goal')) {
            $query->where('goal', $request->goal);
        }

        $plans = $query->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'message' => 'Meal plans fetched successfully',
            'data' => $plans
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:30',
            'total_calories' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'goal' => 'required|in:general_health,weight_loss,muscle_gain',
            'is_premium' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'assignments' => 'nullable|array',
            'assignments.*.day_of_week' => 'required_with:assignments|integer|min:1|max:7',
            'assignments.*.meal_type' => 'required_with:assignments|in:breakfast,lunch,dinner,snack',
            'assignments.*.recipe_id' => 'required_with:assignments|exists:recipes,id',
        ]);

        $data = collect($validated)->except('assignments', 'image')->toArray();
        $data['user_id'] = Auth::id();
        $data['is_premium'] = $request->boolean('is_premium');

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/mealplans');
        }

        $plan = MealPlan::create($data);

        if (!empty($validated['assignments'])) {
            foreach ($validated['assignments'] as $assign) {
                DB::table('meal_plan_recipe')->insert([
                    'meal_plan_id' => $plan->id,
                    'recipe_id' => $assign['recipe_id'],
                    'day_of_week' => $assign['day_of_week'],
                    'meal_type' => $assign['meal_type'],
                ]);
            }
        }

        $plan->load('recipes');

        return response()->json([
            'status' => true,
            'message' => 'Meal plan created successfully',
            'data' => $plan
        ]);
    }

    public function show($id)
    {
        $plan = MealPlan::with('recipes')
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$plan) {
            return response()->json(['status' => false, 'message' => 'Meal plan not found']);
        }

        return response()->json(['status' => true, 'data' => $plan]);
    }

    // public function update(Request $request)
    // {
    //     $validated = $request->validate([
    //         'id' => 'required',
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'duration_days' => 'required|integer|min:1|max:30',
    //         'total_calories' => 'required|integer|min:1',
    //         'difficulty' => 'required|in:easy,medium,hard',
    //         'goal' => 'required|in:general_health,weight_loss,muscle_gain',
    //         'is_premium' => 'boolean',
    //         'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //     ]);

    //     $plan = MealPlan::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();

    //     $data = $validated;
    //     $data['is_premium'] = $request->boolean('is_premium');

    //     if ($request->hasFile('image')) {
    //         $data['image_url'] = uploadImage($request->image, 'images/mealplans');
    //     }

    //     unset($data['image']);

    //     $plan->update($data);

    //     return response()->json(['status' => true, 'message' => 'Meal plan updated successfully', 'data' => $plan]);
    // }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:meal_plans,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:30',
            'total_calories' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'goal' => 'required|in:general_health,weight_loss,muscle_gain',
            'is_premium' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'assignments' => 'nullable|array',
            'assignments.*.day_of_week' => 'required_with:assignments|integer|min:1|max:7',
            'assignments.*.meal_type' => 'required_with:assignments|in:breakfast,lunch,dinner,snack',
            'assignments.*.recipe_id' => 'required_with:assignments|exists:recipes,id',
        ]);

        $plan = MealPlan::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = collect($validated)->except('assignments', 'image')->toArray();
        $data['is_premium'] = $request->boolean('is_premium');

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->file('image'), 'images/mealplans');
        }

        $plan->update($data);

        // 🔹 Handle assignments (recipes)
        if (!empty($validated['assignments'])) {
            DB::table('meal_plan_recipe')->where('meal_plan_id', $plan->id)->delete();

            foreach ($validated['assignments'] as $assign) {
                DB::table('meal_plan_recipe')->insert([
                    'meal_plan_id' => $plan->id,
                    'recipe_id' => $assign['recipe_id'],
                    'day_of_week' => $assign['day_of_week'],
                    'meal_type' => $assign['meal_type'],
                ]);
            }
        }

        $plan->load('mealRecipes.recipe');

        return response()->json([
            'status' => true,
            'message' => 'Meal plan updated successfully',
            'data' => $plan,
        ]);
    }

    public function destroy($id)
    {
        $plan = MealPlan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $plan->delete();

        return response()->json(['status' => true, 'message' => 'Meal plan deleted successfully']);
    }

    public function assignRecipes(Request $request, $id)
    {
        $plan = MealPlan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.day_of_week' => 'required|integer|min:1|max:7',
            'assignments.*.meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'assignments.*.recipe_id' => 'required|exists:recipes,id',
        ]);

        DB::table('meal_plan_recipe')->where('meal_plan_id', $plan->id)->delete();

        foreach ($validated['assignments'] as $assign) {
            DB::table('meal_plan_recipe')->insert([
                'meal_plan_id' => $plan->id,
                'recipe_id' => $assign['recipe_id'],
                'day_of_week' => $assign['day_of_week'],
                'meal_type' => $assign['meal_type'],
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Recipes assigned successfully']);
    }
}
