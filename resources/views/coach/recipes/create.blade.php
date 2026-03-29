@extends('layouts.adminApp')
@section('title', 'Create Exercises')

@section('content')
<div class="content-wrapper">
<h1>Create New Exercise</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('coach.exercise.store') }}" method="POST">
            @include('coach.exercise._form')

            <div class="mt-4">
                <a href="{{ route('coach.exercise.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Exercise</button>
            </div>
        </form>
    </div>
</div>
@endsection
