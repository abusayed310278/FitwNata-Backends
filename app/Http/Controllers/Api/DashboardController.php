<?php

namespace App\Http\Controllers\Api;

use App\Models\Workout;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\WorkoutAssignmentService;

class DashboardController extends Controller
{
    public function __construct(
        private WorkoutAssignmentService $assigner
    ) {}

    public function index(Request $request)
    {
        $user  = $request->user();
        $today = now()->toDateString();

        $assignments = $this->assigner->getTodaysAssignments($user, $today, 3);

        $todayWorkouts = $assignments->map(function ($a) {
            $w = $a->workout;
            return [
                'assignment_id'    => $a->id,
                'scheduled_for'    => $a->scheduled_for,
                'sequence'         => $a->sequence,
                'status'           => $a->status,
                'progress_percent' => (float) $a->progress_percent,
                'started_at'       => $a->started_at,
                'completed_at'     => $a->completed_at,
                'workout'          => [
                    'id'              => $w->id,
                    'title'           => $w->title,
                    'description'     => $w->description,
                    'type'            => $w->type,
                    'level'           => $w->level,
                    'duration'        => $w->duration_minutes ?? $w->duration,
                    'calories_burned' => $w->calories_burned,
                    'equipment'       => $w->equipment,
                    'instructions'    => $w->instructions,
                    'image_url'       => $w->image_url ?? $w->thumbnail_url,
                    'tips'            => $w->tips,
                ],
            ];
        })->values();

        $active   = userActiveSubscription($user->id);
        $planFlag = !$active ? 0 : ($active->plan->isFree() ? 0 : 1);
        $profile  = $user->profile;

        $popular = Workout::query()
            ->where('is_premium', $planFlag)
            // ->when(!empty($profile?->training_level), fn($q) => $q->where('level', $profile->training_level))
            // ->when(!empty($profile?->preferred_workout_types), fn($q) => $q->whereIn('type', (array) $profile->preferred_workout_types))
            // ->when(!empty($profile?->equipment_availability), function ($q) use ($profile) {
            //     $eq = (array) $profile->equipment_availability;
            //     $eq[] = 'none';
            //     $q->whereIn('equipment', $eq);
            // })
            // ->when(!empty($profile?->fitness_goals), fn($q) => $q->whereIn('fitness_goals', (array) $profile->fitness_goals))
            // ->when(!empty($profile?->training_location), fn($q) => $q->where('training_location', $profile->training_location))
            // ->when(!empty($profile?->health_conditions), fn($q) => $q->whereIn('health_conditions', (array) $profile->health_conditions))
            // ->when(!empty($profile?->gender), fn($q) => $q->where('gender_preference', $profile->gender))
            ->withCount(['assignments as total_completions' => fn($q) => $q->where('status', 'completed')])
            ->orderByDesc('total_completions')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function ($w) {
                return [
                    'id'              => $w->id,
                    'title'           => $w->title,
                    'description'     => $w->description,
                    'type'            => $w->type,
                    'level'           => $w->level,
                    'duration'        => $w->duration_minutes ?? $w->duration,
                    'calories_burned' => $w->calories_burned,
                    'equipment'       => $w->equipment,
                    'instructions'    => $w->instructions,
                    'image_url'       => $w->image_url ?? $w->thumbnail_url,
                    'tips'            => $w->tips,
                ];
            });

        return response()->json([
            'success'          => true,
            'message'          => "Dashboard data",
            'today_workouts'   => $todayWorkouts,
            'popular_workouts' => $popular,
        ]);
    }
}