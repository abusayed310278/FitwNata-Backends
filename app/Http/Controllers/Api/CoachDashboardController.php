<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CoachDashboardController extends Controller
{
    public function index(Request $request)
    {
        $coach = Auth::user();

        // Statistics
        $stats = [
            'total_clients' => Appointment::where('coach_id', $coach->id)
                ->orwhere('nutritionist_id', $coach->id)
                ->distinct('user_id')
                ->count(),
            'active_clients' => Appointment::where('coach_id', $coach->id)
                ->orwhere('nutritionist_id', $coach->id)
                ->where('scheduled_at', '>', now()->subMonth())
                ->distinct('user_id')
                ->count(),
            'new_this_month' => Appointment::where('coach_id', $coach->id)
                ->orwhere('nutritionist_id', $coach->id)
                ->whereMonth('created_at', now()->month)
                ->distinct('user_id')
                ->count(),
            'appointments' => Appointment::where('coach_id', $coach->id)
                ->orwhere('nutritionist_id', $coach->id)
                ->count(),
        ];

        $client_ids = Appointment::where('coach_id', $coach->id)
                ->orwhere('nutritionist_id', $coach->id)
                ->pluck('user_id')->toArray();

        $active_clients = User::with('profile:id,username,gender,profile_image_url')
            ->whereIn('id', $client_ids)
            ->where('is_active', true)
            ->get();

        $today_schedule = Appointment::with('user.profile:id,username,gender,profile_image_url')
            ->where(function ($q) use ($coach) {
                $q->where('coach_id', $coach->id)
                ->orWhere('nutritionist_id', $coach->id);
            })
            ->where('status', 'pending')
            ->whereDate('scheduled_at', today())
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Coach/Nutritionist Dashboard Data',
            'stats' => $stats,
            'active_clients' => $active_clients,
            'today_schedule' => $today_schedule,
        ]);
    }
}
