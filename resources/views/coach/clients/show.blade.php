@extends('layouts.adminApp')

@section('title', 'Client Profile')

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
            <h3 class="page-title">Client Profile</h3>
        </div>
        <div>
            <a href="{{ route('coach.clients.index') }}" class="btn btn-secondary">
                <i class="ti-arrow-left"></i> Back to Clients
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

    </div>
</div>
@endsection

@push('scripts')
@include('admins.staff.components.script')
<script>



</script>
@endpush
