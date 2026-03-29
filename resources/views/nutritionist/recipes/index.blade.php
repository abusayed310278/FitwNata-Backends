@extends('layouts.adminApp')

@section('title', 'Recipes')

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
    .recipe-thumb {
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
    .filter-form select,
    .filter-form .btn {
        height: 42px;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .filter-form label {
        font-size: 0.9rem;
        color: #495057;
    }

    .filter-form .btn-success {
        background-color: #006d68;
        border-color: #006d68;
    }

    .filter-form .btn-success:hover {
        background-color: #005a56;
    }

    .filter-form .btn-outline-secondary {
        border-color: #ccc;
        color: #6c757d;
    }

    .filter-form .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        border-color: #bbb;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title mb-0">Recipes</h3>
        <a href="{{ route('nutritionist.recipes.create') }}" class="btn btn-primary">
            <i class="ti-plus"></i> Add New Recipe
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="ti-check"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('nutritionist.recipes.index') }}" class="filter-form mb-4">
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

            {{-- 🔹 Buttons on same row --}}
            <div class="col-md-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-success flex-fill d-flex align-items-center justify-content-center text-white" style="min-width: 140px;">
                    <i class="ti-filter me-2"></i> Apply Filters
                </button>
                <a href="{{ route('nutritionist.recipes.index') }}" class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center" style="min-width: 100px;">
                    <i class="ti-reload me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            @if ($recipes->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:10%">Image</th>
                                <th style="width:25%">Title</th>
                                <th style="width:10%">Calories</th>
                                <th style="width:10%">Difficulty</th>
                                <th style="width:10%">Premium</th>
                                <th style="width:15%">Created</th>
                                <th style="width:15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recipes as $index => $recipe)
                                <tr>
                                    <td>{{ $recipes->firstItem() + $index }}</td>
                                    <td>
                                        <img src="{{ $recipe->image_url ?? asset('assets/images/default-meal.jpg') }}" 
                                             class="recipe-thumb" alt="Recipe Image">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $recipe->title }}</div>
                                        <small class="text-muted">
                                            {{ Str::limit($recipe->description, 50) ?? '—' }}
                                        </small>
                                    </td>
                                    <td><span class="fw-semibold">{{ $recipe->calories }}</span> kcal</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $recipe->difficulty === 'easy' ? 'success' : 
                                            ($recipe->difficulty === 'medium' ? 'warning text-dark' : 'danger') 
                                        }}">
                                            {{ ucfirst($recipe->difficulty) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $recipe->is_premium ? 'primary' : 'secondary' }}">
                                            {{ $recipe->is_premium ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $recipe->created_at?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-center">
                                        <div class="table-actions">
                                            <a href="{{ route('nutritionist.recipes.show', $recipe->id) }}" 
                                               class="btn btn-outline-info btn-sm" title="View Details">
                                                <i class="ti-eye"></i>
                                            </a>
                                            <a href="{{ route('nutritionist.recipes.edit', $recipe->id) }}" 
                                               class="btn btn-outline-primary btn-sm" title="Edit Recipe">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('nutritionist.recipes.destroy', $recipe->id) }}" 
                                                  onsubmit="return confirm('Are you sure you want to delete this recipe?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" title="Delete Recipe">
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

                <div class="mt-3">
                    {{ $recipes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted mb-2">No recipes found.</p>
                    <!-- <a href="{{ route('nutritionist.recipes.create') }}" class="btn btn-primary">
                        <i class="ti-plus"></i> Create Your First Recipe
                    </a> -->
                </div>
            @endif
        </div>
    </div>
</div>
@endsection