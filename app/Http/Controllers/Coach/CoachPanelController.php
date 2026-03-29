<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CoachPanelController extends Controller
{
    /**
     * Coach Dashboard
     */
    public function dashboard()
    {
        $coach = auth()->user();

        // Today's sessions
        $todaySessions = Appointment::where('coach_id', $coach->id)
            ->whereDate('scheduled_at', today())
            ->with('client')
            ->orderBy('scheduled_at')
            ->get();

        // Pending feedback requests (appointments that are completed but no feedback given)
        $feedbackRequests = Appointment::where('coach_id', $coach->id)
            ->where('status', 'completed')
            // ->whereNull('coach_feedback')
            ->with('client')
            ->latest()
            ->take(5)
            ->get();


        // Recent user progress updates (clients who updated their progress journal)
        $progressUpdates = collect();
        $clientIds = Appointment::where('coach_id', $coach->id)
            ->distinct()
            ->pluck('user_id');

        if ($clientIds->isNotEmpty()) {
            $progressUpdates = \App\Models\ProgressJournal::whereIn('user_id', $clientIds)
                ->with('user')
                ->latest()
                ->take(5)
                ->get();
        }

        // Dashboard statistics
        $stats = [
            'total_clients' => Appointment::where('coach_id', $coach->id)
                ->distinct('user_id')
                ->count(),
            'appointments_today' => $todaySessions->count(),
            'pending_appointments' => Appointment::where('coach_id', $coach->id)
                ->where('status', 'pending')
                ->count(),
            'completed_this_month' => Appointment::where('coach_id', $coach->id)
                ->where('status', 'completed')
                ->whereMonth('scheduled_at', now()->month)
                ->count(),
            'total_content' => Article::where('author_id', $coach->id)->count(),
            'published_content' => Article::where('author_id', $coach->id)
                ->whereNotNull('published_at')
                ->count(),
        ];

        // Upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::where('coach_id', $coach->id)
            ->whereBetween('scheduled_at', [now(), now()->addDays(7)])
            ->where('status', 'approved')
            ->with('client')
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();

        // Recent notifications/alerts
        $alerts = [];

        // New session bookings (last 24 hours)
        $newBookings = Appointment::where('coach_id', $coach->id)
            ->where('created_at', '>', now()->subDay())
            ->count();
        if ($newBookings > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-calendar-plus',
                'message' => "$newBookings new session booking(s) in the last 24 hours",
                'time' => 'Recent'
            ];
        }

        // Missed sessions
        $missedSessions = Appointment::where('coach_id', $coach->id)
            ->where('scheduled_at', '<', now())
            ->where('status', 'approved')
            ->count();
        if ($missedSessions > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'message' => "$missedSessions session(s) need status update",
                'time' => 'Overdue'
            ];
        }

        return view('coach.dashboard', compact(
            'coach',
            'todaySessions',
            'feedbackRequests',
            'progressUpdates',
            'stats',
            'upcomingAppointments',
            'alerts'
        ));
    }
}
