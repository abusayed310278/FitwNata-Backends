@extends('layouts.adminApp')

@section('title', 'Edit Workouts')
@push('styles')
    <!-- Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush
@section('content')
<div class="content-wrapper">
<h1>Edit Workout: <span class="text-muted">{{ $workout->title }}</span></h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('coach.workout.update', $workout->id) }}" method="POST">
            @method('PUT')
            @include('coach.workout._form', ['workout' => $workout])

            <div class="mt-4">
                <a href="{{ route('coach.workout.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Workout</button>
            </div>
        </form>
    </div>
</div>
@endsection
