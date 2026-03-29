<?php

namespace App\Http\Controllers\Nutritionist;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::where('user_id', Auth::id());

        if ($request->filled('premium')) {
            $query->where('is_premium', $request->premium === '1');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $recipes = $query->latest()->paginate(10)->appends($request->query());

        return view('nutritionist.recipes.index', compact('recipes'));
    }

    public function create()
    {
        return view('nutritionist.recipes.create');
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/recipes');
        }

        $data['ingredients'] = $request->ingredients;
        $data['instructions'] = $request->instructions;
        $data['user_id'] = auth()->id();

        Recipe::create($data);

        return redirect()->route('nutritionist.recipes.index')->with('success', 'Recipe created successfully!');
    }

    public function edit(Recipe $recipe)
    {
        return view('nutritionist.recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
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
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            $data['image_url'] = uploadImage($request->image, 'images/recipes');
        }

        $data['ingredients'] = $request->ingredients;
        $data['instructions'] = $request->instructions;
        $data['is_premium'] = $request->has('is_premium') ? 1 : 0;

        $recipe->update($data);

        return redirect()->route('nutritionist.recipes.index')->with('success', 'Recipe updated successfully!');
    }

    public function show(Recipe $recipe)
    {
        return view('nutritionist.recipes.show', compact('recipe'));
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return back()->with('success', 'Recipe deleted successfully!');
    }
}