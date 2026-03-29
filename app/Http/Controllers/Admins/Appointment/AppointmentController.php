<?php

namespace App\Http\Controllers\Admins\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AppointmentController extends Controller
{
    public function index()
    {
        $totalAppointments = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $todayAppointments = Appointment::whereDate('scheduled_at', today())->count();

        return view('admins.appointment.index', compact('totalAppointments', 'pendingAppointments', 'todayAppointments'));
    }

    public function create()
    {
        $clients = User::role('customer')->get();
        $coaches = User::role('coach')->get();
        $nutritionists = User::role('nutritionist')->get();
        return view('admins.appointment.create', compact('clients', 'coaches', 'nutritionists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'coach_id' => 'nullable|exists:users,id',
            'nutritionist_id' => 'nullable|exists:users,id',
            'appointment_type' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'notes' => 'nullable|string',
        ]);

        // Ensure either coach_id or nutritionist_id is provided, but not both
        if (empty($validated['coach_id']) && empty($validated['nutritionist_id'])) {
            return back()->withErrors(['professional' => 'Please select either a coach or nutritionist.']);
        }

        if (!empty($validated['coach_id']) && !empty($validated['nutritionist_id'])) {
            return back()->withErrors(['professional' => 'Please select either a coach or nutritionist, not both.']);
        }

        Appointment::create($validated);

        return redirect()->route('appointment.index')
            ->with('success', 'Appointment created successfully!');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'coach', 'nutritionist']);
        return view('admins.appointment.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $clients = User::role('customer')->get();
        $coaches = User::role('coach')->get();
        $nutritionists = User::role('nutritionist')->get();
        return view('admins.appointment.edit', compact('appointment', 'clients', 'coaches', 'nutritionists'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_type' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'status' => 'required|in:pending,confirmed,rescheduled,cancelled,completed,no_show',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointment.index')
            ->with('success', 'Appointment updated successfully!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointment.index')
            ->with('success', 'Appointment deleted successfully!');
    }

    public function getAppointments(Request $request)
    {
        if ($request->ajax()) {
            $appointments = Appointment::with(['user', 'coach', 'nutritionist']);

            return DataTables::of($appointments)
                ->addIndexColumn()
                ->editColumn('user_id', function($row) {
                    return '
                        <div>
                            <strong>'.$row->user->name.'</strong><br>
                            <small class="text-muted">'.$row->user->email.'</small>
                        </div>';
                })
                ->editColumn('professional', function($row) {
                    if ($row->coach) {
                        return '
                            <div>
                                <strong>'.$row->coach->name.'</strong><br>
                                <small class="text-muted">Coach</small>
                            </div>';
                    } elseif ($row->nutritionist) {
                        return '
                            <div>
                                <strong>'.$row->nutritionist->name.'</strong><br>
                                <small class="text-muted">Nutritionist</small>
                            </div>';
                    }
                    return '<span class="text-muted">No professional assigned</span>';
                })
                ->editColumn('scheduled_at', function($row) {
                    return '
                        <div>
                            <strong>'.$row->scheduled_at->format('M d, Y').'</strong><br>
                            <small class="text-muted">'.$row->scheduled_at->format('g:i A').'</small>
                        </div>';
                })
                ->editColumn('status', function($row) {
                    $colors = [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'rescheduled' => 'secondary',
                        'no_show' => 'dark'
                    ];
                    $color = $colors[$row->status] ?? 'secondary';
                    return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('duration', function($row) {
                    return $row->duration_minutes . ' min';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('appointment.show', $row->id).'">
                                    <i class="ti-eye"></i> View
                                </a></li>
                                <li><a class="dropdown-item" href="'.route('appointment.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Edit
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button class="dropdown-item text-success" onclick="updateStatus('.$row->id.', \"confirmed\")">
                                    <i class="ti-check"></i> Confirm
                                </button></li>
                                <li><button class="dropdown-item text-danger" onclick="updateStatus('.$row->id.', \"cancelled\")">
                                    <i class="ti-close"></i> Cancel
                                </button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['user_id', 'professional', 'scheduled_at', 'status', 'actions'])
                ->make(true);
        }
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,rescheduled,cancelled,completed,no_show'
        ]);

        $appointment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully!'
        ]);
    }
}
