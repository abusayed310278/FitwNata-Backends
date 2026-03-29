@extends('layouts.adminApp')

@section('title', 'Edit Appointment')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Appointment</h1>
            <p class="mb-0">Update appointment details</p>
        </div>
        <a href="{{ route('appointment.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('appointment.update', $appointment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">Client</label>
                        <input type="text" class="form-control" value="{{ $appointment->user->name }} ({{ $appointment->user->email }})" readonly>
                        <small class="text-muted">Client cannot be changed once appointment is created</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Professional</label>
                        @if($appointment->coach)
                            <input type="text" class="form-control" value="{{ $appointment->coach->name }} (Coach)" readonly>
                        @elseif($appointment->nutritionist)
                            <input type="text" class="form-control" value="{{ $appointment->nutritionist->name }} (Nutritionist)" readonly>
                        @else
                            <input type="text" class="form-control" value="No professional assigned" readonly>
                        @endif
                        <small class="text-muted">Professional cannot be changed once appointment is created</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="appointment_type" class="form-label">Appointment Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('appointment_type') is-invalid @enderror" id="appointment_type" name="appointment_type" required>
                            <option value="">Select type...</option>
                            <option value="fitness_consultation" {{ old('appointment_type', $appointment->appointment_type) == 'fitness_consultation' ? 'selected' : '' }}>Fitness Consultation</option>
                            <option value="nutrition_consultation" {{ old('appointment_type', $appointment->appointment_type) == 'nutrition_consultation' ? 'selected' : '' }}>Nutrition Consultation</option>
                            <option value="personal_training" {{ old('appointment_type', $appointment->appointment_type) == 'personal_training' ? 'selected' : '' }}>Personal Training</option>
                            <option value="follow_up" {{ old('appointment_type', $appointment->appointment_type) == 'follow_up' ? 'selected' : '' }}>Follow-up Session</option>
                            <option value="group_session" {{ old('appointment_type', $appointment->appointment_type) == 'group_session' ? 'selected' : '' }}>Group Session</option>
                        </select>
                        @error('appointment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                        <select class="form-select @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" required>
                            <option value="">Select duration...</option>
                            <option value="30" {{ old('duration_minutes', $appointment->duration_minutes) == '30' ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('duration_minutes', $appointment->duration_minutes) == '45' ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('duration_minutes', $appointment->duration_minutes) == '60' ? 'selected' : '' }}>60 minutes</option>
                            <option value="90" {{ old('duration_minutes', $appointment->duration_minutes) == '90' ? 'selected' : '' }}>90 minutes</option>
                            <option value="120" {{ old('duration_minutes', $appointment->duration_minutes) == '120' ? 'selected' : '' }}>120 minutes</option>
                        </select>
                        @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="scheduled_at" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror"
                               id="scheduled_at" name="scheduled_at"
                               value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                        @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="pending" {{ old('status', $appointment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $appointment->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="rescheduled" {{ old('status', $appointment->status) == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                            <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="no_show" {{ old('status', $appointment->status) == 'no_show' ? 'selected' : '' }}>No Show</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                              placeholder="Add any additional notes or instructions for this appointment...">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('appointment.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
