@extends('layouts.adminApp')

@section('title', 'Meal Plans')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .table-actions {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
    .plan-thumb {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e9ecef;
    }
    .badge {
        font-size: 0.8rem;
        padding: 0.4em 0.7em;
        border-radius: 0.4rem;
    }
    .btn-sm {
        padding: 4px 8px;
    }
    .pagination {
        justify-content: center;
    }
    .table td img {
        width: 70px;
        height: 70px;
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    {{-- 🔹 Header --}}
    <div class="page-header">
        <h3 class="page-title mb-0">Meal Plans</h3>
        <a href="{{ route('nutritionist.mealplans.create') }}" class="btn btn-primary">
            <i class="ti-plus"></i> Add New Meal Plan
        </a>
    </div>

    {{-- 🔹 Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ti-check"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🔹 Filters --}}
    <form method="GET" action="{{ route('nutritionist.mealplans.index') }}" class="filter-form mb-3">
        <div class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Premium</label>
                <select name="premium" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('premium') === '1' ? 'selected' : '' }}>Premium Only</option>
                    <option value="0" {{ request('premium') === '0' ? 'selected' : '' }}>Non-Premium</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Difficulty</label>
                <select name="difficulty" class="form-select">
                    <option value="">All Levels</option>
                    @foreach (['easy', 'medium', 'hard'] as $diff)
                        <option value="{{ $diff }}" {{ request('difficulty') === $diff ? 'selected' : '' }}>
                            {{ ucfirst($diff) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Goal</label>
                <select name="goal" class="form-select">
                    <option value="">All Goals</option>
                    @foreach (['general_health', 'weight_loss', 'muscle_gain'] as $goal)
                        <option value="{{ $goal }}" {{ request('goal') === $goal ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $goal)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="ti-filter"></i> Apply
                </button>
                <a href="{{ route('nutritionist.mealplans.index') }}" class="btn btn-outline-secondary flex-grow-1">
                    <i class="ti-reload"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            @if ($mealPlans->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:10%">Image</th>
                                <th style="width:25%">Title</th>
                                <th style="width:10%">Calories</th>
                                <th style="width:10%">Days</th>
                                <th style="width:10%">Difficulty</th>
                                <th style="width:10%">Goal</th>
                                <th style="width:10%">Premium</th>
                                <th style="width:10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mealPlans as $index => $plan)
                                <tr>
                                    <td>{{ $mealPlans->firstItem() + $index }}</td>
                                    <td>
                                        <img src="{{ $plan->image_url ?? asset('assets/images/default-meal.jpg') }}" 
                                             class="plan-thumb" alt="Meal Plan Image">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $plan->title }}</div>
                                        <small class="text-muted">
                                            {{ Str::limit($plan->description, 50) ?? '—' }}
                                        </small>
                                    </td>
                                    <td><span class="fw-semibold">{{ $plan->total_calories }}</span> kcal</td>
                                    <td>{{ $plan->duration_days }} days</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $plan->difficulty === 'easy' ? 'success' : 
                                            ($plan->difficulty === 'medium' ? 'warning text-dark' : 'danger') 
                                        }}">
                                            {{ ucfirst($plan->difficulty) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ ucwords(str_replace('_', ' ', $plan->goal)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $plan->is_premium ? 'primary' : 'secondary' }}">
                                            {{ $plan->is_premium ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('nutritionist.mealplans.show', $plan->id) }}" 
                                               class="btn btn-outline-info btn-sm" title="View Details">
                                                <i class="ti-eye"></i>
                                            </a>
                                            <a href="{{ route('nutritionist.mealplans.edit', $plan->id) }}" 
                                               class="btn btn-outline-primary btn-sm" title="Edit Meal Plan">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <a href="{{ route('nutritionist.mealplans.assign', $plan->id) }}" 
                                                class="btn btn-outline-success btn-sm" title="Assign Recipes">
                                                    <i class="ti-link"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('nutritionist.mealplans.destroy', $plan->id) }}" 
                                                  onsubmit="return confirm('Are you sure you want to delete this meal plan?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" title="Delete Meal Plan">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $mealPlans->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted mb-2">No meal plans found.</p>
                    <!-- <a href="{{ route('nutritionist.mealplans.create') }}" class="btn btn-primary">
                        <i class="ti-plus"></i> Create Your First Meal Plan
                    </a> -->
                </div>
            @endif
        </div>
    </div>
</div>
@endsection