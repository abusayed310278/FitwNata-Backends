@extends('layouts.adminApp')
@section('title', 'Manage Exercises')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Exercises</h1>
        <a href="{{ route('coach.exercise.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Create New Exercise
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
            <table class="table table-bordered table-striped" id="exercises-table" style="width:100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Equipment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
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
        var table = $('#exercises-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('coach.exercise.getExercises') }}',
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, width: '1%' },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '1%' },
                { data: 'name', name: 'name' },
                { data: 'equipment_needed', name: 'equipment_needed' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, width: '10%' },
            ]
        });

        $('#select-all').on('click', function(){
            $('input[type="checkbox"]', table.rows({ 'search': 'applied' }).nodes()).prop('checked', this.checked);
        });
    });
</script>
@endpush
