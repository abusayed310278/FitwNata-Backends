@extends('layouts.adminApp')

@section('title', 'Coach Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Welcome back, {{ $coach->name }}!</h1>
                    <p class="mb-0 text-muted">Here's what's happening with your coaching sessions today</p>
                </div>
                <div class="text-muted">
                    <i class="fas fa-calendar-day me-2"></i>{{ now()->format('l, F j, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clients</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_clients'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['appointments_today'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Approvals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_appointments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_this_month'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts/Notifications -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-warning shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-bell me-2"></i>Notifications & Alerts
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show mb-2">
                        <i class="{{ $alert['icon'] }} me-2"></i>
                        <strong>{{ $alert['message'] }}</strong>
                        <small class="float-end">{{ $alert['time'] }}</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Today's Sessions -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Sessions</h6>
                    <a href="{{ route('coach.appointments.calendar') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-calendar me-1"></i>View Calendar
                    </a>
                </div>
                <div class="card-body">
                    @if($todaySessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Client</th>
                                        <th>Session Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todaySessions as $session)
                                    <tr>
                                        <td>{{ $session->scheduled_at->format('g:i A') }}</td>
                                        <td>
                                            <strong>{{ $session->client->name }}</strong><br>
                                            <small class="text-muted">{{ $session->client->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_', ' ', $session->appointment_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'info',
                                                    'completed' => 'success',
                                                    'canceled' => 'danger'
                                                ];
                                                $color = $statusColors[$session->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($session->status) }}</span>
                                        </td>
                                        <td>
                                            @if($session->status === 'pending')
                                                <button class="btn btn-sm btn-success" onclick="quickApprove({{ $session->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            @if(in_array($session->status, ['pending', 'approved']))
                                                <button class="btn btn-sm btn-primary" onclick="quickComplete({{ $session->id }})">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">No sessions scheduled for today</h5>
                            <p class="text-muted">Enjoy your free day or check your availability settings!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Progress Updates -->
            @if($progressUpdates->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Client Progress Updates</h6>
                </div>
                <div class="card-body">
                    @foreach($progressUpdates->take(5) as $update)
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $update->user->name }}</h6>
                            <p class="mb-1 text-muted small">{{ Str::limit($update->notes, 100) }}</p>
                            <small class="text-muted">{{ $update->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            <a href="{{ route('coach.clients.show', $update->user_id) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('coach.appointments.index') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Manage Appointments
                        </a>
                        <a href="{{ route('coach.availability.index') }}" class="btn btn-info">
                            <i class="fas fa-clock me-2"></i>Set Availability
                        </a>
                        <a href="{{ route('coach.content.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i>Create Content
                        </a>
                        <a href="{{ route('coach.clients.index') }}" class="btn btn-warning">
                            <i class="fas fa-users me-2"></i>View Clients
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Content Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-3 mb-2">
                                <h4 class="mb-1 text-primary">{{ $stats['total_content'] }}</h4>
                                <small class="text-muted">Total Content</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 mb-2">
                                <h4 class="mb-1 text-success">{{ $stats['published_content'] }}</h4>
                                <small class="text-muted">Published</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('coach.content.index') }}" class="btn btn-sm btn-outline-primary">
                            Manage Content
                        </a>
                    </div>
                </div>
            </div>

            <!-- Feedback Requests -->
            @if($feedbackRequests->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Feedback</h6>
                </div>
                <div class="card-body">
                    @foreach($feedbackRequests->take(3) as $request)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $request->client->name }}</h6>
                            <small class="text-muted">Session: {{ $request->scheduled_at->format('M d, Y') }}</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="addFeedback({{ $request->id }})">
                            Add Feedback
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Upcoming Appointments -->
            @if($upcomingAppointments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Appointments</h6>
                </div>
                <div class="card-body">
                    @foreach($upcomingAppointments->take(5) as $appointment)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $appointment->client->name }}</h6>
                            <small class="text-muted">
                                {{ $appointment->scheduled_at->format('M d, Y g:i A') }}
                                <br>{{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                    <div class="text-center">
                        <a href="{{ route('coach.appointments.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quickApprove(appointmentId) {
    if (confirm('Approve this appointment?')) {
        $.post(`/coach/appointments/${appointmentId}/approve`, {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        })
        .fail(function() {
            alert('Error approving appointment');
        });
    }
}

function quickComplete(appointmentId) {
    const notes = prompt('Add session notes (optional):');
    if (notes !== null) {
        $.post(`/coach/appointments/${appointmentId}/complete`, {
            session_notes: notes,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        })
        .fail(function() {
            alert('Error completing appointment');
        });
    }
}

function addFeedback(appointmentId) {
    const feedback = prompt('Add your feedback for this session:');
    if (feedback !== null && feedback.trim() !== '') {
        $.post(`/coach/appointments/${appointmentId}/complete`, {
            session_notes: feedback,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        })
        .fail(function() {
            alert('Error adding feedback');
        });
    }
}
</script>
@endpush
