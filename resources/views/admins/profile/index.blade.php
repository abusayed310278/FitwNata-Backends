@extends('layouts.adminApp')

@section('title', 'Edit Profile')

@push('styles')
<style>
    body {
        background-color: #f8f9fa; /* Light grey background */
    }
    .card {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    .form-control, .form-select {
        border: 1px solid #e1e1e1;
        border-radius: .5rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: none;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #333;
    }
    .profile-photo-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    .profile-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: .75rem;
        background-color: #ffe0e0; /* Placeholder bg */
    }
    .remove-photo-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #fff;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: 1px solid #ddd;
        cursor: pointer;
    }
    .btn-outline-secondary {
        border-color: #e1e1e1;
    }
    .page-header {
        margin-bottom: 2rem;
    }
    .nav-pills .nav-link {
        color: #6c757d;
    }
    .nav-pills .nav-link.active {
        background-color: #fff;
        color: #0d6efd;
        border-radius: .5rem;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Users</h3>

        </div>

    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif


    <div class="row">
        {{-- Left Column: Account Management --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Account Management</h5>

                    {{-- Profile Photo Uploader --}}
                    <div class="text-center mb-4">
                        <div class="profile-photo-container">
                             <img src="{{ $user->profile_photo_url ?? asset('assets/images/faces/face28.jpg')}}" alt="profile"
                                class="profile-photo" id="photoPreview" />
                            <div class="remove-photo-btn" title="Remove photo">
                                <i class="ti-close"></i>
                            </div>
                        </div>
                    </div>

                    {{-- NOTE: For file uploads, ensure your main form tag is: <form method="POST" action="..." enctype="multipart/form-data"> --}}

                    <button type="button" class="btn btn-outline-secondary w-100 mb-4" onclick="document.getElementById('photoUpload').click();">
                        Upload Photo
                    </button>

                    {{-- Change Password Form --}}
                    <form action="{{ route('user.password.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Old Password</label>
                            <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="old_password" name="old_password">
                             @error('old_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                             @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Change Password</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Profile Information --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('user.profile.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                         <input type="file" id="photoUpload" name="profile_photo_url" class="d-none">

                        {{-- Profile Information Section --}}
                        <h5 class="section-title">Profile Information</h5>
                        <div class="row">
                            {{-- <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}">
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div> --}}
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="nickname" class="form-label">Nickname</label>
                                <input type="text" class="form-control @error('nickname') is-invalid @enderror" id="nickname" name="nickname" value="{{ old('nickname', $user->nickname) }}">
                                @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div> --}}
                            {{-- <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                    <option value="subscriber" @if(old('role', $user->role) == 'subscriber') selected @endif>Subscriber</option>
                                    <option value="editor" @if(old('role', $user->role) == 'editor') selected @endif>Editor</option>
                                    <option value="admin" @if(old('role', $user->role) == 'admin') selected @endif>Administrator</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div> --}}
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label">Display Name Publicly as</label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $user->display_name) }}">
                                @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Contact Info Section --}}
                        <h5 class="section-title mt-4">Contact Info</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email (required)</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">WhatsApp</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" placeholder="@username">
                                @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="https://example.com">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>

                        {{-- About the User Section --}}
                        <h5 class="section-title mt-4">About the User</h5>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">Biographical Info</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="5" placeholder="A short bio about the user...">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Simple script for live preview of photo upload
    document.getElementById('photoUpload').onchange = evt => {
        const [file] = evt.target.files
        if (file) {
            document.getElementById('photoPreview').src = URL.createObjectURL(file)
        }
    }
</script>
@endpush
