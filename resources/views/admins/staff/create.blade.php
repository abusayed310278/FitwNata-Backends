@extends('layouts.adminApp')

@section('title', 'Create New User')

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
</style>
@endpush

@section('content')
<div class="content-wrapper">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Create New User</h3>

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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <h5 class="section-title">User Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name" value="{{ old('last_name') }}">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label">Display Name</label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror"
                                       id="display_name" name="display_name" value="{{ old('display_name') }}">
                                @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}"
                                                @if(old('role') == $role->name) selected @endif>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" @if(old('status') == 'active') selected @endif>
                                        Active
                                    </option>
                                    <option value="suspended" @if(old('status') == 'suspended') selected @endif>
                                        Suspended
                                    </option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp" class="form-label">WhatsApp Number</label>
                            <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                   id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                                   placeholder="+1234567890">
                            @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Profile Photo Section -->
                        <div class="mb-3">
                            <label class="form-label">Profile Photo</label>
                            <div class="text-center mb-3">
                                <div class="profile-photo-container">
                                    <img src="{{ asset('assets/images/faces/face28.jpg') }}"
                                         alt="Profile Photo"
                                         class="profile-photo"
                                         id="photoPreview"
                                         onclick="document.getElementById('photoUpload').click();">
                                    <div class="remove-photo-btn" title="Remove photo" style="display: none;"
                                         onclick="removePhoto();">
                                        <i class="ti-close"></i>
                                    </div>
                                </div>
                                <input type="file" id="photoUpload" name="profile_photo_url" class="d-none" accept="image/*">
                                <small class="form-text text-muted">Click on the image to upload a photo. JPG, PNG max 2MB.</small>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                                <i class="ti-arrow-left"></i> Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-check"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">User Roles</h5>
                    <div class="alert alert-info">
                        <i class="ti-info-alt"></i>
                        <strong>Role Information:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Admin:</strong> Full system access</li>
                            <li><strong>Coach:</strong> Access to coaching features</li>
                            <li><strong>Nutritionist:</strong> Access to nutrition features</li>
                            <li><strong>User:</strong> Basic user access</li>
                        </ul>
                    </div>
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
function removePhoto() {
    document.getElementById('photoPreview').src = '{{ asset('assets/images/faces/face28.jpg') }}';
    document.getElementById('photoUpload').value = '';
    document.querySelector('.remove-photo-btn').style.display = 'none';
}
</script>
@endpush
