<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\WorkoutLog;
use App\Models\Article;
use App\Models\ExerciseLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkoutController extends BaseApiController
{
    /**
     * Get all workouts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Workout::query();

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('duration')) {
            $query->where('duration_minutes', '<=', $request->duration);
        }

        $workouts = $query->paginate(10);

        return $this->paginatedSuccess($workouts, 'Workouts retrieved successfully');
    }


    /**
     * Get recommended workouts.
     */
    public function recommended(Request $request,$date = null)
    {
        $user = $request->user() ?? auth()->user();
        $profile = $user->userProfile;
        $plan = $user->subscriptions->plan->isFree() ? 0 : 1;

        $query = Workout::query()->where('is_premium',$plan)->when($date,function($q)use($date){
            $q->whereDate('published_at', $date);
        })->whereDoesntHave('logs', function ($q) {
            $q->where('user_id', auth()->id())
            ->where('completed_at', null);
        });

        if ($profile) {
            $filters = [
                'training_level'       => 'level',
                'fitness_goals'        => 'fitness_goals',
                'training_location'    => 'training_location',
                'health_conditions'    => 'health_conditions',
                'gender'    => 'gender_preference',
                'equipment_availability'            => 'equipment',
                'preferred_workout_types'                 => 'type',
            ];

            foreach ($filters as $profileField => $workoutField) {
                if (!empty($profile->$profileField)) {
                    $query->where($workoutField, $profile->$profileField);
                }
            }
        }


        $workouts = $query->get();

        $workouts = $query->get();

        if($date)
        {
            return $workouts;
        }


        return $this->success($workouts, 'Recommended workouts retrieved');
    }

    /**
     * Get workouts by level.
     */
    public function byLevel(Request $request, string $level): JsonResponse
    {
        $workouts = Workout::where('level', $level)->paginate(10);
        return $this->paginatedSuccess($workouts, "Workouts for {$level} level retrieved");
    }

    /**
     * Get workout details.
     */
    public function show(Workout $workout): JsonResponse
    {
        $workout->load('exercises');
        return $this->success($workout, 'Workout details retrieved');
    }

    /**
     * Get workout exercises.
     */
    public function exercises(Workout $workout): JsonResponse
    {
        $exercises = $workout->exercises()
            ->withPivot('sets', 'reps', 'duration_seconds', 'order')
            ->orderBy('order')
            ->get();

        return $this->success($exercises, 'Workout exercises retrieved');
    }

    public function markComplete(Request $request,$excercise_id,$workout_id): JsonResponse
    {
        $user = $request->user();

        $w = Workout::where($workout_id)->findOrFail();

        ExerciseLog::create([
            'user_id' => auth()->id(),
            'workout_id' => $workout_id,
            'exercise_id' => $excercise_id,
            'is_completed' => true,
        ]);

        $w = $this->updateWorkoutLog($w);

        // $modelClass = [
        //     'workout' => Workout::class,
        //     'article' => Article::class,
        // ][$type] ?? null;

        // if (!$modelClass) {
        //     return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        // }

        // $item = $modelClass::findOrFail($id);

        // $log = UserLog::create([
        //     'user_id' => $user->id,
        //     'loggable_id' => $item->id,
        //     'loggable_type' => $modelClass,
        //     'completed_at' => now(),
        // ]);

        return response()->json([
            'success' => true,
            'data' => $w,
            'message' => ' marked as completed'
        ]);
    }

    public function updateWorkoutLog($workout)
    {
        $totalExercises = $workout->exercises()->count();
        $completedExercises = ExerciseLog::where('user_id', auth()->id())
            ->where('workout_id', $workout->id)
            ->count();

        $percentage = 0;
        if ($totalExercises > 0) {
            $percentage = round(($completedExercises / $totalExercises) * 100, 2); // 2 decimal
        }

        $w = WorkoutLog::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'workout_id' => $workout->id,
            ],
            [
                'exercises_completed' => $completedExercises,
                'is_completed' => $completedExercises >= $totalExercises,
                'completion_percentage' => $percentage, // 👈 new field
            ]
        );

        return $w;
    }

    // /**
    //  * Mark workout as completed.
    //  */
    // public function markComplete(Request $request, Workout $workout): JsonResponse
    // {
    //     $user = $request->user();

    //     WorkoutLog::create([
    //         'workout_id' => $workout->id,
    //         'completed_at' => now(),
    //         'user_id' => $user->id
    //     ]);

    //     return $this->success([
    //         'workout_id' => $workout->id,
    //         'completed_at' => now(),
    //         'user_id' => $user->id
    //     ], 'Workout marked as completed');
    // }

    public function popularworkouts(Request $request)
    {
        $user = $request->user() ?? auth()->user();
        $profile = $user->userProfile;
        $plan = $user->plan->isFree() ? 0 : 1;

        $query = Workout::query()
        ->where('is_premium', $plan)
        ->when($date, function ($q) use ($date) {
            $q->whereDate('published_at', $date);
        })
        ->whereDoesntHave('logs', function ($q) {
            $q->where('user_id', auth()->id())
            ->whereNotNull('completed_at');
        })
        ->withCount(['logs as total_completions' => function ($q) {
            $q->whereNotNull('completed_at');
        }]);


        if ($profile) {
            $filters = [
                'training_level'       => 'level',
                'fitness_goals'        => 'fitness_goals',
                'training_location'    => 'training_location',
                'health_conditions'    => 'health_conditions',
                'gender'    => 'gender_preference',
                'equipment_availability'            => 'equipment',
                'preferred_workout_types'                 => 'type',
            ];

            foreach ($filters as $profileField => $workoutField) {
                if (!empty($profile->$profileField)) {
                    $query->where($workoutField, $profile->$profileField);
                }
            }
        }


        $workouts = $query->orderByDesc('total_completions')
        ->take(5)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $workouts,
            'message' => 'Popular workouts retrieved'
        ]);

    }

    public function today(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $workouts = $this->recommended(request(),$today);

        return response()->json([
            'success' => true,
            'workouts' => $workouts,
            'message' => "Today's content retrieved successfully"
        ]);
    }

    // public function today(Request $request)
    // {
    //     $user = $request->user();

    //     // Example logic: fetch workouts scheduled for today for the user
    //     $today = now()->toDateString();
    //     $workouts = $user->workouts()
    //         ->whereDate('scheduled_for', $today)
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $workouts,
    //         'message' => 'Today\'s workouts retrieved successfully'
    //     ]);
    // }
    public function myWorkouts(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        // Plans the user belongs to
        $planIds = $user->plans()->pluck('plans.id');

        // Get today's published workouts assigned to user's plans
        $workouts = Workout::whereHas('plans', function($q) use ($planIds) {
                $q->whereIn('plans.id', $planIds);
            })
            ->get();

        // Get today's published articles assigned to user's plans
        $articles = Article::whereHas('plans', function($q) use ($planIds) {
                $q->whereIn('plans.id', $planIds);
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'workouts' => $workouts,
                'articles' => $articles,
            ],
            'message' => "Content retrieved successfully"
        ]);
    }
}
