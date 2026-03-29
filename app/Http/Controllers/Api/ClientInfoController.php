<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserWeightHistory;
use App\Http\Controllers\Controller;
use App\Models\UserWorkoutAssignment;
use App\Models\UserWorkoutExerciseLog;

class ClientInfoController extends Controller
{
    public function index($id)
    {
        $user = User::with('profile')->where('id', $id)->where('is_Active', true)->first();

        if(!$user)
        {
            return response()->json([
                'status' => false,
                'message' => 'Error! No record found for selected user',
            ]);
        }

        $info = [];

        $info['name'] = $user->name;
        $info['email'] = $user->email;
        $info['phone'] = $user->phone;
        $info['avatar'] = $user->profile_photo_url ?? $user->profile->profile_image_url;
        $info['gender'] = $user->profile->gender;

        $dob = $user->profile->date_of_birth ?? null;
        $info['age'] = $dob ? Carbon::parse($dob)->age . ' years' : null;

        $info['member_since'] = $user->created_at
        ? Carbon::parse($user->created_at)->format('M Y')
        : null;

        $info['goals'] = $user->profile->fitness_goals;

        $info['weight'] = $user->profile->weight_kg;
        $info['workout_completion_rate'] = UserWorkoutAssignment::where('user_id', $user->id)->avg('progress_percent');

        $fiveMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        $weightHistory = UserWeightHistory::where('user_id', $user->id)
            ->where('created_at', '>=', $fiveMonthsAgo)
            ->orderBy('created_at', 'asc')
            ->get(['new_value', 'created_at'])
            ->map(function ($entry) {
                return [
                    'month' => Carbon::parse($entry->created_at)->format('M Y'),
                    'weight' => (float) $entry->new_value,
                ];
            })
            ->values();

        $info['weight_last_5_months'] = $weightHistory;

        $days = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $exerciseLogs = UserWorkoutExerciseLog::whereHas('assignment', function ($q) use ($user, $startOfWeek, $endOfWeek) {
                $q->where('user_id', $user->id)
                ->whereBetween('scheduled_for', [$startOfWeek, $endOfWeek]);
            })
            ->get(['status', 'completed_at']);

        $adherenceData = $days->map(function ($dayName) use ($exerciseLogs) {
            $logsForDay = $exerciseLogs->filter(function ($log) use ($dayName) {
                return $log->completed_at && Carbon::parse($log->completed_at)->format('D') === $dayName;
            });

            $total = $exerciseLogs->filter(function ($log) use ($dayName) {
                $date = $log->completed_at ?? $log->created_at;
                return Carbon::parse($date)->format('D') === $dayName;
            })->count();

            $completed = $logsForDay->where('status', 'completed')->count();

            $percentage = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

            return [
                'day' => $dayName,
                'adherence' => $percentage
            ];
        });

        $info['workout_adherence'] = $adherenceData->values();
        

        return response()->json([
            'status' => true,
            'message' => 'Record found successfully',
            'data' => $info,
        ]);
    }
}