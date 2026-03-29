@extends('layouts.adminApp')

@section('title', 'Create New Nutritionist')

@push('styles')
<style>
    .section-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .page-header {
        margin-bottom: 2rem;
    }
    .profile-photo-container {
        position: relative;
        display: inline-block;
    }
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e9ecef;
        cursor: pointer;
    }
    .remove-photo-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }
    .btn-outline-secondary {
        border-color: #e1e1e1;
    }
    .availability-day {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    .time-slot-row {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }
    .blocked-time-item {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Create New Nutritionist</h3>

        </div>
    </div>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left Column: Profile Photo --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Profile Photo</h5>

                    {{-- Profile Photo Uploader --}}
                    <div class="text-center mb-4">
                        <div class="profile-photo-container">
                            <img src="{{ asset('assets/images/default.png') }}" alt="profile"
                                class="profile-photo" id="photoPreview" onclick="document.getElementById('photoUpload').click();" />
                            <div class="remove-photo-btn" title="Remove photo" style="display: none;">
                                <i class="ti-close"></i>
                            </div>
                        </div>
                        <input type="file" id="photoUpload" name="profile_photo_url" class="d-none" form="nutritionistForm" accept="image/*">
                        <p class="text-muted mt-2 mb-0">Click to upload photo</p>
                        <small class="text-muted">JPG, PNG max 2MB</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Nutritionist Information --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('nutritionist.store') }}" method="POST" enctype="multipart/form-data" id="nutritionistForm">
                        @csrf

                        {{-- Personal Information Section --}}
                        <h5 class="section-title">Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror"
                                       id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                                @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Account Security Section --}}
                        <h5 class="section-title">Account Security</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        {{-- Contact Information Section --}}
                        <h5 class="section-title">Contact Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp Number</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                       id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                                       placeholder="+1234567890">
                                @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Specialties & Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio" name="bio" rows="4"
                                      placeholder="Tell us about the nutritionist's specialties and experience...">{{ old('bio') }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Maximum 500 characters</small>
                        </div>

                        {{-- Availability Settings Section --}}
                        <h5 class="section-title">Availability Settings</h5>
                        <div class="alert alert-info">
                            <i class="ti-info-alt"></i> Set the nutritionist's regular availability schedule. This can be modified later by the nutritionist.
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Weekly Availability</h6>
                            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                            <div class="availability-day mb-3 p-3 rounded">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                        id="day_{{ $index }}"
                                        name="availability[{{ $day }}][0][enabled]"
                                        value="1">
                                    <label class="form-check-label fw-bold" for="day_{{ $index }}">
                                        {{ $day }}
                                    </label>
                                </div>
                                <div class="time-slots ms-4" id="slots_{{ $index }}" style="display: none;">
                                    <div class="time-slot-row">
                                        <div class="row align-items-center">
                                            <div class="col-md-5">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control"
                                                    name="availability[{{ $day }}][0][start_time]"
                                                    value="09:00">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">End Time</label>
                                                <input type="time" class="form-control"
                                                    name="availability[{{ $day }}][0][end_time]"
                                                    value="17:00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Blocked Times (Optional)</h6>
                            <div class="alert alert-warning">
                                <i class="ti-info-alt"></i> Block specific dates when the nutritionist is unavailable.
                            </div>
                            <div id="blocked-times-container">
                                <div class="blocked-time-item mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control"
                                                   name="blocked_times[0][date]">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Start Time</label>
                                            <input type="time" class="form-control"
                                                   name="blocked_times[0][start_time]">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">End Time</label>
                                            <input type="time" class="form-control"
                                                   name="blocked_times[0][end_time]">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Reason</label>
                                            <input type="text" class="form-control"
                                                   name="blocked_times[0][reason]"
                                                   placeholder="e.g., Vacation">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-blocked-time">
                                <i class="ti-plus"></i> Add Another Blocked Time
                            </button>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('nutritionist.index') }}" class="btn btn-secondary">
                                <i class="ti-arrow-left"></i> Back to Nutritionists
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-check"></i> Create Nutritionist
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Photo preview functionality
    document.getElementById('photoUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
                document.querySelector('.remove-photo-btn').style.display = 'flex';
            }
            reader.readAsDataURL(file);
        }
    });

    // Remove photo functionality
    document.querySelector('.remove-photo-btn').addEventListener('click', function() {
        document.getElementById('photoPreview').src = '{{ asset('assets/images/faces/face28.jpg') }}';
        document.getElementById('photoUpload').value = '';
        this.style.display = 'none';
    });

    // Auto-generate display name from first and last name
    function updateDisplayName() {
        const firstName = document.getElementById('name').value;
        const lastName = document.getElementById('last_name').value;
        const displayNameField = document.getElementById('display_name');

        if (firstName && lastName && !displayNameField.value) {
            displayNameField.value = firstName + ' ' + lastName;
        }
    }

    document.getElementById('name').addEventListener('blur', updateDisplayName);
    document.getElementById('last_name').addEventListener('blur', updateDisplayName);

    // Toggle time slots visibility when day is checked
    // document.querySelectorAll('.form-check-input').forEach(checkbox => {
    //     checkbox.addEventListener('change', function() {
    //         const dayIndex = this.id.replace('day_', '');
    //         const timeSlots = document.getElementById('slots_' + dayIndex);
    //         timeSlots.style.display = this.checked ? 'block' : 'none';

    //         // Enable or disable time inputs
    //         // timeSlots.querySelectorAll('input').forEach(input => {
    //         //     input.disabled = !this.checked;
    //         // });
    //     });
    // });

    // Add blocked time functionality
    document.getElementById('add-blocked-time').addEventListener('click', function() {
        const container = document.getElementById('blocked-times-container');
        const currentIndex = container.children.length;

        const newBlockedTime = document.createElement('div');
        newBlockedTime.className = 'blocked-time-item mb-3';
        newBlockedTime.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control"
                           name="blocked_times[${currentIndex}][date]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Time</label>
                    <input type="time" class="form-control"
                           name="blocked_times[${currentIndex}][start_time]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Time</label>
                    <input type="time" class="form-control"
                           name="blocked_times[${currentIndex}][end_time]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Reason</label>
                    <input type="text" class="form-control"
                           name="blocked_times[${currentIndex}][reason]"
                           placeholder="e.g., Vacation">
                </div>
            </div>
        `;

        container.appendChild(newBlockedTime);
    });

    // Ensure all checked days send their time inputs before form submit
    // document.getElementById('nutritionistForm').addEventListener('submit', function() {
    //     document.querySelectorAll('.form-check-input').forEach(checkbox => {
    //         const dayIndex = checkbox.id.replace('day_', '');
    //         const timeSlots = document.getElementById('slots_' + dayIndex);
    //         if (checkbox.checked) {
    //             timeSlots.querySelectorAll('input').forEach(input => {
    //                 input.disabled = false; // just in case
    //             });
    //         }
    //     });
    // });

    // Toggle time slots visibility when day is checked
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const dayIndex = this.id.replace('day_', '');
            const timeSlots = document.getElementById('slots_' + dayIndex);
            timeSlots.style.display = this.checked ? 'block' : 'none';
        });
    });

    // Ensure all checked days send their time inputs before form submit
    document.getElementById('nutritionistForm').addEventListener('submit', function() {
        document.querySelectorAll('.form-check-input').forEach(checkbox => {
            const dayIndex = checkbox.id.replace('day_', '');
            const timeSlots = document.getElementById('slots_' + dayIndex);
            if (checkbox.checked) {
                timeSlots.querySelectorAll('input').forEach(input => {
                    input.disabled = false; // just in case
                });
            }
        });
    });
</script>
@endpush
