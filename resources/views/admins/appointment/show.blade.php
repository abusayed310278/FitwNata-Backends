@extends('layouts.adminApp')

@section('title', 'Appointment Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Appointment Details</h1>
            <p class="mb-0">View appointment information and details</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('appointment.edit', $appointment) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('appointment.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Appointment Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Appointment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Appointment ID:</strong>
                            <p class="mb-0">#{{ $appointment->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong>
                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Scheduled Date & Time:</strong>
                            <p class="mb-0">{{ $appointment->scheduled_at->format('l, F j, Y') }}</p>
                            <p class="text-muted mb-0">{{ $appointment->scheduled_at->format('g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Duration:</strong>
                            <p class="mb-0">{{ $appointment->duration_minutes }} minutes</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'rescheduled' => 'secondary',
                                        'no_show' => 'dark'
                                    ];
                                    $statusColor = $statusColors[$appointment->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($appointment->status) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Created:</strong>
                            <p class="mb-0">{{ $appointment->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>

                    @if($appointment->notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Notes:</strong>
                            <p class="mb-0">{{ $appointment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-success btn-sm w-100" onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                <i class="fas fa-check me-1"></i>Confirm
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-info btn-sm w-100" onclick="updateStatus({{ $appointment->id }}, 'rescheduled')">
                                <i class="fas fa-calendar-alt me-1"></i>Reschedule
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-primary btn-sm w-100" onclick="updateStatus({{ $appointment->id }}, 'completed')">
                                <i class="fas fa-check-circle me-1"></i>Complete
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-danger btn-sm w-100" onclick="updateStatus({{ $appointment->id }}, 'cancelled')">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-dark btn-sm w-100" onclick="updateStatus({{ $appointment->id }}, 'no_show')">
                                <i class="fas fa-user-times me-1"></i>No Show
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client & Coach Information -->
        <div class="col-lg-4">
            <!-- Client Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Client Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $appointment->user->name }}</h6>
                            <p class="text-muted mb-0">{{ $appointment->user->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <strong>Member since:</strong> {{ $appointment->user->created_at->format('M Y') }}
                    </small>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Professional Information</h6>
                </div>
                <div class="card-body">
                    @if($appointment->coach)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $appointment->coach->name }}</h6>
                                <p class="text-muted mb-0">{{ $appointment->coach->email }}</p>
                                <small class="badge bg-info">Coach</small>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <strong>Joined:</strong> {{ $appointment->coach->created_at->format('M Y') }}
                        </small>
                    @elseif($appointment->nutritionist)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $appointment->nutritionist->name }}</h6>
                                <p class="text-muted mb-0">{{ $appointment->nutritionist->email }}</p>
                                <small class="badge bg-success">Nutritionist</small>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <strong>Joined:</strong> {{ $appointment->nutritionist->created_at->format('M Y') }}
                        </small>
                    @else
                        <p class="text-muted">No professional assigned to this appointment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(appointmentId, status) {
    if (confirm('Are you sure you want to update this appointment status to ' + status + '?')) {
        $.ajax({
            url: `/admin/appointment/${appointmentId}/status`,
            method: 'POST',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Error updating appointment status');
            }
        });
    }
}
</script>
@endpush
