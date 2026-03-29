<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Support\WorkoutProgress;
use App\Http\Controllers\Controller;
use App\Models\UserWorkoutAssignment;
use App\Models\UserWorkoutExerciseLog;

class UserWorkoutRunController extends Controller
{
    protected function ownedAssignment(Request $r, $id): UserWorkoutAssignment
    {
        return UserWorkoutAssignment::where('id',$id)
            ->where('user_id',$r->user()->id)
            ->firstOrFail();
    }

    public function startExercise(Request $r)
    {
        $r->validate([
            'assignmentId' => 'required|integer|exists:user_workout_assignments,id',
            'exerciseId'   => 'required|integer|exists:exercises,id',
        ]);

        // dd($r->all());

        $assignmentId = $r->assignmentId;
        $exerciseId   = $r->exerciseId;

        $a = $this->ownedAssignment($r, $assignmentId);

        // auto-start workout if first action
        if ($a->status === 'pending') {
            $a->status = 'started';
            $a->started_at = $a->started_at ?? now();
            $a->save();
        }

        $log = UserWorkoutExerciseLog::where('assignment_id',$a->id)
            ->where('exercise_id',$exerciseId)->firstOrFail();

        if ($log->status === 'pending') {
            $log->status = 'started';
            $log->started_at = $log->started_at ?? now();
            $log->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Exercise started',
        ]);
    }

    public function completeExercise(Request $r)
    {
        $r->validate([
            'assignmentId' => 'required|integer|exists:user_workout_assignments,id',
            'exerciseId'   => 'required|integer|exists:exercises,id',
        ]);

        // dd($r->all());

        $assignmentId = $r->assignmentId;
        $exerciseId   = $r->exerciseId;

        $a = $this->ownedAssignment($r, $assignmentId);

        $log = UserWorkoutExerciseLog::where('assignment_id',$a->id)
            ->where('exercise_id',$exerciseId)->firstOrFail();

        $actualSeconds = $r->integer('actual_seconds'); // optional

        $log->status = 'completed';
        $log->completed_at = now();
        if ($actualSeconds) $log->actual_seconds = $actualSeconds;
        $log->save();

        WorkoutProgress::recompute($a);

        return response()->json(['status'=>true,'message'=>'Exercise completed','progress'=>$a->progress_percent,'assignment_status'=>$a->status]);
    }

    public function skipExercise(Request $r)
    {
        $r->validate([
            'assignmentId' => 'required|integer|exists:user_workout_assignments,id',
            'exerciseId'   => 'required|integer|exists:exercises,id',
        ]);

        // dd($r->all());

        $assignmentId = $r->assignmentId;
        $exerciseId   = $r->exerciseId;

        $a = $this->ownedAssignment($r, $assignmentId);

        $log = UserWorkoutExerciseLog::where('assignment_id',$a->id)
            ->where('exercise_id',$exerciseId)->firstOrFail();

        $log->status = 'skipped';
        $log->completed_at = $log->completed_at ?? now();
        $log->save();

        WorkoutProgress::recompute($a);

        return response()->json(['status'=>true,'message'=>'Exercise skipped','progress'=>$a->progress_percent,'assignment_status'=>$a->status]);
    }
}