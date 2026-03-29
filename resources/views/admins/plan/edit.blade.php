@extends('layouts.adminApp')

@section('title', 'Edit Plan')

@push('styles')
<style>
    .form-card {
        transition: transform 0.2s;
    }
    .form-card:hover {
        transform: translateY(-2px);
    }
    .feature-input {
        margin-bottom: 10px;
    }
    .feature-input .btn-remove {
        display: none;
    }
    .feature-input:not(:first-child) .btn-remove {
        display: inline-block;
    }
    .preview-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
    }
    .preview-price {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 15px 0;
    }
    .preview-features {
        text-align: left;
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 15px;
        margin-top: 20px;
    }
    .subscription-warning {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .settings-list {
        display: grid;
        gap: .75rem;
    }
    .settings-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .85rem 1rem;
        border: 1px solid #e9ecef;
        border-radius: .75rem;
        background: #f8f9fa;
    }
    .settings-item .form-switch { margin: 0; padding-right: .25rem; }
    .settings-item .form-check-input { width: 2.75rem; height: 1.5rem; cursor: pointer; }
    .settings-item .form-check-input:focus { box-shadow: 0 0 0 .2rem rgba(13,110,253,.15); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Edit Plan: {{ $plan->name }}</h1>
                    <p class="text-muted">Modify subscription plan details</p>
                </div>
                <div>
                    <a href="{{ route('plan.show', $plan->id) }}" class="btn btn-info me-2">
                        <i class="fas fa-eye me-2"></i>View Plan
                    </a>
                    <a href="{{ route('plan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Plans
                    </a>
                </div>
            </div>

            @if($plan->subscriptions()->where('stripe_status', 'active')->count() > 0)
                <div class="subscription-warning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <strong>Warning:</strong>
                    </div>
                    <p class="mb-0 mt-1">This plan has {{ $plan->subscriptions()->where('stripe_status', 'active')->count() }} active subscriptions. Changes to pricing may affect existing subscribers.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('plan.update', $plan->id) }}" id="planForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information Card -->
                        <div class="card shadow mb-4 form-card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Plan Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                   id="price" name="price" value="{{ old('price', $plan->price) }}" min="0" step="0.01" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="interval" class="form-label">Billing Interval *</label>
                                        <select class="form-select @error('interval') is-invalid @enderror"
                                                id="interval" name="interval" required>
                                            <option value="">Select interval...</option>
                                            <option value="month" {{ old('interval', $plan->interval) == 'month' ? 'selected' : '' }}>Monthly</option>
                                            <option value="year" {{ old('interval', $plan->interval) == 'year' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                        @error('interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="trial_days" class="form-label">Trial Days</label>
                                        <input type="number" class="form-control @error('trial_days') is-invalid @enderror"
                                               id="trial_days" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0">
                                        <small class="form-text text-muted">Leave empty for no trial period</small>
                                        @error('trial_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4" required>{{ old('description', $plan->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stripe_plan_id" class="form-label">Stripe Plan ID</label>
                                    <input type="text" class="form-control @error('stripe_plan_id') is-invalid @enderror"
                                           id="stripe_plan_id" name="stripe_plan_id" value="{{ old('stripe_plan_id', $plan->stripe_plan_id) }}">
                                    <small class="form-text text-muted">Enter the Stripe Price ID for integration</small>
                                    @error('stripe_plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Features Card -->
                        <div class="card shadow mb-4 form-card">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Plan Features</h6>
                                <button type="button" class="btn btn-sm btn-success" id="addFeature">
                                    <i class="fas fa-plus me-1"></i>Add Feature
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="featuresContainer">
                                    @php
                                        $features = old('features', $plan->features ?? []);
                                        $features = is_array($features) ? $features : [];
                                    @endphp

                                    @if(count($features) > 0)
                                        @foreach($features as $index => $feature)
                                            <div class="feature-input">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-check text-success"></i>
                                                    </span>
                                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Enter feature description">
                                                    <button type="button" class="btn btn-outline-danger btn-remove">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="feature-input">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-check text-success"></i>
                                                </span>
                                                <input type="text" class="form-control" name="features[]" placeholder="Enter feature description">
                                                <button type="button" class="btn btn-outline-danger btn-remove">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @error('features')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Settings Card -->
                        <div class="card shadow mb-4 form-card">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Plan Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="settings-list">

                                    <div class="settings-item">
                                        <div class="me-3">
                                            <div class="fw-semibold">Active Plan</div>
                                            <small class="text-muted">Users can subscribe to this plan</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            {{-- Send 0 when unchecked --}}
                                            <input type="hidden" name="is_active" value="0">
                                            {{-- Send 1 when checked --}}
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="is_active"
                                                name="is_active"
                                                value="1"
                                                {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                                            >
                                            <label class="visually-hidden" for="is_active">Active Plan</label>
                                        </div>
                                    </div>

                                    <div class="settings-item">
                                        <div class="me-3">
                                            <div class="fw-semibold">Popular Plan</div>
                                            <small class="text-muted">Mark as recommended plan</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            {{-- Send 0 when unchecked --}}
                                            <input type="hidden" name="is_popular" value="0">
                                            {{-- Send 1 when checked --}}
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="is_popular"
                                                name="is_popular"
                                                value="1"
                                                {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}
                                            >
                                            <label class="visually-hidden" for="is_popular">Popular Plan</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end mb-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Plan
                            </button>
                        </div>
                    </div>

                    <!-- Preview Column -->
                    <div class="col-lg-4">
                        <div class="sticky-top">
                            <!-- Current Plan Stats -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-info">Current Plan Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary mb-0">{{ $plan->subscriptions()->count() }}</h4>
                                                <small class="text-muted">Total Subscriptions</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success mb-0">{{ $plan->subscriptions()->where('stripe_status', 'active')->count() }}</h4>
                                            <small class="text-muted">Active Subscriptions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Preview -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Live Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="preview-card">
                                        <h5 id="previewName">{{ $plan->name }}</h5>
                                        <div class="preview-price" id="previewPrice">${{ number_format($plan->price, 2) }}</div>
                                        <small id="previewInterval">per {{ $plan->interval }}</small>
                                        <div class="preview-features" id="previewFeatures">
                                            <strong>Features:</strong>
                                            <ul id="previewFeaturesList">
                                                @if($plan->features && count($plan->features) > 0)
                                                    @foreach($plan->features as $feature)
                                                        <li>{{ $feature }}</li>
                                                    @endforeach
                                                @else
                                                    <li>Add features to see preview</li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-secondary' }}" id="previewStatus">
                                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span class="badge bg-warning" id="previewPopular" {{ $plan->is_popular ? '' : 'style=display:none;' }}>Popular</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips Card -->
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-warning">⚠️ Important Notes</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="fas fa-info-circle text-info me-2"></i>Price changes affect new subscriptions only</li>
                                        <li class="mb-2"><i class="fas fa-info-circle text-info me-2"></i>Deactivating removes plan from signup options</li>
                                        <li class="mb-2"><i class="fas fa-info-circle text-info me-2"></i>Stripe Plan ID should match your Stripe dashboard</li>
                                        <li><i class="fas fa-info-circle text-info me-2"></i>Feature changes are immediately visible to users</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add feature functionality
    $('#addFeature').click(function() {
        const newFeature = `
            <div class="feature-input">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-check text-success"></i>
                    </span>
                    <input type="text" class="form-control" name="features[]" placeholder="Enter feature description">
                    <button type="button" class="btn btn-outline-danger btn-remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        $('#featuresContainer').append(newFeature);
        updatePreview();
    });

    // Remove feature functionality
    $(document).on('click', '.btn-remove', function() {
        $(this).closest('.feature-input').remove();
        updatePreview();
    });

    // Live preview functionality
    function updatePreview() {
        const name = $('#name').val() || 'Plan Name';
        const price = $('#price').val() || '0.00';
        const interval = $('#interval').val() || 'month';
        const isActive = $('#is_active').is(':checked');
        const isPopular = $('#is_popular').is(':checked');

        $('#previewName').text(name);
        $('#previewPrice').text('$' + parseFloat(price).toFixed(2));
        $('#previewInterval').text('per ' + interval);

        // Update status
        if (isActive) {
            $('#previewStatus').removeClass('bg-secondary').addClass('bg-success').text('Active');
        } else {
            $('#previewStatus').removeClass('bg-success').addClass('bg-secondary').text('Inactive');
        }

        // Update popular badge
        if (isPopular) {
            $('#previewPopular').show();
        } else {
            $('#previewPopular').hide();
        }

        // Update features
        const features = [];
        $('input[name="features[]"]').each(function() {
            if ($(this).val().trim()) {
                features.push($(this).val().trim());
            }
        });

        if (features.length > 0) {
            const featuresList = features.map(feature => `<li>${feature}</li>`).join('');
            $('#previewFeaturesList').html(featuresList);
        } else {
            $('#previewFeaturesList').html('<li>Add features to see preview</li>');
        }
    }

    // Bind events for live preview
    $('#name, #price, #interval').on('input change', updatePreview);
    $('#is_active, #is_popular').on('change', updatePreview);
    $(document).on('input', 'input[name="features[]"]', updatePreview);

    // Initial preview update
    updatePreview();

    // Form validation - removed features requirement
    $('#planForm').on('submit', function(e) {
        // No form validation needed - server-side validation will handle it
        return true;
    });
});
</script>
@endpush
