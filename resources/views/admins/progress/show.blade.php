@extends('layouts.adminApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Progress Entry Details</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('progress.index') }}">Progress Journals</a></li>
                        <li class="breadcrumb-item active">Entry Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Entry Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            {{ $entry->title ?: 'Progress Entry #' . $entry->id }}
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteEntry({{ $entry->id }})">
                                    <i class="ti-trash me-2"></i>Delete Entry
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Entry Meta -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Entry Type:</small>
                                <p class="mb-1">
                                    @php
                                        $typeColors = [
                                            'workout' => 'primary',
                                            'nutrition' => 'success',
                                            'wellness' => 'info',
                                            'measurements' => 'warning',
                                            'goals' => 'secondary',
                                            'coach_note' => 'danger'
                                        ];
                                        $color = $typeColors[$entry->entry_type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $entry->entry_type)) }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Entry Date:</small>
                                <p class="mb-1"><strong>{{ $entry->entry_date->format('F j, Y') }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Ratings -->
                    @if($entry->mood_rating || $entry->energy_level)
                    <div class="mb-4">
                        <div class="row">
                            @if($entry->mood_rating)
                            <div class="col-md-6">
                                <small class="text-muted">Mood Rating:</small>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $entry->mood_rating)
                                            <i class="ti-star text-warning me-1"></i>
                                        @else
                                            <i class="ti-star text-muted me-1"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2">{{ $entry->mood_rating }}/5</span>
                                </div>
                            </div>
                            @endif

                            @if($entry->energy_level)
                            <div class="col-md-6">
                                <small class="text-muted">Energy Level:</small>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $entry->energy_level)
                                            <i class="ti-bolt text-primary me-1"></i>
                                        @else
                                            <i class="ti-bolt text-muted me-1"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2">{{ $entry->energy_level }}/5</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Entry Content -->
                    @if($entry->content)
                    <div class="mb-4">
                        <h6>Content</h6>
                        <div class="border rounded p-3">
                            {!! nl2br(e($entry->content)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Legacy Notes -->
                    @if($entry->notes)
                    <div class="mb-4">
                        <h6>Notes</h6>
                        <div class="alert alert-light">
                            {{ $entry->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Entry Sidebar -->
        <div class="col-lg-4">
            <!-- User Information -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $entry->user->name }}</h6>
                            <p class="text-muted mb-0">{{ $entry->user->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <strong>Member since:</strong> {{ $entry->user->created_at->format('M Y') }}
                    </small>
                </div>
            </div>

            <!-- Coach Information -->
            @if($entry->coach)
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Coach Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $entry->coach->name }}</h6>
                            <p class="text-muted mb-0">{{ $entry->coach->email }}</p>
                            <small class="badge bg-info">Coach</small>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted">
                        <strong>Joined:</strong> {{ $entry->coach->created_at->format('M Y') }}
                    </small>
                </div>
            </div>
            @endif

            <!-- Entry Metadata -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Entry Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Entry ID</label>
                        <code>#{{ $entry->id }}</code>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Created</label>
                        <p class="mb-0">{{ $entry->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Last Modified</label>
                        <p class="mb-0">{{ $entry->updated_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>

                    <!-- Legacy Mood (if different from mood_rating) -->
                    @if($entry->mood && $entry->mood !== $entry->mood_rating)
                    <div class="mb-3">
                        <label class="form-label">Legacy Mood</label>
                        <p class="mb-0">{{ $entry->mood }}/5</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteEntry(id) {
    if (confirm('Are you sure you want to delete this progress entry? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/progress/${id}`;

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
