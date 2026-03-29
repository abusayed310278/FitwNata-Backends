@extends('layouts.adminApp')

@section('title', $recipe->title)

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">{{ $recipe->title }}</h3>
        <a href="{{ route('nutritionist.recipes.index') }}" class="btn btn-secondary"><i class="ti-arrow-left"></i> Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="{{ $recipe->image_url ?? asset('assets/images/default-meal.jpg') }}" width="200" class="rounded mb-3 shadow-sm">
                <h5 class="fw-semibold">{{ $recipe->description }}</h5>
                <p class="mb-1">
                    <strong>Calories:</strong> {{ $recipe->calories }} kcal |
                    <strong>Servings:</strong> {{ $recipe->servings }}
                </p>
                <p class="mb-1"><strong>Difficulty:</strong> {{ ucfirst($recipe->difficulty) }}</p>
                @if($recipe->is_premium)
                    <span class="badge bg-primary mt-2">Premium Recipe</span>
                @endif
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Ingredients</h5>
                    <ul>
                        @foreach ($recipe->ingredients as $i)
                            <li>{{ $i }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Instructions</h5>
                    <ol>
                        @foreach ($recipe->instructions as $i)
                            <li>{{ $i }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <h6 class="mt-4">Nutritional Breakdown</h6>
            <p>
                Protein: {{ $recipe->protein }}g,
                Carbs: {{ $recipe->carbs }}g,
                Fat: {{ $recipe->fat }}g
            </p>

            @if($recipe->tags)
                <p><strong>Tags:</strong> {{ is_array($recipe->tags) ? implode(', ', $recipe->tags) : $recipe->tags }}</p>
            @endif
        </div>
    </div>
</div>
@endsection