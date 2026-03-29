@extends('layouts.adminApp')

@section('title', 'View Workouts')

@section('content')
    <div class="content-wrapper">
        <h1>{{ $workout->title }}</h1>
        <div>
            <a href="{{ route('coach.workout.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('coach.workout.edit', $workout->id) }}" class="btn btn-primary">
                <i class="ti ti-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Workout Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Description:</strong><br>{{ $workout->description ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-5">Level:</dt>
                        <dd class="col-sm-7"><span
                                class="badge bg-{{ ['beginner' => 'success', 'intermediate' => 'warning', 'advanced' => 'danger'][$workout->level] }}">{{ ucfirst($workout->level) }}</span>
                        </dd>

                        <dt class="col-sm-5">Duration:</dt>
                        <dd class="col-sm-7">{{ $workout->duration_minutes }} minutes</dd>

                        <dt class="col-sm-5">Calories Burned:</dt>
                        <dd class="col-sm-7">{{ $workout->calories_burned ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Thumbnail:</dt>
                        <dd class="col-sm-7">
                            @if ($workout->thumbnail_url)
                                <a href="{{ $workout->thumbnail_url }}" target="_blank">View Image</a>
                            @else
                                N/A
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Exercises in this Workout</h5>
            @if ($workout->exercises->count())
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Exercise Name</th>
                            <th>Sets</th>
                            <th>Reps</th>
                            <th>Duration (sec)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($workout->exercises->sortBy('pivot.order') as $exercise)
                            <tr>
                                <td>{{ $exercise->pivot->order }}</td>
                                <td>{{ $exercise->name }}</td>
                                <td>{{ $exercise->pivot->sets ?? '-' }}</td>
                                <td>{{ $exercise->pivot->reps ?? '-' }}</td>
                                <td>{{ $exercise->pivot->duration_seconds ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No exercises have been added to this workout yet.</p>
            @endif
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <h4>Assign Plans to Workout</h4>
            <form action="{{ route('assign.doc.plans', ['type' => 'workout', 'id' => $workout->id]) }}" method="POST">
                @csrf
                <select name="plans[]" class="form-control select2" multiple>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" {{ $workout->plans->contains($plan->id) ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary mt-3">Save</button>
            </form>
        </div>
    </div>
@endsection
