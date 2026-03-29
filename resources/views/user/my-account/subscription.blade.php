@extends('layouts.adminApp')

@section('title', 'Subscription Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Subscription Management</h1>
            <p class="mb-0">Choose and manage your subscription plan</p>
        </div>
        <a href="{{ route('my-account.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to My Account
        </a>
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

    <!-- Current Subscription Status -->
    @if($currentSubscription && $currentPlan)
        <div class="alert alert-info" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Current Plan:</strong> {{ $currentPlan->name }}
                    (${{ number_format($currentPlan->price, 2) }}/{{ $currentPlan->interval }})
                </div>
                <div>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Plans -->
    <div class="row">
        @foreach($plans as $plan)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card h-100 {{ $currentPlan && $currentPlan->id === $plan->id ? 'border-primary' : '' }}">
                    <div class="card-header text-center {{ $currentPlan && $currentPlan->id === $plan->id ? 'bg-primary text-white' : '' }}">
                        <h4 class="my-0 font-weight-normal">{{ $plan->name }}</h4>
                        @if($plan->is_popular)
                            <span class="badge bg-warning text-dark">Most Popular</span>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-3">
                            <h1 class="card-title pricing-card-title">
                                @if($plan->price == 0)
                                    Free
                                @else
                                    ${{ number_format($plan->price, 0) }}
                                    <small class="text-muted">/ {{ $plan->interval }}</small>
                                @endif
                            </h1>
                        </div>

                        @if($plan->description)
                            <p class="text-muted">{{ $plan->description }}</p>
                        @endif

                        <ul class="list-unstyled mt-3 mb-4 flex-grow-1">
                            @if($plan->features && is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                @endforeach
                            @else
                                <li><i class="fas fa-check text-success me-2"></i>Access to workouts</li>
                                <li><i class="fas fa-check text-success me-2"></i>Basic meal plans</li>
                                <li><i class="fas fa-check text-success me-2"></i>Progress tracking</li>
                                @if($plan->isPremium())
                                    <li><i class="fas fa-check text-success me-2"></i>Premium workouts</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Advanced meal plans</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Coach consultations</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Priority support</li>
                                @endif
                            @endif
                        </ul>

                        <div class="mt-auto">
                            @if($currentPlan && $currentPlan->id === $plan->id)
                                <button class="btn btn-primary btn-lg btn-block w-100" disabled>
                                    Current Plan
                                </button>
                            @else
                                @if($currentSubscription)
                                    <button type="button" class="btn btn-success btn-lg w-100"
                                            data-bs-toggle="modal" data-bs-target="#changePlanModal"
                                            data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}"
                                            data-plan-price="{{ $plan->formatted_price }}">
                                        Switch to {{ $plan->name }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary btn-lg w-100"
                                            data-bs-toggle="modal" data-bs-target="#subscribeModal"
                                            data-plan-id="{{ $plan->id }}" data-plan-name="{{ $plan->name }}"
                                            data-plan-price="{{ $plan->formatted_price }}" data-plan-is-free="{{ $plan->isFree() }}">
                                        Get Started
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Subscription Actions -->
    @if($currentSubscription)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Subscription Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i>Cancel Subscription
                                </button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ route('my-account.billing') }}" class="btn btn-info w-100">
                                    <i class="fas fa-file-invoice me-2"></i>Billing History
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button type="button" class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#updatePaymentModal">
                                    <i class="fas fa-credit-card me-2"></i>Update Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Subscribe Modal -->
<div class="modal fade" id="subscribeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subscribe to <span id="subscribe-plan-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('my-account.subscribe') }}" method="POST" id="subscribeForm">
                @csrf
                <input type="hidden" name="plan_id" id="subscribe-plan-id">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h4>Price: <span id="subscribe-plan-price"></span></h4>
                    </div>

                    <div id="payment-section">
                        <div class="form-group mb-3">
                            <label for="card-element">Credit or debit card</label>
                            <div id="card-element" class="form-control" style="height: 40px; padding: 10px;">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>
                    </div>

                    <div id="free-plan-notice" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle me-2"></i>
                            This is a free plan. No payment information required.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="subscribe-button">Subscribe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Plan Modal -->
<div class="modal fade" id="changePlanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change to <span id="change-plan-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('my-account.change-plan') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id" id="change-plan-id">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h4>New Price: <span id="change-plan-price"></span></h4>
                    </div>
                    <p>Are you sure you want to change your subscription plan? This will take effect immediately and your billing will be prorated.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Change Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
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

<!-- Update Payment Modal -->
<div class="modal fade" id="updatePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('my-account.update-payment') }}" method="POST" id="updatePaymentForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="card-element-update">New Credit or debit card</label>
                        <div id="card-element-update" class="form-control" style="height: 40px; padding: 10px;">
                            <!-- Stripe Elements will create form elements here -->
                        </div>
                        <div id="card-errors-update" role="alert" class="text-danger mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="update-payment-button">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe
    const stripe = Stripe('{{ config("cashier.key") }}');
    const elements = stripe.elements();

    // Create card elements
    const cardElement = elements.create('card');
    const cardElementUpdate = elements.create('card');

    cardElement.mount('#card-element');
    cardElementUpdate.mount('#card-element-update');

    // Handle real-time validation errors from the card Element
    cardElement.on('change', ({error}) => {
        const displayError = document.getElementById('card-errors');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });

    cardElementUpdate.on('change', ({error}) => {
        const displayError = document.getElementById('card-errors-update');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Subscribe modal handlers
    document.querySelectorAll('[data-bs-target="#subscribeModal"]').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('subscribe-plan-id').value = this.dataset.planId;
            document.getElementById('subscribe-plan-name').textContent = this.dataset.planName;
            document.getElementById('subscribe-plan-price').textContent = this.dataset.planPrice;

            const isFree = this.dataset.planIsFree === 'true' || this.dataset.planIsFree === '1';
            if (isFree) {
                document.getElementById('payment-section').style.display = 'none';
                document.getElementById('free-plan-notice').style.display = 'block';
            } else {
                document.getElementById('payment-section').style.display = 'block';
                document.getElementById('free-plan-notice').style.display = 'none';
            }
        });
    });

    // Change plan modal handlers
    document.querySelectorAll('[data-bs-target="#changePlanModal"]').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('change-plan-id').value = this.dataset.planId;
            document.getElementById('change-plan-name').textContent = this.dataset.planName;
            document.getElementById('change-plan-price').textContent = this.dataset.planPrice;
        });
    });

    // Handle subscription form submission
    document.getElementById('subscribeForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const button = document.getElementById('subscribe-button');
        button.disabled = true;
        button.textContent = 'Processing...';

        const planId = document.getElementById('subscribe-plan-id').value;
        const isFree = document.querySelector(`[data-plan-id="${planId}"]`).dataset.planIsFree === 'true';

        if (isFree) {
            // Submit form directly for free plan
            this.submit();
            return;
        }

        // Create payment method for paid plans
        const {error, paymentMethod} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            button.disabled = false;
            button.textContent = 'Subscribe';
        } else {
            // Add payment method to form and submit
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'payment_method';
            hiddenInput.value = paymentMethod.id;
            this.appendChild(hiddenInput);
            this.submit();
        }
    });

    // Handle update payment form submission
    document.getElementById('updatePaymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const button = document.getElementById('update-payment-button');
        button.disabled = true;
        button.textContent = 'Updating...';

        const {error, paymentMethod} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElementUpdate,
        });

        if (error) {
            document.getElementById('card-errors-update').textContent = error.message;
            button.disabled = false;
            button.textContent = 'Update Payment';
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'payment_method';
            hiddenInput.value = paymentMethod.id;
            this.appendChild(hiddenInput);
            this.submit();
        }
    });
});
</script>
@endpush
