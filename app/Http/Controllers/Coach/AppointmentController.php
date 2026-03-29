<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display appointments list
     */
    public function index()
    {
        $coach = auth()->user();
        // Statistics for the view
        $stats = [
            'total' => $coach->appointmentsAsCoach()->count(),
            'pending' => $coach->appointmentsAsCoach()->where('status', 'pending')->count(),
            'today' => $coach->appointmentsAsCoach()->whereDate('scheduled_at', today())->count(),
            'upcoming' => $coach->appointmentsAsCoach()
                ->where('scheduled_at', '>', now())
                ->whereIn('status', ['pending', 'approved'])
                ->count(),
        ];

        return view('coach.appointments.index', compact('stats'));
    }

    /**
     * Calendar view for appointments
     */
    public function calendar()
    {
        // $coach = auth()->user();

        // // Get appointments for calendar display
        // $appointments = $coach->appointmentsAsCoach()
        //     ->with('client')
        //     ->get()
        //     ->map(function($appointment) {
        //         return [
        //             'id' => $appointment->id,
        //             'title' => $appointment->client->name . ' - ' . ucfirst($appointment->appointment_type),
        //             'start' => $appointment->scheduled_at->format('Y-m-d H:i:s'),
        //             'end' => $appointment->scheduled_at->addMinutes($appointment->duration_minutes)->format('Y-m-d H:i:s'),
        //             'backgroundColor' => $this->getStatusColor($appointment->status),
        //             'borderColor' => $this->getStatusColor($appointment->status),
        //             'extendedProps' => [
        //                 'status' => $appointment->status,
        //                 'client_email' => $appointment->client->email,
        //                 'duration' => $appointment->duration_minutes,
        //                 'notes' => $appointment->notes,
        //             ]
        //         ];
        //     });

        return view('coach.appointments.calendar');
    }

    public function getEvents(Request $request)
    {
        $appointments = Appointment::where('coach_id', auth()->id())
            ->with('client')
            ->get();

        $events = $appointments->map(function($appointment) {
            return [
                'id'    => $appointment->id,
                'title' => $appointment->client->name . ' - ' . ucfirst($appointment->appointment_type),
                'start' => $appointment->scheduled_at,
                'end'   => Carbon::parse($appointment->scheduled_at)->addMinutes($appointment->duration_minutes),
                'color' => match ($appointment->status) {
                    'pending' => 'orange',
                    'approved' => 'blue',
                    'completed' => 'green',
                    'canceled' => 'red',
                    default => 'gray'
                }
            ];
        });

        return response()->json($events);
    }

    /**
     * Get status color for calendar
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#ffc107',
            'approved' => '#17a2b8',
            'completed' => '#28a745',
            'cancelled' => '#dc3545',
            'rescheduled' => '#6c757d',
            default => '#6c757d'
        };
    }

    /**
     * Approve appointment
     */
    public function approve(Appointment $appointment): JsonResponse
    {
        if ($appointment->coach_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $appointment->update(['status' => 'approved']);


        return response()->json([
            'success' => true,
            'message' => 'Appointment approved successfully'
        ]);
    }

    /**
     * Reschedule appointment
     */
    public function reschedule(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->coach_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'new_datetime' => 'required|date|after:now',
            'reschedule_reason' => 'nullable|string|max:500'
        ]);

        $appointment->update([
            'scheduled_at' => $request->new_datetime,
            'status' => 'rescheduled',
            'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') .
                      "Rescheduled: " . ($request->reschedule_reason ?? 'No reason provided')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully'
        ]);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->coach_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:500'
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') .
                      "cancelled: " . ($request->cancel_reason ?? 'No reason provided')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
    }

    /**
     * Complete appointment
     */
    public function complete(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->coach_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'session_notes' => 'nullable|string|max:1000',
            'client_feedback' => 'nullable|string|max:500'
        ]);

        $appointment->update([
            'status' => 'completed',
            'coach_feedback' => $request->session_notes,
            'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') .
                      "Session completed. Client feedback: " . ($request->client_feedback ?? 'No feedback')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment marked as completed'
        ]);
    }

    /**
     * Get appointments data for DataTables
     */
    public function getAppointments(Request $request)
    {
        if ($request->ajax()) {
            $appointments = Appointment::where('coach_id', auth()->id())
                ->with('client')
                ->select('appointments.*');

            return DataTables::of($appointments)
                ->addIndexColumn()
                ->editColumn('client', function($row) {
                    return '
                        <div>
                            <strong>'.$row->client->name.'</strong><br>
                            <small class="text-muted">'.$row->client->email.'</small>
                        </div>';
                })
                ->filterColumn('client', function($query, $keyword) {
                    $query->whereHas('client', function($q) use ($keyword) {
                        $q->whereRaw("LOWER(name) like ?", ["%".strtolower($keyword)."%"])
                        ->orWhereRaw("LOWER(email) like ?", ["%".strtolower($keyword)."%"]);
                    });
                })
                ->editColumn('appointment_type', function($row) {
                    return '<span class="badge bg-info">'.ucfirst(str_replace('_', ' ', $row->appointment_type)).'</span>';
                })
                ->editColumn('scheduled_at', function($row) {
                    return '
                        <div>
                            <strong>'.Carbon::parse($row->scheduled_at)->format('M d, Y').'</strong><br>
                            <small class="text-muted">'.Carbon::parse($row->scheduled_at)->format('g:i A').'</small>
                        </div>';
                })
                ->addColumn('duration', function($row) {
                    return $row->duration_minutes . ' min';
                })
                ->editColumn('status', function($row) {
                    $colors = [
                        'pending' => 'warning',
                        'approved' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'rescheduled' => 'secondary'
                    ];
                    $color = $colors[$row->status] ?? 'secondary';
                    return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('actions', function($row) {
                    $actions = '<div class="btn-group btn-group-sm">';

                    if ($row->status === 'pending') {
                        $actions .= '<button class="btn btn-success" onclick="approveAppointment('.$row->id.')">
                                        <i class="fas fa-check"></i>
                                    </button>';
                    }

                    if (in_array($row->status, ['pending', 'approved'])) {
                        $actions .= '<button class="btn btn-warning" onclick="rescheduleAppointment('.$row->id.')">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>';

                        $actions .= '<button class="btn btn-danger" onclick="cancelAppointment('.$row->id.')">
                                        <i class="fas fa-times"></i>
                                    </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['client', 'appointment_type', 'scheduled_at', 'status', 'actions'])
                ->make(true);
        }
    }

    // $actions .= '<button class="btn btn-primary" onclick="completeAppointment('.$row->id.')">
    //                                     <i class="fas fa-check-circle"></i>
    //                                 </button>';
}
