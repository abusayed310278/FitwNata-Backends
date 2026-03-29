@extends('layouts.adminApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Measurement Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('measurements.index') }}">Measurements</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Measurement Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Measurement Record</h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('measurements.user.progress', $measurement->user_id) }}">
                                    <i class="ti-chart-line me-2"></i>View Progress
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteMeasurement({{ $measurement->id }})">
                                    <i class="ti-trash me-2"></i>Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Basic Measurements -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Basic Measurements</h6>
                            <div class="row">
                                @if($measurement->weight)
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted">Weight</label>
                                    <p class="h5 mb-0">{{ $measurement->weight }} kg</p>
                                </div>
                                @endif

                                @if($measurement->height)
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted">Height</label>
                                    <p class="h5 mb-0">{{ $measurement->height }} cm</p>
                                </div>
                                @endif

                                @if($measurement->weight_kg && $measurement->weight_kg !== $measurement->weight)
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted">Weight (Legacy)</label>
                                    <p class="h5 mb-0">{{ $measurement->weight_kg }} kg</p>
                                </div>
                                @endif

                                @if($measurement->bmi)
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted">BMI</label>
                                    <p class="h5 mb-0">
                                        {{ number_format($measurement->bmi, 1) }}
                                        @php
                                            $bmi = $measurement->bmi;
                                            if ($bmi < 18.5) $category = 'Underweight';
                                            elseif ($bmi < 25) $category = 'Normal';
                                            elseif ($bmi < 30) $category = 'Overweight';
                                            else $category = 'Obese';
                                        @endphp
                                        <small class="text-muted">({{ $category }})</small>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6>Body Composition</h6>
                            <div class="row">
                                @if($measurement->body_fat_percentage)
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted">Body Fat</label>
                                    <p class="h5 mb-0">{{ $measurement->body_fat_percentage }}%</p>
                                </div>
                                @endif

                                @if($measurement->muscle_mass)
                                <div class="col-6 mb-3">
                                    <label class="form-label text-muted">Muscle Mass</label>
                                    <p class="h5 mb-0">{{ $measurement->muscle_mass }} kg</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Circumference Measurements -->
                    @if($measurement->waist_circumference || $measurement->chest_circumference || $measurement->arm_circumference || $measurement->thigh_circumference)
                    <div class="mb-4">
                        <h6>Circumference Measurements</h6>
                        <div class="row">
                            @if($measurement->waist_circumference)
                            <div class="col-md-3 col-6 mb-3">
                                <label class="form-label text-muted">Waist</label>
                                <p class="h6 mb-0">{{ $measurement->waist_circumference }} cm</p>
                            </div>
                            @endif

                            @if($measurement->chest_circumference)
                            <div class="col-md-3 col-6 mb-3">
                                <label class="form-label text-muted">Chest</label>
                                <p class="h6 mb-0">{{ $measurement->chest_circumference }} cm</p>
                            </div>
                            @endif

                            @if($measurement->arm_circumference)
                            <div class="col-md-3 col-6 mb-3">
                                <label class="form-label text-muted">Arm</label>
                                <p class="h6 mb-0">{{ $measurement->arm_circumference }} cm</p>
                            </div>
                            @endif

                            @if($measurement->thigh_circumference)
                            <div class="col-md-3 col-6 mb-3">
                                <label class="form-label text-muted">Thigh</label>
                                <p class="h6 mb-0">{{ $measurement->thigh_circumference }} cm</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($measurement->notes)
                    <div class="mb-4">
                        <h6>Notes</h6>
                        <div class="alert alert-light">
                            {{ $measurement->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Measurement Sidebar -->
        <div class="col-lg-4">
            <!-- User Information -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $measurement->user->name }}</h6>
                            <p class="text-muted mb-0">{{ $measurement->user->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Member since:</small>
                        <small>{{ $measurement->user->created_at->format('M Y') }}</small>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Total measurements:</small>
                        <small>{{ $measurement->user->measurements()->count() }}</small>
                    </div>
                </div>
            </div>

            <!-- Measurement Metadata -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Measurement Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Measurement Date</label>
                        <p class="mb-0">{{ $measurement->date->format('F j, Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Recorded On</label>
                        <p class="mb-0">{{ $measurement->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>

                    @if($measurement->updated_at != $measurement->created_at)
                    <div class="mb-3">
                        <label class="form-label">Last Updated</label>
                        <p class="mb-0">{{ $measurement->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Record ID</label>
                        <code>#{{ $measurement->id }}</code>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('measurements.user.progress', $measurement->user_id) }}" class="btn btn-primary mb-2 w-100">
                        <i class="ti-chart-line me-2"></i>View User Progress
                    </a>
                    <a href="{{ route('measurements.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti-arrow-left me-2"></i>Back to Measurements
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteMeasurement(id) {
    if (confirm('Are you sure you want to delete this measurement? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/measurements/${id}`;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
