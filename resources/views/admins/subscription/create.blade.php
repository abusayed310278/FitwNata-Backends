@extends('layouts.adminApp')

@section('title', 'Create Subscription')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create New Subscription</h1>
            <p class="mb-0">Add a new subscription for a user</p>
        </div>
        <a href="{{ route('subscription.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Subscriptions
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Subscription Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Details</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('subscription.store') }}" method="POST" id="subscriptionForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Select User</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Choose a user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="plan_id" class="form-label">Select Plan</label>
                                <select class="form-select" id="plan_id" name="plan_id" required>
                                    <option value="">Choose a plan...</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}"
                                                data-price="{{ $plan->price }}"
                                                data-interval="{{ $plan->interval }}"
                                                {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - ${{ $plan->price }}/{{ $plan->interval }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stripe_id" class="form-label">Stripe Subscription ID</label>
                                <input type="text" class="form-control" id="stripe_id" name="stripe_id"
                                       value="{{ old('stripe_id') }}" placeholder="sub_1234567890" required>
                                <div class="form-text">Enter the Stripe subscription ID from Stripe dashboard</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="trialing" {{ old('status') == 'trialing' ? 'selected' : '' }}>Trialing</option>
                                    <option value="past_due" {{ old('status') == 'past_due' ? 'selected' : '' }}>Past Due</option>
                                    <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                    <option value="unpaid" {{ old('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="trial_ends_at" class="form-label">Trial Ends At (Optional)</label>
                                <input type="datetime-local" class="form-control" id="trial_ends_at"
                                       name="trial_ends_at" value="{{ old('trial_ends_at') }}">
                                <div class="form-text">Leave empty if no trial period</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="ends_at" class="form-label">Ends At (Optional)</label>
                                <input type="datetime-local" class="form-control" id="ends_at"
                                       name="ends_at" value="{{ old('ends_at') }}">
                                <div class="form-text">Leave empty for recurring subscriptions</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Plan Preview -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Plan Preview</h6>
                </div>
                <div class="card-body">
                    <div id="plan-preview" class="text-center py-4" style="display: none;">
                        <h4 id="plan-name" class="text-primary mb-2"></h4>
                        <h2 id="plan-price" class="text-success mb-1"></h2>
                        <small id="plan-interval" class="text-muted"></small>
                        <hr>
                        <div class="text-start">
                            <div class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <span>Full access to workouts</span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <span>Nutrition plans & recipes</span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <span>Progress tracking</span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <span>Coach consultations</span>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <span>Premium content</span>
                            </div>
                        </div>
                    </div>
                    <div id="no-plan-selected" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <p>Select a plan to see preview</p>
                    </div>
                </div>
            </div>

            <!-- Guidelines Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Important Notes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li class="mb-2">Ensure the Stripe subscription ID is valid and active</li>
                        <li class="mb-2">Selected user should not have an existing active subscription</li>
                        <li class="mb-2">Trial period is optional but should be set if applicable</li>
                        <li class="mb-2">End date should only be set for fixed-term subscriptions</li>
                        <li>Always verify payment status in Stripe dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Plan selection handler
    $('#plan_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');

        if (selectedOption.val()) {
            const planName = selectedOption.text().split(' - ')[0];
            const planPrice = selectedOption.data('price');
            const planInterval = selectedOption.data('interval');

            $('#plan-name').text(planName);
            $('#plan-price').text('$' + planPrice);
            $('#plan-interval').text('per ' + planInterval);

            $('#no-plan-selected').hide();
            $('#plan-preview').show();
        } else {
            $('#no-plan-selected').show();
            $('#plan-preview').hide();
        }
    });

    // Form validation
    $('#subscriptionForm').on('submit', function(e) {
        const userId = $('#user_id').val();
        const planId = $('#plan_id').val();
        const stripeId = $('#stripe_id').val();

        if (!userId || !planId || !stripeId) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }

        // Validate Stripe ID format
        if (!stripeId.startsWith('sub_')) {
            e.preventDefault();
            alert('Stripe subscription ID should start with "sub_"');
            return false;
        }
    });

    // Auto-generate subscription end date based on plan interval
    $('#plan_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const interval = selectedOption.data('interval');

        if (interval && $('#ends_at').val() === '') {
            const now = new Date();
            let endDate;

            switch(interval) {
                case 'month':
                    endDate = new Date(now.setMonth(now.getMonth() + 1));
                    break;
                case 'year':
                    endDate = new Date(now.setFullYear(now.getFullYear() + 1));
                    break;
                case 'week':
                    endDate = new Date(now.setDate(now.getDate() + 7));
                    break;
                default:
                    return;
            }

            // Format date for datetime-local input
            const formattedDate = endDate.toISOString().slice(0, 16);
            $('#ends_at').val(formattedDate);
        }
    });
});
</script>
@endpush
