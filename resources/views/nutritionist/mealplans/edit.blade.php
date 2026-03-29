@extends('layouts.adminApp')

@section('title', 'Edit Meal Plan')

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title mb-0">Edit Meal Plan</h3>
        <a href="{{ route('nutritionist.mealplans.index') }}" class="btn btn-secondary"><i class="ti-arrow-left"></i> Back</a>
    </div>

    <form action="{{ route('nutritionist.mealplans.update', $mealPlan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('nutritionist.mealplans.form', ['mealPlan' => $mealPlan])

        <div class="text-end">
            <button type="submit" class="btn btn-primary"><i class="ti-save"></i> Update Meal Plan</button>
        </div>
    </form>
</div>
@endsection