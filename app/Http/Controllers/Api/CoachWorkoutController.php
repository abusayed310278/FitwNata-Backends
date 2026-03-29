<?php

namespace App\Http\Controllers\Api;

use App\Models\Workout;
use Illuminate\Http\Request;
use App\Models\WorkoutExercise;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachWorkoutController extends Controller
{
    public function index()
    {
        $workouts = Workout::with('exercises')->withCount('exercises')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(['status' => true, 'message' => 'Record found successfully', 'data' => $workouts]);
    }

    public function show($id)
    {
        $workout = Workout::with(['tags:id,name', 'exercises'])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$workout) {
            return response()->json(['status' => false, 'message' => 'Workout not found']);
        }

        return response()->json(['status' => true, 'data' => $workout]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'required|integer|min:1',
            'calories_burned' => 'nullable|integer|min:0',
            'thumbnail_url' => 'nullable|url',
            'fitness_goals' => 'required|string',
            'training_location' => 'required|string',
            'health_conditions' => 'required|string',
            'gender_preference' => 'required|string',
            'equipment' => 'required|string',
            'type' => 'required|string',
            'plan_type' => 'required|in:free,premium',
            'published_at' => 'required|date',
            'exercises' => 'array',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sets' => 'nullable|integer|min:1',
            'exercises.*.reps' => 'nullable|integer|min:1',
            'exercises.*.duration_seconds' => 'nullable|integer|min:1',
            'exercises.*.order' => 'nullable|integer|min:1',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10248',
        ]);

        if ($request->filled('exercises')) {
            $orders = collect($request->input('exercises'))->pluck('order')->filter();

            // Check duplicates in request
            $duplicates = $orders->duplicates();
            if ($duplicates->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate "order" values found in your exercise list: ' .
                        $duplicates->implode(', ') .
                        '. Each exercise must have a unique order number.'
                ], 422);
            }

            // Check if any of these order values already exist in DB (for same user’s workouts)
            $existingOrders = WorkoutExercise::whereIn('order', $orders)
                ->whereIn('workout_id', Workout::where('user_id', Auth::id())->pluck('id'))
                ->pluck('order')
                ->unique();

            if ($existingOrders->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The following order numbers are already used in your previous workouts: ' .
                        $existingOrders->implode(', ') .
                        '. Please use unique order values.'
                ], 422);
            }
        }

        $validated['is_premium'] = $request->plan_type == 'free' ? 0 : 1;
        $validated['user_id'] = Auth::id();
        $validated['duration'] = $request->duration_minutes;
        $validated['image_url'] = uploadImage($request->image, 'images/workouts');

        $workout = Workout::create($validated);

        // attach tags
        if ($request->filled('tags')) {
            $workout->tags()->sync($request->tags);
        }

        // attach exercises
        // if ($request->filled('exercises')) {
        //     foreach ($request->input('exercises', []) as $i => $ex) {
        //         $workout->exercises()->attach(
        //             (int) $ex['exercise_id'],
        //             [
        //                 'sets' => $ex['sets'] ?? null,
        //                 'reps' => $ex['reps'] ?? null,
        //                 'duration_seconds' => $ex['duration_seconds'] ?? null,
        //                 'order' => $ex['order'] ?? ($i + 1)
        //             ]
        //         );
        //     }
        // }

        if ($request->filled('exercises')) {
            $order = 1;
            foreach ($request->input('exercises', []) as $ex) {
                WorkoutExercise::create([
                    'workout_id'        => $workout->id,
                    'exercise_id'       => (int) $ex['exercise_id'],
                    'sets'              => $ex['sets'] ?? null,
                    'reps'              => $ex['reps'] ?? null,
                    'duration_seconds'  => $ex['duration_seconds'] ?? null,
                    'order'             => $ex['order'] ?? $order++,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Workout created successfully', 'data' => $workout]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:workouts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_minutes' => 'required|integer|min:1',
            'plan_type' => 'required|in:free,premium',
            'fitness_goals' => 'required|string',
            'training_location' => 'required|string',
            'health_conditions' => 'required|string',
            'gender_preference' => 'required|string',
            'equipment' => 'required|string',
            'type' => 'required|string',
            'published_at' => 'required|date',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'exercises' => 'array',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10248',
        ]);

        if ($request->filled('exercises')) {
            $orders = collect($request->input('exercises'))->pluck('order')->filter();

            $duplicates = $orders->duplicates();
            if ($duplicates->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Duplicate "order" values found in your exercise list: ' .
                        $duplicates->implode(', ') .
                        '. Each exercise must have a unique order number.'
                ], 422);
            }

            $existingOrders = WorkoutExercise::whereIn('order', $orders)
                ->whereIn('workout_id', Workout::where('user_id', Auth::id())
                    ->where('id', '!=', $request->id)
                    ->pluck('id'))
                ->pluck('order')
                ->unique();

            if ($existingOrders->isNotEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The following order numbers already exist in your other workouts: ' .
                        $existingOrders->implode(', ') .
                        '. Please use unique order values.'
                ], 422);
            }
        }

        $workout = Workout::where('id', $request->id)->where('user_id', Auth::id())->firstOrFail();

        $validated['is_premium'] = $request->plan_type == 'free' ? 0 : 1;
        $validated['image_url'] = $request->image ? uploadImage($request->image, 'images/workouts') : $workout->image_url;
        $workout->update($validated);

        if ($request->filled('tags')) {
            $workout->tags()->sync($request->tags);
        }

        // if ($request->filled('exercises')) {
        //     $syncData = [];
        //     foreach ($request->input('exercises', []) as $i => $ex) {
        //         $syncData[(int)$ex['exercise_id']] = [
        //             'sets' => $ex['sets'] ?? null,
        //             'reps' => $ex['reps'] ?? null,
        //             'duration_seconds' => $ex['duration_seconds'] ?? null,
        //             'order' => $ex['order'] ?? ($i + 1)
        //         ];
        //     }
        //     $workout->exercises()->sync($syncData);
        // }

        if ($request->filled('exercises')) {
            // Delete old records for this workout
            WorkoutExercise::where('workout_id', $workout->id)->delete();

            $order = 1;
            foreach ($request->input('exercises', []) as $ex) {
                WorkoutExercise::create([
                    'workout_id'        => $workout->id,
                    'exercise_id'       => (int) $ex['exercise_id'],
                    'sets'              => $ex['sets'] ?? null,
                    'reps'              => $ex['reps'] ?? null,
                    'duration_seconds'  => $ex['duration_seconds'] ?? null,
                    'order'             => $ex['order'] ?? $order++,
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Workout updated successfully']);
    }

    public function destroy($id)
    {
        $workout = Workout::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $workout->delete();

        return response()->json(['status' => true, 'message' => 'Workout deleted successfully']);
    }
}
