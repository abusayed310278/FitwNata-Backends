@extends('layouts.adminApp')

@section('title', 'User Profile')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .profile-photo-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid rgba(255, 255, 255, 0.3);
        margin: 0 auto;
    }
    .stat-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .role-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }
    .section-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">User Profile</h3>
        </div>
        <div>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                <i class="ti-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="{{ $staff->profile_photo_url ?? asset('assets/images/faces/face28.jpg') }}"
                     alt="Profile Photo"
                     class="profile-photo-large">
            </div>
            <div class="col-md-9">
                <h2 class="mb-2">{{ $staff->name }} {{ $staff->last_name }}</h2>
                <p class="mb-3">
                    <i class="ti-email me-2"></i> {{ $staff->email }}
                </p>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($staff->roles as $role)
                        <span class="role-badge bg-light text-dark">
                            {{ ucfirst($role->name) }}
                        </span>
                    @endforeach
                </div>

                <div class="d-flex gap-3">
                    <div>
                        <i class="ti-calendar me-1"></i>
                        Member since {{ $staff->created_at->format('M Y') }}
                    </div>
                    <div>
                        <i class="ti-time me-1"></i>
                        Last active {{ $staff->last_active ? $staff->last_active->diffForHumans() : 'Never' }}
                    </div>
                </div>

                @if($staff->email_verified_at)
                    <span class="badge bg-success mt-2">
                        <i class="ti-check me-1"></i> Active Account
                    </span>
                @else
                    <span class="badge bg-warning mt-2">
                        <i class="ti-info me-1"></i> Suspended Account
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats and Details -->
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">User Statistics</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card bg-primary text-white p-3 text-center">
                                <h3 class="mb-1">{{ $staff->orders->count() }}</h3>
                                <p class="mb-0">Orders</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card bg-success text-white p-3 text-center">
                                <h3 class="mb-1">{{ $staff->subscriptions->count() }}</h3>
                                <p class="mb-0">Subscriptions</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card bg-info text-white p-3 text-center">
                                <h3 class="mb-1">{{ $staff->appointmentsAsCoach->count() }}</h3>
                                <p class="mb-0">Appointments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Personal Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">First Name</label>
                                <p class="fw-bold mb-0">{{ $staff->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Last Name</label>
                                <p class="fw-bold mb-0">{{ $staff->last_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Display Name</label>
                                <p class="fw-bold mb-0">{{ $staff->display_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">WhatsApp</label>
                                <p class="fw-bold mb-0">{{ $staff->whatsapp ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted">Biography</label>
                                <p class="fw-bold mb-0">{{ $staff->bio ?? 'No biography provided.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('staff.edit', $staff) }}" class="btn btn-primary">
                            <i class="ti-pencil me-2"></i> Edit User
                        </a>
                        <button type="button" class="btn btn-warning" onclick="resetPassword({{ $staff->id }})">
                            <i class="ti-key me-2"></i> Reset Password
                        </button>
                        <button type="button" class="btn {{ $staff->email_verified_at ? 'btn-danger' : 'btn-success' }}"
                                onclick="toggleStatus({{ $staff->id }})">
                            <i class="ti-power-off me-2"></i>
                            {{ $staff->email_verified_at ? 'Suspend User' : 'Activate User' }}
                        </button>
                        @if ($staff->id !== auth()->id())
                        <button type="button" class="btn btn-outline-danger" onclick="deleteUser({{ $staff->id }})">
                            <i class="ti-trash me-2"></i> Delete User
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">Account Status</h5>
                    <div class="mb-3">
                        <label class="text-muted">Account Status</label>
                        <p class="fw-bold mb-0">
                            @if($staff->email_verified_at)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Suspended</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Email Verified</label>
                        <p class="fw-bold mb-0">
                            @if($staff->email_verified_at)
                                <span class="text-success">Yes</span>
                                ({{ $staff->email_verified_at->format('M d, Y') }})
                            @else
                                <span class="text-danger">No</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Member Since</label>
                        <p class="fw-bold mb-0">
                            {{ $staff->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted">Last Updated</label>
                        <p class="fw-bold mb-0">
                            {{ $staff->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admins.staff.components.script')
<script>



</script>
@endpush
