<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserWorkoutExerciseLog;
use App\Services\Api\WorkoutAssignmentService;

class UserWorkoutController extends Controller
{
    public function __construct(
        private WorkoutAssignmentService $assigner
    ) {}

    /**
     * Return today's workouts for the authenticated user.
     * If not assigned yet, assign N (e.g., 3) and then return.
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $today = now()->toDateString();

        $assignments = $this->assigner->getTodaysAssignments($user, $today, 3);

        // preload logs for mapping statuses
        $assignmentIds = $assignments->pluck('id');
        $logs = UserWorkoutExerciseLog::whereIn('assignment_id', $assignmentIds)->get()->groupBy('assignment_id');

        return response()->json([
            'status' => true,
            'message' => "Today's workouts",
            'todayWorkouts' => $assignments->map(function ($a) use ($logs) {
                $byExercise = ($logs[$a->id] ?? collect())->keyBy('exercise_id');

                return [
                    'assignment_id'   => $a->id,
                    'scheduled_for'   => $a->scheduled_for,
                    'sequence'        => $a->sequence,
                    'status'          => $a->status,               // pending|started|completed|skipped
                    'progress_percent'=> (float)$a->progress_percent,
                    'started_at'      => $a->started_at,
                    'completed_at'    => $a->completed_at,
                    'workout'         => [
                        'id'        => $a->workout->id,
                        'title'     => $a->workout->title,
                        'description' => $a->workout->description,
                        'type'      => $a->workout->type,
                        'level'     => $a->workout->level,
                        'duration'  => $a->workout->duration_minutes ?? $a->workout->duration,
                        'calories_burned' => $a->workout->calories_burned,
                        'equipment' => $a->workout->equipment,
                        'instructions' => $a->workout->instructions,
                        'image_url' => $a->workout->image_url ?? $a->workout->thumbnail_url,
                        'tips'      => $a->workout->tips,
                        'exercises' => $a->workout->exercises->map(function ($ex) use ($byExercise) {
                            $log = $byExercise->get($ex->id);
                            return [
                                'id'               => $ex->id,
                                'name'             => $ex->name,
                                'description'      => $ex->description,
                                'muscle_group'     => $ex->muscle_group,
                                'difficulty'       => $ex->difficulty,
                                'equipment'        => $ex->equipment,
                                'instructions'     => $ex->instructions,
                                'tips'             => $ex->tips,
                                'order'            => $ex->pivot->order,
                                'sets'             => $ex->pivot->sets,
                                'reps'             => $ex->pivot->reps,
                                'duration_seconds' => $ex->pivot->duration_seconds,
                                'equipment_needed' => $ex->equipment_needed,
                                'image_url'        => $ex->image_url,
                                'video_url'        => $ex->video_url,
                                'status'           => $log?->status ?? 'pending',
                                'actual_seconds'   => $log?->actual_seconds,
                                'started_at'       => $log?->started_at,
                                'completed_at'     => $log?->completed_at,
                            ];
                        })->values(),
                    ],
                ];
            })->values(),
        ]);
    }
}