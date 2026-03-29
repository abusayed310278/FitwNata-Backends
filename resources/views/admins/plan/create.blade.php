@extends('layouts.adminApp')

@section('title', 'Create New Plan')

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

    /* Make the switch a touch larger and keep it off the edge */
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
                    <h1 class="h3 mb-0 text-gray-800">Create New Plan</h1>
                    <p class="text-muted">Add a new subscription plan to your platform</p>
                </div>
                <div>
                    <a href="{{ route('plan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Plans
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('plan.store') }}" id="planForm">
                @csrf
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
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                   id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
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
                                            <option value="month" {{ old('interval') == 'month' ? 'selected' : '' }}>Monthly</option>
                                            <option value="year" {{ old('interval') == 'year' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                        @error('interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="trial_days" class="form-label">Trial Days</label>
                                        <input type="number" class="form-control @error('trial_days') is-invalid @enderror"
                                               id="trial_days" name="trial_days" value="{{ old('trial_days') }}" min="0">
                                        <small class="form-text text-muted">Leave empty for no trial period</small>
                                        @error('trial_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stripe_plan_id" class="form-label">Stripe Plan ID</label>
                                    <input type="text" class="form-control @error('stripe_plan_id') is-invalid @enderror"
                                           id="stripe_plan_id" name="stripe_plan_id" value="{{ old('stripe_plan_id') }}">
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
                                    @if(old('features'))
                                        @foreach(old('features') as $index => $feature)
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
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                {{ old('is_active', 1) ? 'checked' : '' }}>
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
                                            {{-- Send 1 when checked (instead of default "on") --}}
                                            <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="is_popular"
                                            name="is_popular"
                                            value="1"
                                            {{ old('is_popular') ? 'checked' : '' }}
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
                                <i class="fas fa-save me-2"></i>Create Plan
                            </button>
                        </div>
                    </div>

                    <!-- Preview Column -->
                    <div class="col-lg-4">
                        <div class="sticky-top">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Live Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="preview-card">
                                        <h5 id="previewName">Plan Name</h5>
                                        <div class="preview-price" id="previewPrice">$0.00</div>
                                        <small id="previewInterval">per month</small>
                                        <div class="preview-features" id="previewFeatures">
                                            <strong>Features:</strong>
                                            <ul id="previewFeaturesList">
                                                <li>Add features to see preview</li>
                                            </ul>
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge bg-light text-dark" id="previewStatus">Inactive</span>
                                            <span class="badge bg-warning" id="previewPopular" style="display:none;">Popular</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips Card -->
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">💡 Tips</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Use clear, benefit-focused feature descriptions</li>
                                        <li class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Set competitive pricing based on value provided</li>
                                        <li class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Consider offering trial periods for premium plans</li>
                                        <li><i class="fas fa-lightbulb text-warning me-2"></i>Mark your best value plan as popular</li>
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
