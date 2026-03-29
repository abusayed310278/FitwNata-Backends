@extends('layouts.adminApp')

@section('title', 'Nutritionist Details')

@push('styles')
<style>
    .section-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .page-header {
        margin-bottom: 2rem;
    }
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e9ecef;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }
    .stat-card h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: bold;
    }
    .stat-card p {
        margin: 0;
        opacity: 0.9;
    }
    .badge-custom {
        padding: 0.5em 0.9em;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    .badge-success {
        background-color: #e6f4ea;
        color: #28a745;
    }
    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    .badge-primary {
        background-color: #cce7ff;
        color: #004085;
    }
    .badge-danger {
        background-color: #f8d7da;
        color: #dc3545;
    }
    .contact-info i {
        width: 20px;
        text-align: center;
        margin-right: 8px;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Nutritionist Details</h3>

        </div>
        <div>
            <a href="{{ route('nutritionist.edit', $nutritionist) }}" class="btn btn-primary me-2">
                <i class="ti-pencil"></i> Edit Nutritionist
            </a>
            <form action="{{ route('nutritionist.destroy', $nutritionist) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this nutritionist?')">
                    <i class="ti-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Left Column: Nutritionist Profile --}}
        <div class="col-lg-4">
            {{-- Profile Card --}}
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ $nutritionist->profile_photo_url ?? asset('assets/images/faces/face28.jpg') }}"
                         alt="Nutritionist Profile" class="profile-photo mb-3">
                    <h4 class="mb-1">{{ $nutritionist->name }} {{ $nutritionist->last_name }}</h4>
                    <p class="text-muted mb-2">{{ $nutritionist->display_name }}</p>
                    <span class="badge badge-custom badge-success">Nutritionist</span>

                    {{-- Contact Information --}}
                    <div class="contact-info text-start mt-4">
                        <h6 class="fw-bold mb-3">Contact Information</h6>
                        <div class="mb-2">
                            <i class="ti-email"></i>
                            <a href="mailto:{{ $nutritionist->email }}">{{ $nutritionist->email }}</a>
                        </div>
                        @if($nutritionist->whatsapp)
                        <div class="mb-2">
                            <i class="ti-mobile"></i>
                            <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $nutritionist->whatsapp) }}" target="_blank">{{ $nutritionist->whatsapp }}</a>
                        </div>
                        @endif
                        <div>
                            <i class="ti-calendar"></i>
                            Joined {{ $nutritionist->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Biography Card --}}
            @if($nutritionist->bio)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Specialties & Bio</h5>
                    <p class="text-muted">{{ $nutritionist->bio }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Statistics and Details --}}
        <div class="col-lg-8">
            {{-- Statistics Row --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h3>{{ $nutritionist->articles->count() }}</h3>
                        <p>Total Articles</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card mb-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h3>{{ $nutritionist->appointmentsAsCoach->count() }}</h3>
                        <p>Total Consultations</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card mb-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <h3>{{ $nutritionist->appointmentsAsCoach->where('status', 'pending')->count() }}</h3>
                        <p>Pending Consultations</p>
                    </div>
                </div>
            </div>

            {{-- Recent Articles --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="section-title">Recent Articles</h5>
                    @if($nutritionist->articles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Published</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nutritionist->articles->take(5) as $article)
                                    <tr>
                                        <td>
                                            <a href="{{ route('article.edit', $article) }}">{{ $article->title }}</a>
                                        </td>
                                        <td>
                                            @if($article->is_published)
                                                <span class="badge badge-custom badge-success">Published</span>
                                            @else
                                                <span class="badge badge-custom badge-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $article->published_at ? $article->published_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="ti-file fs-1 mb-3"></i>
                            <p>No articles published by this nutritionist yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Upcoming Consultations --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="section-title">Upcoming Consultations</h5>
                    @if($nutritionist->appointmentsAsCoach->where('status', 'confirmed')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nutritionist->appointmentsAsCoach->where('status', 'confirmed')->take(5) as $appointment)
                                    <tr>
                                        <td>{{ $appointment->user->name ?? 'N/A' }}</td>
                                        <td>{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->start_time }}</td>
                                        <td>
                                            <span class="badge badge-custom badge-primary">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="ti-calendar fs-1 mb-3"></i>
                            <p>No upcoming consultations scheduled.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
