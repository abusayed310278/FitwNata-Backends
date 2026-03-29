@extends('layouts.adminApp')
@section('title', 'Edit Exercises')

@section('content')
<div class="content-wrapper">
    <h1>Edit Exercise: <span class="text-muted">{{ $exercise->name }}</span></h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('coach.exercise.update', $exercise->id) }}" method="POST">
                @method('PUT')
                @include('coach.exercise._form', ['exercise' => $exercise])

                <div class="mt-4">
                    <a href="{{ route('coach.exercise.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Exercise</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
