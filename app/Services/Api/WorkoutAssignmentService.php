<?php

namespace App\Services\Api;

use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Collection;
use App\Models\UserWorkoutAssignment;
use App\Models\UserWorkoutExerciseLog;

class WorkoutAssignmentService
{
    /**
     * Public entry: return today's assignments in stable order.
     * If none exist, assign N and then return.
     */
    public function getTodaysAssignments(User $user, string $date, int $count = 3): Collection
    {
        // 1) Bring forward any unfinished assignments from today or earlier (“sticky”)
        $carry = $user->workoutAssignments()
            ->with(['workout.exercises' => fn($q) => $q->orderBy('workout_exercise.order')])
            ->whereDate('scheduled_for', '<=', $date)
            ->incomplete()
            ->orderBy('scheduled_for')     // older first
            ->orderBy('sequence')
            ->get();

        // 2) If we already have enough for today, return them
        if ($carry->count() >= $count) {
            return $carry->take($count)->values();
        }

        // 3) Top-up with fresh assignments for $date
        $needed = $count - $carry->count();
        $created = $this->assignFor($user, $date, $needed); // seeds logs too

        // Merge and return in order
        return $carry->concat($created)->values();
    }

    public function assignFor(User $user, string $date, int $count = 3): Collection
    {
        if ($count <= 0) return collect();

        $eligible = $this->buildEligibleWorkouts($user, $date);
        $ordered  = $this->orderWorkouts($eligible)->take($count);

        $createdIds = [];

        foreach ($ordered as $i => $workout) {
            $assignment = UserWorkoutAssignment::firstOrCreate(
                [
                    'user_id'       => $user->id,
                    'workout_id'    => $workout->id,
                    'scheduled_for' => $date,
                ],
                [
                    'sequence' => $i + 1,
                    'status'   => 'pending',
                    'source'   => 'auto',
                ]
            );

            $createdIds[] = $assignment->id;

            // seed per-exercise logs once
            if ($assignment->wasRecentlyCreated) {
                $workout->load(['exercises' => fn($q) => $q->orderBy('workout_exercise.order')]);
                foreach ($workout->exercises as $ex) {
                    UserWorkoutExerciseLog::firstOrCreate(
                        ['assignment_id'=>$assignment->id,'exercise_id'=>$ex->id],
                        [
                            'planned_seconds' => $ex->pivot->duration_seconds,
                            'planned_reps'    => $ex->pivot->reps,
                            'status'          => 'pending',
                        ]
                    );
                }
            }
        }

        return UserWorkoutAssignment::with(['workout.exercises' => fn($q) => $q->orderBy('workout_exercise.order')])
            ->whereIn('id', $createdIds)
            ->orderBy('sequence')
            ->get();
    }

    // protected function buildEligibleWorkouts(User $user, string $date): Collection
    // {
    //     $active = userActiveSubscription($user->id);
    //     $plan   = !$active ? 0 : ($active->plan->isFree() ? 0 : 1);
    //     $profile = $user->profile;

    //     $q = Workout::query()
    //         ->where('is_premium', $plan)
    //         ->where(function ($q) use ($date) {
    //             $q->whereNull('published_at')->orWhereDate('published_at','<=',$date);
    //         });

    //     if (!empty($profile?->training_level))          $q->where('level',$profile->training_level);
    //     if (!empty($profile?->preferred_workout_types)) $q->whereIn('type',(array)$profile->preferred_workout_types);
    //     if (!empty($profile?->equipment_availability)) {
    //         $equipment = (array)$profile->equipment_availability;
    //         $equipment[] = 'none';
    //         $q->whereIn('equipment',$equipment);
    //     }
    //     if (!empty($profile?->fitness_goals))           $q->whereIn('fitness_goals',(array)$profile->fitness_goals);
    //     if (!empty($profile?->training_location))       $q->where('training_location',$profile->training_location);
    //     if (!empty($profile?->health_conditions))       $q->whereIn('health_conditions',(array)$profile->health_conditions);
    //     if (!empty($profile?->gender))                  $q->where('gender_preference',$profile->gender);

    //     return $q->with(['exercises' => fn($e) => $e->orderBy('workout_exercise.order')])->get();
    // }

    protected function buildEligibleWorkouts(User $user, string $date): Collection
    {
        $active = userActiveSubscription($user->id);
        // If the user has an active subscription, check if it's a free plan
        $plan = !$active ? 0 : ($active->plan->isFree() ? 0 : 1);
        
        // Premium users can see both free (0) and paid (1) workouts
        if ($plan === 1) {
            $plan = [0, 1]; // This will include both free and paid workouts for premium users
        }

        $profile = $user->profile;

        $q = Workout::query()
            ->whereIn('is_premium', (array) $plan) // Using whereIn to allow both free and paid workouts
            ->where(function ($q) use ($date) {
                $q->whereNull('published_at')->orWhereDate('published_at','<=',$date);
            });

        // if (!empty($profile?->training_level))          $q->where('level', $profile->training_level);
        // if (!empty($profile?->preferred_workout_types)) $q->whereIn('type', (array)$profile->preferred_workout_types);
        // if (!empty($profile?->equipment_availability)) {
        //     $equipment = (array)$profile->equipment_availability;
        //     $equipment[] = 'none';
        //     $q->whereIn('equipment', $equipment);
        // }
        // if (!empty($profile?->fitness_goals))           $q->whereIn('fitness_goals', (array)$profile->fitness_goals);
        // if (!empty($profile?->training_location))       $q->where('training_location', $profile->training_location);
        // if (!empty($profile?->health_conditions))       $q->whereIn('health_conditions', (array)$profile->health_conditions);
        // if (!empty($profile?->gender))                  $q->where('gender_preference', $profile->gender);

        return $q->with(['exercises' => fn($e) => $e->orderBy('workout_exercise.order')])->get();
    }

    /**
     * Deterministic ordering: warm-up → core → strength → cardio; tie by shorter duration.
     */
    protected function orderWorkouts(Collection $workouts): Collection
    {
        $weights = [
            'flexibility' => 10, 'yoga' => 15,
            'abs' => 40, 'toning' => 45,
            'strength' => 60,
            'cardio' => 80,
        ];

        return $workouts->sortBy(function ($w) use ($weights) {
            $rank = $weights[$w->type] ?? 99;
            $mins = (int) ($w->duration_minutes ?? $w->duration ?? 0);
            return [$rank, $mins];
        })->values();
    }
}