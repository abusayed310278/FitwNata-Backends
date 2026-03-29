@extends('layouts.adminApp')

@section('title', 'Coach Details')

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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
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

        .availability-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .availability-item:last-child {
            border-bottom: none;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        {{-- Page Header --}}
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="page-title">Coach Details</h3>

            </div>
            <div>
                {{-- <a href="{{ route('coach.edit', $coach) }}" class="btn btn-primary me-2">
                <i class="ti-pencil"></i> Edit Coach
            </a>
            <form action="{{ route('coach.destroy', $coach) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this coach?')">
                    <i class="ti-trash"></i> Delete
                </button>
            </form> --}}
            </div>
        </div>

        <div class="row">
            {{-- Left Column: Coach Profile --}}
            <div class="col-lg-4">
                {{-- Profile Card --}}
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="{{ $coach->profile_photo_url ?? asset('assets/images/faces/face28.jpg') }}"
                            alt="Coach Profile" class="profile-photo mb-3">
                        <h4 class="mb-1">{{ $coach->name }} {{ $coach->last_name }}</h4>
                        <p class="text-muted mb-2">{{ $coach->display_name }}</p>
                        <span class="badge badge-custom badge-success">Coach</span>

                        {{-- Contact Information --}}
                        <div class="contact-info text-start mt-4">
                            <h6 class="fw-bold mb-3">Contact Information</h6>
                            <div class="mb-2">
                                <i class="ti-email"></i>
                                <a href="mailto:{{ $coach->email }}">{{ $coach->email }}</a>
                            </div>
                            @if ($coach->whatsapp)
                                <div class="mb-2">
                                    <i class="ti-mobile"></i>
                                    <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $coach->whatsapp) }}"
                                        target="_blank">{{ $coach->whatsapp }}</a>
                                </div>
                            @endif
                            <div>
                                <i class="ti-calendar"></i>
                                Joined {{ $coach->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Biography Card --}}
                @if ($coach->bio)
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="section-title">Biography</h5>
                            <p class="text-muted">{{ $coach->bio }}</p>
                        </div>
                    </div>
                @endif
                @if ($coach->status != 'approved')
                    <div class="">
                        <!-- Quick Actions -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="section-title">Quick Actions</h5>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-warning"
                                        onclick="approveCoach({{ $coach->id }})">
                                        <i class="ti-key me-2"></i> Approved Account
                                    </button>
                                </div>
                            </div>
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
                            <h3>{{ $coach->appointmentsAsCoach->count() }}</h3>
                            <p>Total Appointments</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card mb-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h3>{{ $coach->appointmentsAsCoach->where('status', 'pending')->count() }}</h3>
                            <p>Pending Appointments</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card mb-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            @php
                                $availableDaysCount = $coach->availabilities
                                    ->where('is_blocked', false)
                                    ->whereNotNull('day_of_week')
                                    ->groupBy('day_of_week')
                                    ->count();
                            @endphp
                            <h3>{{ $availableDaysCount }}</h3>
                            <p>Availability Days</p>
                        </div>
                    </div>
                </div>

                {{-- Availability Schedule --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="section-title">Weekly Availability</h5>
                        @php
                            $daysOfWeek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

                            // Filter ONLY weekly availability (exclude blocked/specific-date entries),
                            // then group by day name
                            $weeklyGrouped = $coach->availabilities
                                ->where('is_blocked', false)
                                ->whereNotNull('day_of_week')
                                ->groupBy('day_of_week');

                            // Helper to sort slots within a day by start_time
                            $sortSlots = fn($slots) => $slots->sortBy('start_time');
                        @endphp

                        @if ($weeklyGrouped->isNotEmpty())
                            @foreach ($daysOfWeek as $dayName)
                                @if ($weeklyGrouped->has($dayName))
                                    <div class="availability-item">
                                        <span class="fw-bold">{{ $dayName }}</span>
                                        <span>
                                            @foreach ($sortSlots($weeklyGrouped->get($dayName)) as $slot)
                                                <span class="badge bg-light text-dark border me-2 mb-2">
                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                                </span>
                                            @endforeach
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="ti-calendar fs-1 mb-3"></i>
                                <p>No weekly availability set for this coach.</p>
                                <small>The coach needs to set their weekly schedule.</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Appointments --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="section-title">Recent Appointments</h5>
                        @if ($coach->appointmentsAsCoach->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Date & Time</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($coach->appointmentsAsCoach->take(5) as $appointment)
                                            <tr>
                                                <td>{{ $appointment->user->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('M d, Y g:i A') }}
                                                </td>
                                                <td>{{ ucfirst($appointment->appointment_type) }}</td>
                                                <td>
                                                    @switch($appointment->status)
                                                        @case('pending')
                                                            <span class="badge badge-custom badge-warning">Pending</span>
                                                        @break

                                                        @case('approved')
                                                            <span class="badge badge-custom badge-success">Approved</span>
                                                        @break

                                                        @case('completed')
                                                            <span class="badge badge-custom badge-primary">Completed</span>
                                                        @break

                                                        @case('canceled')
                                                            <span class="badge badge-custom badge-danger">Canceled</span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="badge badge-custom badge-warning">{{ ucfirst($appointment->status) }}</span>
                                                    @endswitch
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="ti-calendar fs-1 mb-3"></i>
                                <p>No appointments scheduled yet.</p>
                                <small>Clients can book appointments with this coach once availability is set.</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Articles/Content --}}
                @if ($coach->articles->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="section-title">Published Articles</h5>
                            <div class="list-group list-group-flush">
                                @foreach ($coach->articles->take(5) as $article)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">{{ $article->title }}</h6>
                                                <small class="text-muted">Published
                                                    {{ $article->published_at?->diffForHumans() ?? 'Draft' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('coach.index') }}" class="btn btn-secondary">
                        <i class="ti-arrow-left"></i> Back to Coaches
                    </a>
                    {{-- <div>
                    <a href="{{ route('coach.edit', $coach) }}" class="btn btn-primary">
                        <i class="ti-pencil"></i> Edit Coach
                    </a>
                </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function approveCoach(appointmentId) {
            showConfirm("Are you sure?", "Do you want to approve this coach?")
                .then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/coach/update/status/${appointmentId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Approved!", data.message, "success")
                                        .then(() => {
                                            // Refresh DataTable
                                            $('#appointments-table').DataTable().ajax.reload();
                                        });
                                } else {
                                    Swal.fire("Error", data.message || "Something went wrong.", "error");
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire("Error", "An error occurred while approving the coach.", "error");
                            });
                    }
                });
        }
    </script>
@endpush
