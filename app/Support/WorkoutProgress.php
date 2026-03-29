<?php

namespace App\Support;

use App\Models\UserWorkoutAssignment;

class WorkoutProgress
{
    public static function recompute(UserWorkoutAssignment $a): void
    {
        $a->loadMissing(['exerciseLogs','workout.exercises' => fn($q)=>$q->orderBy('workout_exercise.order')]);

        $logs = $a->exerciseLogs;
        if ($logs->isEmpty()) { $a->progress_percent = 0; $a->save(); return; }

        $totalPlanned = 0; $totalDone = 0;

        foreach ($logs as $log) {
            $planned = $log->planned_seconds ?: 60;
            $totalPlanned += $planned;

            if ($log->status === 'completed') {
                $totalDone += $log->actual_seconds ?: $planned;
            }
        }

        $a->progress_percent = $totalPlanned > 0
            ? round(($totalDone / $totalPlanned) * 100, 1)
            : round(($logs->where('status','completed')->count() / max(1,$logs->count())) * 100, 1);

        $allResolved = $logs->every(fn($l) => in_array($l->status,['completed','skipped']));
        if ($allResolved) {
            $a->status = 'completed';
            $a->completed_at = $a->completed_at ?? now();
        }

        $a->save();
    }
}