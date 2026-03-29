<?php

namespace App\Http\Controllers\Nutritionist;

use App\Models\Recipe;
use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MealPlanController extends Controller
{
    /**
     * Display a listing of meal plans with filters.
     */
    public function index(Request $request)
    {
        $query = MealPlan::query()->where('user_id', Auth::Id())->latest();

        // Apply filters
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

        return view('nutritionist.mealplans.index', compact('mealPlans'));
    }

    /**
     * Show the form for creating a new meal plan.
     */
    public function create()
    {
        return view('nutritionist.mealplans.create');
    }

    /**
     * Store a newly created meal plan in storage.
     */
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Remove the temporary 'image' key from array
        $data = collect($validated)->except('image')->toArray();

        $data['user_id'] = auth()->id();
        $data['is_premium'] = $request->has('is_premium');

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/mealplans');
        }

        MealPlan::create($data);

        return redirect()->route('nutritionist.mealplans.index')
            ->with('success', 'Meal Plan created successfully!');
    }

    /**
     * Show the form for editing the specified meal plan.
     */
    public function edit(MealPlan $mealPlan)
    {
        return view('nutritionist.mealplans.edit', compact('mealPlan'));
    }

    /**
     * Update the specified meal plan in storage.
     */
    public function update(Request $request, MealPlan $mealPlan)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:30',
            'total_calories' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'goal' => 'required|in:general_health,weight_loss,muscle_gain',
            'is_premium' => 'boolean',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = collect($validated)->except('image')->toArray();
        $data['is_premium'] = $request->has('is_premium');

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/mealplans');
        }

        $mealPlan->update($data);

        return redirect()->route('nutritionist.mealplans.index')
            ->with('success', 'Meal Plan updated successfully!');
    }

    /**
     * Display the specified meal plan.
     */
    public function show(MealPlan $mealPlan)
    {
        $mealPlan->load('recipes'); // include assigned recipes
        return view('nutritionist.mealplans.show', compact('mealPlan'));
    }

    /**
     * Remove the specified meal plan from storage.
     */
    public function destroy(MealPlan $mealPlan)
    {
        $mealPlan->delete();

        return back()->with('success', 'Meal Plan deleted successfully!');
    }

    public function assignRecipes(MealPlan $mealPlan)
    {
        $recipes = Recipe::where('user_id', auth()->id())->orderBy('title')->get();

        $mealPlan->load('recipes');

        $existingAssignments = $mealPlan->recipes->map(function ($recipe) {
            return [
                'recipe_id' => $recipe->id,
                'day_of_week' => $recipe->pivot->day_of_week,
                'meal_type' => $recipe->pivot->meal_type,
            ];
        });

        return view('nutritionist.mealplans.assign', compact('mealPlan', 'recipes', 'existingAssignments'));
    }


    public function storeAssignedRecipes(Request $request, MealPlan $mealPlan)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.day_of_week' => 'required|integer|min:1|max:7',
            'assignments.*.meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'assignments.*.recipe_id' => 'required|exists:recipes,id',
        ]);

        DB::table('meal_plan_recipe')->where('meal_plan_id', $mealPlan->id)->delete();

        foreach ($validated['assignments'] as $assign) {
            DB::table('meal_plan_recipe')->insert([
                'meal_plan_id' => $mealPlan->id,
                'recipe_id' => $assign['recipe_id'],
                'day_of_week' => $assign['day_of_week'],
                'meal_type' => $assign['meal_type'],
            ]);
        }

        return redirect()->route('nutritionist.mealplans.show', $mealPlan->id)
            ->with('success', 'Recipes assigned successfully!');
    }
}