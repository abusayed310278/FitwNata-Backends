@extends('layouts.adminApp')

@section('title', 'Plan Details')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Plan Details: {{ $plan->name }}</h1>
                </div>
                <div>
                    <a href="{{ route('plan.edit', $plan->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('plan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $plan->name }}</p>
                            <p><strong>Price:</strong>
                                @if($plan->price == 0)
                                    <span class="text-success">FREE</span>
                                @else
                                    ${{ number_format($plan->price, 2) }}
                                @endif
                            </p>
                            <p><strong>Billing:</strong> {{ ucfirst($plan->billing_interval) }}ly</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong>
                                <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                            <p><strong>Subscriptions:</strong> {{ $subscriptionsCount }}</p>
                            <p><strong>Active Subscriptions:</strong> {{ $activeSubscriptionsCount }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p>{{ $plan->description }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Features:</strong>
                        @if($plan->features && count($plan->features) > 0)
                            <ul>
                                @foreach($plan->features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No features defined</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
