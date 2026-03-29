<?php

namespace App\Http\Controllers\Api;

use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NutritionistRecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::where('user_id', Auth::id());

        if ($request->filled('premium')) {
            $query->where('is_premium', $request->boolean('premium'));
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $recipes = $query->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'message' => 'Recipes fetched successfully',
            'data' => $recipes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|string|max:255',
            'instructions' => 'required|array|min:1',
            'instructions.*' => 'required|string|max:255',
            'prep_time' => 'required|integer|min:0',
            'cook_time' => 'required|integer|min:0',
            'servings' => 'required|integer|min:1',
            'calories' => 'required|integer|min:1',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
            'tags' => 'nullable|string',
            'is_premium' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $validated;
        $data['user_id'] = Auth::id();
        $data['is_premium'] = $request->boolean('is_premium');

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/recipes');
        }

        $recipe = Recipe::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Recipe created successfully',
            'data' => $recipe
        ]);
    }

    public function show($id)
    {
        $recipe = Recipe::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Recipe not found']);
        }

        return response()->json(['status' => true, 'data' => $recipe]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:recipes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'required|string|max:255',
            'instructions' => 'required|array|min:1',
            'instructions.*' => 'required|string|max:255',
            'prep_time' => 'required|integer|min:0',
            'cook_time' => 'required|integer|min:0',
            'servings' => 'required|integer|min:1',
            'calories' => 'required|integer|min:1',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
            'tags' => 'nullable|string',
            'is_premium' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $recipe = Recipe::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();

        $data = $validated;
        $data['is_premium'] = $request->boolean('is_premium');

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/recipes');
        }

        $recipe->update($data);

        return response()->json(['status' => true, 'message' => 'Recipe updated successfully']);
    }

    public function destroy($id)
    {
        $recipe = Recipe::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $recipe->delete();

        return response()->json(['status' => true, 'message' => 'Recipe deleted successfully']);
    }
}
