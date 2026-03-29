@extends('layouts.adminApp')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-3">User Measurements</h3>
                <!-- <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Measurements</li>
                    </ol>
                </div> -->
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Measurements</p>
                            <h4 class="mb-2">{{ $totalMeasurements }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ti-ruler font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">This Week</p>
                            <h4 class="mb-2">{{ $weekMeasurements }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ti-calendar font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Active Users</p>
                            <h4 class="mb-2">{{ $activeMeasurementUsers }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ti-users font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Avg Weight (kg)</p>
                            <h4 class="mb-2">{{ number_format($avgWeight, 1) }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ti-target font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Measurements Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">User Measurements</h5>
                        <div class="d-flex gap-2">
                            <!-- Date Range Filter -->
                            <input type="date" id="dateFrom" class="form-control form-control-sm" style="width: auto;" placeholder="From Date">
                            <input type="date" id="dateTo" class="form-control form-control-sm" style="width: auto;" placeholder="To Date">
                            <button class="btn btn-sm btn-primary" onclick="applyDateFilter()">Filter</button>
                            <button class="btn btn-sm btn-secondary" onclick="clearDateFilter()">Clear</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="measurementsTable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>Weight (kg)</th>
                                    <th>Height (cm)</th>
                                    <th>Body Fat %</th>
                                    <th>Muscle Mass</th>
                                    <th>BMI</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    window.measurementsTable = $('#measurementsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('measurements.getMeasurements') }}",
            data: function(d) {
                d.date_from = $('#dateFrom').val();
                d.date_to = $('#dateTo').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'user', name: 'user.name' },
            { data: 'date', name: 'date' },
            { data: 'weight', name: 'weight' },
            { data: 'height', name: 'height' },
            { data: 'body_fat_percentage', name: 'body_fat_percentage' },
            { data: 'muscle_mass', name: 'muscle_mass' },
            { data: 'bmi', name: 'bmi' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            emptyTable: "No measurements found"
        }
    });
});

function applyDateFilter() {
    window.measurementsTable.draw();
}

function clearDateFilter() {
    $('#dateFrom').val('');
    $('#dateTo').val('');
    window.measurementsTable.draw();
}

function viewMeasurement(id) {
    window.location.href = `/admin/measurements/${id}`;
}

function viewUserProgress(userId) {
    window.location.href = `/admin/measurements/user/${userId}`;
}

function deleteMeasurement(id) {
    if (confirm('Are you sure you want to delete this measurement? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/measurements/${id}`;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
