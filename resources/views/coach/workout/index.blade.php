@extends('layouts.adminApp')

@section('title', 'Manage Workouts')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1>Manage Workouts</h1>
        <a href="{{ route('coach.workout.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Create New Workout
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="workouts-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>#</th>
                            <th>Title</th>
                            <th>Level</th>
                            <th>Duration</th>
                            <th>Exercises</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    $(function() {
// Initialize DataTable
var table = $('#workouts-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route('coach.workout.getWorkouts') }}',
    columns: [
        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'title', name: 'title' },
        { data: 'level', name: 'level' },
        { data: 'duration_minutes', name: 'duration_minutes' },
        { data: 'exercises_count', name: 'exercises_count', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
    ]
});

// Handle "select all" checkbox
$('#select-all').on('click', function(){
    var rows = table.rows({ 'search': 'applied' }).nodes();
    $('input[type="checkbox"]', rows).prop('checked', this.checked);
});
});
</script>
@endpush
