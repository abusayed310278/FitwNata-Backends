@extends('layouts.adminApp')

@section('title', $mealPlan->title)

@push('styles')
<style>
    .section-title {
        font-weight: 600;
        color: #2c3e50;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.3rem;
    }
    .recipe-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        background-color: #fff;
        transition: all 0.2s;
    }
    .recipe-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .recipe-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    .day-section {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        margin-top: 1.5rem;
    }
    .meal-type {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.6rem;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">{{ $mealPlan->title }}</h3>
        <a href="{{ route('nutritionist.mealplans.index') }}" class="btn btn-secondary">
            <i class="ti-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- 🔹 Basic Info --}}
            <div class="text-center mb-4">
                <img src="{{ $mealPlan->image_url ?? asset('assets/images/default-meal.jpg') }}" 
                     width="200" class="rounded shadow-sm mb-3" alt="Meal Plan Image">

                <h5 class="fw-semibold">{{ $mealPlan->description }}</h5>
                <p class="mb-1">
                    <strong>Duration:</strong> {{ $mealPlan->duration_days }} days |
                    <strong>Calories:</strong> {{ $mealPlan->total_calories }} kcal
                </p>
                <p class="mb-1">
                    <strong>Difficulty:</strong> {{ ucfirst($mealPlan->difficulty) }} |
                    <strong>Goal:</strong> {{ ucwords(str_replace('_', ' ', $mealPlan->goal)) }}
                </p>

                @if($mealPlan->is_premium)
                    <span class="badge bg-primary mt-2">Premium Plan</span>
                @endif
            </div>

            {{-- 🔹 Recipes Grouped by Day and Meal Type --}}
            <h5 class="section-title">Assigned Recipes</h5>

            @php
                $recipesByDay = $mealPlan->recipes
                    ->groupBy('pivot.day_of_week')
                    ->sortKeys();
                $daysOfWeek = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
                $mealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
            @endphp

            @if($recipesByDay->isEmpty())
                <p class="text-muted text-center py-4">No recipes assigned yet.</p>
            @else
                @foreach($recipesByDay as $dayNumber => $recipes)
                    <div class="day-section">
                        <h6 class="fw-bold mb-3">
                            <i class="ti-calendar text-primary"></i> {{ $daysOfWeek[$dayNumber] ?? 'Day '.$dayNumber }}
                        </h6>
                        <div class="row">
                            @foreach($mealTypes as $type)
                                @php
                                    $recipesForType = $recipes->where('pivot.meal_type', $type);
                                @endphp
                                <div class="col-md-3 mb-3">
                                    <div class="meal-type text-capitalize">{{ $type }}</div>
                                    @forelse($recipesForType as $recipe)
                                        <div class="recipe-card">
                                            <img src="{{ $recipe->image_url ?? asset('assets/images/default-meal.jpg') }}" alt="Recipe">
                                            <div class="fw-semibold text-dark">{{ $recipe->title }}</div>
                                            <small class="text-muted">{{ $recipe->calories }} kcal</small>
                                        </div>
                                    @empty
                                        <p class="text-muted small">No {{ $type }} recipe</p>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection