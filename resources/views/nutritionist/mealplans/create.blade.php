@extends('layouts.adminApp')

@section('title', 'Create Meal Plan')

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title mb-0">Create Meal Plan</h3>
        <a href="{{ route('nutritionist.mealplans.index') }}" class="btn btn-secondary"><i class="ti-arrow-left"></i> Back</a>
    </div>

    <form action="{{ route('nutritionist.mealplans.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('nutritionist.mealplans.form')

        <div class="text-end">
            <button type="submit" class="btn btn-primary"><i class="ti-save"></i> Save Meal Plan</button>
        </div>
    </form>
</div>
@endsection