<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use App\Models\ProgressJournal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ClientController extends Controller
{
    /**
     * Display clients list
     */
    public function index()
    {
        $coach = auth()->user();

        // Statistics
        $stats = [
            'total_clients' => Appointment::where('coach_id', $coach->id)
                ->distinct('user_id')
                ->count(),
            'active_clients' => Appointment::where('coach_id', $coach->id)
                ->where('scheduled_at', '>', now()->subMonth())
                ->distinct('user_id')
                ->count(),
            'new_this_month' => Appointment::where('coach_id', $coach->id)
                ->whereMonth('created_at', now()->month)
                ->distinct('user_id')
                ->count(),
        ];

        return view('coach.clients.index', compact('stats'));
    }

    /**
     * Show client details
     */
    public function show(User $user)
    {
        $coach = auth()->user();

        // Verify coach has access to this client
        $hasAccess = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this client');
        }

        // Load client data with relationships
        $user->load(['userProfile', 'progressJournals', 'measurements']);
        $staff = $user;

        // Get appointment history with this coach
        $appointments = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->orderBy('scheduled_at', 'desc')
            ->get();

        // Get progress journal entries
        $progressEntries = $user->progressJournals()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get latest measurements
        $latestMeasurements = $user->measurements()
            ->latest()
            ->first();

        // Client statistics
        $clientStats = [
            'total_sessions' => Appointment::where('coach_id', $coach->id)
                ->where('user_id', $user->id)
                ->count(),
            'completed_sessions' => Appointment::where('coach_id', $coach->id)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'next_appointment' => Appointment::where('coach_id', $coach->id)
                ->where('user_id', $user->id)
                ->where('scheduled_at', '>', now())
                ->whereIn('status', ['pending', 'approved'])
                ->orderBy('scheduled_at')
                ->first(),
            'last_progress_update' => $user->progressJournals()->latest()->first(),
        ];

        return view('coach.clients.show', get_defined_vars());
    }

    /**
     * Add note to client
     */
    public function addNote(Request $request, User $user): JsonResponse
    {
        $coach = auth()->user();

        // Verify coach has access to this client
        $hasAccess = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'note' => 'required|string|max:1000',
            'note_type' => 'required|in:performance,feedback,general,goal'
        ]);

        // Create a progress journal entry as coach note
        ProgressJournal::create([
            'user_id' => $user->id,
            'coach_id' => $coach->id,
            'entry_type' => 'coach_note',
            'notes' => $request->note,
            'note_type' => $request->note_type,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully'
        ]);
    }

    /**
     * Get client history
     */
    public function history(User $user)
    {
        $coach = auth()->user();

        // Verify coach has access to this client
        $hasAccess = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this client');
        }

        // Get complete workout/nutrition history
        $workoutHistory = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('appointment_type', 'workout')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $nutritionHistory = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('appointment_type', 'nutrition')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        // Get progress timeline
        $progressTimeline = $user->progressJournals()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('coach.clients.history', compact(
            'user',
            'workoutHistory',
            'nutritionHistory',
            'progressTimeline'
        ));
    }

    /**
     * Schedule follow-up appointment
     */
    public function scheduleFollowup(Request $request, User $user): JsonResponse
    {
        $coach = auth()->user();

        // Verify coach has access to this client
        $hasAccess = Appointment::where('coach_id', $coach->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'appointment_type' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'notes' => 'nullable|string|max:500'
        ]);

        Appointment::create([
            'user_id' => $user->id,
            'coach_id' => $coach->id,
            'appointment_type' => $request->appointment_type,
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up appointment scheduled successfully'
        ]);
    }

    /**
     * Get clients data for DataTables
     */
    public function getClients(Request $request)
    {
        if ($request->ajax()) {
            $coach = auth()->user();
            // Get all clients who have appointments with this coach
            $clientIds = Appointment::where('coach_id', $coach->id)
                ->distinct()
                ->pluck('user_id');

            $clients = User::whereIn('id', $clientIds)
                ->with(['appointments' => function($query) use ($coach) {
                    $query->where('coach_id', $coach->id);
                }]);

            return DataTables::of($clients)
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    $avatarUrl = $row->profile_photo_url ?? "https://i.pravatar.cc/40?u=" . urlencode($row->email);
                    return '
                        <div class="d-flex align-items-center">
                            <img src="'.$avatarUrl.'" alt="Avatar" class="rounded-circle me-3" width="40" height="40">
                            <div>
                                <strong>'.$row->name.'</strong><br>
                                <small class="text-muted">'.$row->email.'</small>
                            </div>
                        </div>';
                })
                ->filterColumn('name', function($query, $keyword) {
                        $query->whereRaw("LOWER(name) like ?", ["%".strtolower($keyword)."%"])
                        ->orWhereRaw("LOWER(email) like ?", ["%".strtolower($keyword)."%"]);
                })
                ->addColumn('total_sessions', function($row) use ($coach) {
                    $total = $row->appointments->count();
                    $completed = $row->appointments->where('status', 'completed')->count();
                    return $total . ' (' . $completed . ' completed)';
                })
                ->addColumn('last_session', function($row) use ($coach) {
                    $lastSession = $row->appointments
                        ->where('status', 'completed')
                        ->sortByDesc('scheduled_at')
                        ->first();

                    if ($lastSession) {
                        return Carbon::parse($lastSession->scheduled_at)->format('M d, Y');
                    }
                    return '<span class="text-muted">No sessions</span>';
                })
                ->addColumn('next_appointment', function($row) use ($coach) {
                    $nextAppointment = $row->appointments
                        ->where('scheduled_at', '>', now())
                        ->whereIn('status', ['pending', 'approved'])
                        ->sortBy('scheduled_at')
                        ->first();

                    if ($nextAppointment) {
                        return Carbon::parse($nextAppointment->scheduled_at)->format('M d, Y g:i A');
                    }
                    return '<span class="text-muted">None scheduled</span>';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="btn-group btn-group-sm">
                            <a href="'.route('coach.clients.show', $row->id).'" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="'.route('coach.clients.history', $row->id).'" class="btn btn-info">
                                <i class="fas fa-history"></i> History
                            </a>
                            <button class="btn btn-success" onclick="scheduleFollowup('.$row->id.')">
                                <i class="fas fa-calendar-plus"></i> Schedule
                            </button>
                        </div>';
                })
                ->rawColumns(['name', 'last_session', 'next_appointment', 'actions'])
                ->make(true);
        }
    }
}
