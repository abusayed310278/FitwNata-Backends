@extends('layouts.adminApp')

@section('title', 'Create Workouts')
@push('styles')
    <!-- Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush
@section('content')
<div class="content-wrapper">
<h1>Create New Workout</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('coach.workout.store') }}" method="POST">
            @include('coach.workout._form')

            <div class="mt-4">
                <a href="{{ route('coach.workout.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Workout</button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
