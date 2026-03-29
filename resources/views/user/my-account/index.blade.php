@extends('layouts.adminApp')

@section('title', 'My Account')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">My Account</h1>
            <p class="mb-0">Manage your subscription and account settings</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="text-muted small">
                        Member since {{ $user->created_at->format('M Y') }}
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('my-account.subscription') }}" class="btn btn-outline-primary">
                            <i class="fas fa-credit-card me-2"></i>Manage Subscription
                        </a>
                        <a href="{{ route('my-account.billing') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-file-invoice me-2"></i>Billing History
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-info">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Details -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Current Subscription</h6>
                    @if($isSubscribed)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">No Subscription</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($isSubscribed && $currentPlan)
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary">{{ $currentPlan->name }}</h5>
                                <p class="text-muted mb-2">{{ $currentPlan->description }}</p>
                                <div class="d-flex align-items-center mb-2">
                                    <strong class="text-success me-2">${{ number_format($currentPlan->price, 2) }}</strong>
                                    <span class="text-muted">per {{ $currentPlan->interval }}</span>
                                </div>
                                @if($currentSubscription && isset($currentSubscription['ends_at']) && $currentSubscription['ends_at'])
                                    <div class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Ends on {{ \Carbon\Carbon::parse($currentSubscription['ends_at'])->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>Plan Features:</h6>
                                <ul class="list-unstyled">
                                    @if($currentPlan->features && is_array($currentPlan->features))
                                        @foreach($currentPlan->features as $feature)
                                            <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                        @endforeach
                                    @else
                                        <li><i class="fas fa-check text-success me-2"></i>Full access to workouts</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Nutrition plans & recipes</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Progress tracking</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Coach consultations</li>
                                        @if($currentPlan->isPremium())
                                            <li><i class="fas fa-check text-success me-2"></i>Premium content access</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Advanced analytics</li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('my-account.subscription') }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Manage Subscription
                            </a>
                            @if($currentSubscription && isset($currentSubscription['status']) && $currentSubscription['status'] !== 'canceled')
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i>Cancel Subscription
                                </button>
                            @endif
                            @if($availablePlans->count() > 0)
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#upgradeModal">
                                    <i class="fas fa-arrow-up me-2"></i>Change Plan
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5>No Active Subscription</h5>
                            <p class="text-muted mb-4">Choose a plan to get started with premium features</p>
                            <a href="{{ route('my-account.subscription') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Choose a Plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        <i class="fas fa-history fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No recent activity</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Subscription Modal -->
@if($isSubscribed)
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('my-account.cancel') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to cancel your subscription?</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="cancel_immediately" id="cancelImmediately">
                        <label class="form-check-label" for="cancelImmediately">
                            Cancel immediately (you will lose access right away)
                        </label>
                    </div>
                    <small class="text-muted">
                        If not checked, your subscription will remain active until the end of the current billing period.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Subscription</button>
                    <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upgrade Plan Modal -->
@if($availablePlans->count() > 0)
<div class="modal fade" id="upgradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('my-account.change-plan') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @foreach($availablePlans as $plan)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="plan_id"
                                                   id="plan_{{ $plan->id }}" value="{{ $plan->id }}" required>
                                            <label class="form-check-label w-100" for="plan_{{ $plan->id }}">
                                                <h6>{{ $plan->name }}</h6>
                                                <p class="text-muted small mb-2">{{ $plan->description }}</p>
                                                <div class="text-primary font-weight-bold">
                                                    ${{ number_format($plan->price, 2) }}/{{ $plan->interval }}
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Change Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endif

@endsection
