<?php

namespace App\Http\Controllers\Api;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Exercises fetched successfully',
            'data' => $exercises
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:exercises,name',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'equipment_needed' => 'nullable|string|max:255',
            'calories_per_rep_or_second' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|array',
            'tips' => 'nullable|array',
        ]);

        $validated['instructions'] = $request->instructions ? implode(',', $request->instructions) : null;
        $validated['tips'] = $request->tips ? implode(',', $request->tips) : null;
        $validated['user_id'] = Auth::id();
        $validated['equipment'] = $request->equipment_needed;

        $exercise = Exercise::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Exercise created successfully',
            'data' => $exercise
        ]);
    }

    public function show($id)
    {
        $exercise = Exercise::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$exercise) {
            return response()->json(['status' => false, 'message' => 'Exercise not found']);
        }

        return response()->json(['status' => true, 'data' => $exercise]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:exercises,id',
            'name' => ['required','string','max:255', Rule::unique('exercises','name')->ignore($request->id)],
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'equipment_needed' => 'nullable|string|max:255',
            'calories_per_rep_or_second' => 'nullable|numeric|min:0',
            'instructions' => 'nullable|array',
            'tips' => 'nullable|array',
        ]);

        $exercise = Exercise::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();

        $validated['instructions'] = $request->instructions ? implode(',', $request->instructions) : null;
        $validated['tips'] = $request->tips ? implode(',', $request->tips) : null;

        $exercise->update($validated);

        return response()->json(['status' => true, 'message' => 'Exercise updated successfully', 'data' => $exercise]);
    }

    public function destroy($id)
    {
        $exercise = Exercise::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $exercise->workouts()->detach();
        $exercise->delete();

        return response()->json(['status' => true, 'message' => 'Exercise deleted successfully']);
    }
}
