@extends('layouts.adminApp')
@section('title', 'View Exercises')

@section('content')
<div class="content-wrapper">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ $exercise->name }}</h1>
    <div>
        <a href="{{ route('coach.exercise.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left"></i> Back to List
        </a>
        <a href="{{ route('exercise.edit', $exercise->id) }}" class="btn btn-primary">
            <i class="ti ti-pencil"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Exercise Video</h5>
                @if($exercise->video_url)
                    @php
                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $exercise->video_url, $match);
                        $youtube_id = $match[1] ?? null;
                    @endphp
                    @if($youtube_id)
                        <div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/{{ $youtube_id }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    @else
                        <p>Invalid YouTube URL. <a href="{{ $exercise->video_url }}" target="_blank">View link</a></p>
                    @endif
                @else
                    <p class="text-muted">No video has been added.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Details</h5>
                <p><strong>Description:</strong><br>{{ $exercise->description ?? 'N/A' }}</p>
                <hr>
                <dl class="row">
                    <dt class="col-sm-6">Equipment:</dt>
                    <dd class="col-sm-6">{{ $exercise->equipment_needed ?? 'N/A' }}</dd>
                    <dt class="col-sm-6">Calories Burned:</dt>
                    <dd class="col-sm-6">{{ $exercise->calories_per_rep_or_second ?? 'N/A' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
