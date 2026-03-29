@extends('layouts.adminApp')

@section('title', 'Create Appointment')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create New Appointment</h1>
            <p class="mb-0">Schedule a new appointment for clients</p>
        </div>
        <a href="{{ route('appointment.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('appointment.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                            <option value="">Choose a client...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Professional <span class="text-danger">*</span></label>
                        @error('professional')<div class="text-danger small">{{ $message }}</div>@enderror

                        <div class="mb-2">
                            <label for="coach_id" class="form-label">Coach</label>
                            <select class="form-select @error('coach_id') is-invalid @enderror" id="coach_id" name="coach_id">
                                <option value="">Choose a coach...</option>
                                @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}" {{ old('coach_id') == $coach->id ? 'selected' : '' }}>
                                        {{ $coach->name }} ({{ $coach->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('coach_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-2">
                            <label for="nutritionist_id" class="form-label">Nutritionist</label>
                            <select class="form-select @error('nutritionist_id') is-invalid @enderror" id="nutritionist_id" name="nutritionist_id">
                                <option value="">Choose a nutritionist...</option>
                                @foreach($nutritionists as $nutritionist)
                                    <option value="{{ $nutritionist->id }}" {{ old('nutritionist_id') == $nutritionist->id ? 'selected' : '' }}>
                                        {{ $nutritionist->name }} ({{ $nutritionist->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('nutritionist_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <small class="text-muted">Please select either a coach OR a nutritionist (not both)</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="appointment_type" class="form-label">Appointment Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('appointment_type') is-invalid @enderror" id="appointment_type" name="appointment_type" required>
                            <option value="">Select type...</option>
                            <option value="fitness_consultation" {{ old('appointment_type') == 'fitness_consultation' ? 'selected' : '' }}>Fitness Consultation</option>
                            <option value="nutrition_consultation" {{ old('appointment_type') == 'nutrition_consultation' ? 'selected' : '' }}>Nutrition Consultation</option>
                            <option value="personal_training" {{ old('appointment_type') == 'personal_training' ? 'selected' : '' }}>Personal Training</option>
                            <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Follow-up Session</option>
                            <option value="group_session" {{ old('appointment_type') == 'group_session' ? 'selected' : '' }}>Group Session</option>
                        </select>
                        @error('appointment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                        <select class="form-select @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" required>
                            <option value="">Select duration...</option>
                            <option value="30" {{ old('duration_minutes') == '30' ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('duration_minutes') == '45' ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('duration_minutes') == '60' ? 'selected' : '' }}>60 minutes</option>
                            <option value="90" {{ old('duration_minutes') == '90' ? 'selected' : '' }}>90 minutes</option>
                            <option value="120" {{ old('duration_minutes') == '120' ? 'selected' : '' }}>120 minutes</option>
                        </select>
                        @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="scheduled_at" class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror"
                               id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}"
                               min="{{ date('Y-m-d\TH:i') }}" required>
                        @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                              placeholder="Add any additional notes or instructions for this appointment...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('appointment.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Ensure only one professional is selected
    $('#coach_id').change(function() {
        if ($(this).val()) {
            $('#nutritionist_id').val('').prop('disabled', true);
        } else {
            $('#nutritionist_id').prop('disabled', false);
        }
    });

    $('#nutritionist_id').change(function() {
        if ($(this).val()) {
            $('#coach_id').val('').prop('disabled', true);
        } else {
            $('#coach_id').prop('disabled', false);
        }
    });

    // Form validation
    $('form').submit(function(e) {
        const coachId = $('#coach_id').val();
        const nutritionistId = $('#nutritionist_id').val();

        if (!coachId && !nutritionistId) {
            e.preventDefault();
            alert('Please select either a coach or nutritionist.');
            return false;
        }

        if (coachId && nutritionistId) {
            e.preventDefault();
            alert('Please select either a coach or nutritionist, not both.');
            return false;
        }
    });
});
</script>
@endpush
