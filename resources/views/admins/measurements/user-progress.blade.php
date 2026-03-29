@extends('layouts.adminApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $user->name }}'s Progress</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('measurements.index') }}">Measurements</a></li>
                        <li class="breadcrumb-item active">User Progress</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <p class="mb-1"><strong>{{ $totalMeasurements }}</strong> measurements</p>
                            <small class="text-muted">Member since {{ $user->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($latestMeasurement)
    <!-- Latest Measurements Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Current Weight</p>
                            <h4 class="mb-2">{{ $latestMeasurement->weight ?: $latestMeasurement->weight_kg ?: 'N/A' }}
                                @if($latestMeasurement->weight || $latestMeasurement->weight_kg) kg @endif
                            </h4>
                            @if($weightChange)
                                <p class="text-muted mb-0">
                                    @if($weightChange > 0)
                                        <span class="text-success">+{{ number_format($weightChange, 1) }} kg</span>
                                    @else
                                        <span class="text-danger">{{ number_format($weightChange, 1) }} kg</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ti-target font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($latestMeasurement->body_fat_percentage)
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Body Fat</p>
                            <h4 class="mb-2">{{ $latestMeasurement->body_fat_percentage }}%</h4>
                            @if($bodyFatChange)
                                <p class="text-muted mb-0">
                                    @if($bodyFatChange > 0)
                                        <span class="text-warning">+{{ number_format($bodyFatChange, 1) }}%</span>
                                    @else
                                        <span class="text-success">{{ number_format($bodyFatChange, 1) }}%</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ti-activity font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($latestMeasurement->muscle_mass)
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Muscle Mass</p>
                            <h4 class="mb-2">{{ $latestMeasurement->muscle_mass }} kg</h4>
                            @if($muscleMassChange)
                                <p class="text-muted mb-0">
                                    @if($muscleMassChange > 0)
                                        <span class="text-success">+{{ number_format($muscleMassChange, 1) }} kg</span>
                                    @else
                                        <span class="text-danger">{{ number_format($muscleMassChange, 1) }} kg</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ti-trending-up font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($latestMeasurement->bmi)
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">BMI</p>
                            <h4 class="mb-2">{{ number_format($latestMeasurement->bmi, 1) }}</h4>
                            @php
                                $bmi = $latestMeasurement->bmi;
                                if ($bmi < 18.5) { $category = 'Underweight'; $color = 'info'; }
                                elseif ($bmi < 25) { $category = 'Normal'; $color = 'success'; }
                                elseif ($bmi < 30) { $category = 'Overweight'; $color = 'warning'; }
                                else { $category = 'Obese'; $color = 'danger'; }
                            @endphp
                            <p class="text-muted mb-0">
                                <span class="badge bg-{{ $color }}">{{ $category }}</span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-{{ $color }} rounded-3">
                                <i class="ti-dashboard font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Progress Charts -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Weight Progress</h5>
                </div>
                <div class="card-body">
                    <canvas id="weightChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Body Composition</h5>
                </div>
                <div class="card-body">
                    <canvas id="bodyCompositionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Circumference Progress -->
    @if($measurements->where('waist_circumference', '!=', null)->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Circumference Measurements</h5>
                </div>
                <div class="card-body">
                    <canvas id="circumferenceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Measurements Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Measurements</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="userMeasurementsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Weight (kg)</th>
                                    <th>Height (cm)</th>
                                    <th>Body Fat %</th>
                                    <th>Muscle Mass</th>
                                    <th>BMI</th>
                                    <th>Waist (cm)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($measurements as $measurement)
                                <tr>
                                    <td>{{ $measurement->date->format('M j, Y') }}</td>
                                    <td>{{ $measurement->weight ?: $measurement->weight_kg ?: '-' }}</td>
                                    <td>{{ $measurement->height ?: '-' }}</td>
                                    <td>{{ $measurement->body_fat_percentage ? $measurement->body_fat_percentage . '%' : '-' }}</td>
                                    <td>{{ $measurement->muscle_mass ? $measurement->muscle_mass . ' kg' : '-' }}</td>
                                    <td>{{ $measurement->bmi ? number_format($measurement->bmi, 1) : '-' }}</td>
                                    <td>{{ $measurement->waist_circumference ?: '-' }}</td>
                                    <td>
                                        <a href="{{ route('measurements.show', $measurement) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    $('#userMeasurementsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });

    // Weight Progress Chart
    const weightData = @json($measurements->pluck('weight')->map(function($w, $key) use ($measurements) {
        return $w ?: $measurements[$key]->weight_kg;
    })->filter());
    const weightDates = @json($measurements->pluck('date')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('M j');
    }));

    const weightCtx = document.getElementById('weightChart').getContext('2d');
    new Chart(weightCtx, {
        type: 'line',
        data: {
            labels: weightDates,
            datasets: [{
                label: 'Weight (kg)',
                data: weightData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    // Body Composition Chart
    const bodyFatData = @json($measurements->pluck('body_fat_percentage')->filter());
    const muscleMassData = @json($measurements->pluck('muscle_mass')->filter());

    if (bodyFatData.length > 0 || muscleMassData.length > 0) {
        const bodyCtx = document.getElementById('bodyCompositionChart').getContext('2d');
        new Chart(bodyCtx, {
            type: 'line',
            data: {
                labels: weightDates,
                datasets: [
                    @if($measurements->where('body_fat_percentage', '!=', null)->count() > 0)
                    {
                        label: 'Body Fat %',
                        data: bodyFatData,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        yAxisID: 'y'
                    },
                    @endif
                    @if($measurements->where('muscle_mass', '!=', null)->count() > 0)
                    {
                        label: 'Muscle Mass (kg)',
                        data: muscleMassData,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                    @endif
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }

    // Circumference Chart
    @if($measurements->where('waist_circumference', '!=', null)->count() > 0)
    const circumferenceCtx = document.getElementById('circumferenceChart').getContext('2d');
    new Chart(circumferenceCtx, {
        type: 'line',
        data: {
            labels: weightDates,
            datasets: [
                @if($measurements->where('waist_circumference', '!=', null)->count() > 0)
                {
                    label: 'Waist (cm)',
                    data: @json($measurements->pluck('waist_circumference')->filter()),
                    borderColor: 'rgb(255, 206, 86)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    tension: 0.1
                },
                @endif
                @if($measurements->where('chest_circumference', '!=', null)->count() > 0)
                {
                    label: 'Chest (cm)',
                    data: @json($measurements->pluck('chest_circumference')->filter()),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                },
                @endif
                @if($measurements->where('arm_circumference', '!=', null)->count() > 0)
                {
                    label: 'Arm (cm)',
                    data: @json($measurements->pluck('arm_circumference')->filter()),
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.1
                }
                @endif
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    @endif
});
</script>
@endpush
@endsection
