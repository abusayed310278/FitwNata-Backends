@extends('layouts.adminApp')

@section('title', 'Reports & Feedback')

@push('styles')
{{-- Include DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .table th, .table td { vertical-align: middle; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; margin-right: 15px; }
    .badge-custom { padding: 0.5em 0.9em; border-radius: 20px; font-weight: 500; font-size: 0.75rem; }
    .badge-purple { background-color: #f3e8ff; color: #8e44ad; }
    .badge-green { background-color: #e6f4ea; color: #28a745; }
    .badge-primary { background-color: #cce7ff; color: #004085; }
    .badge-warning { background-color: #fff3cd; color: #856404; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Nutritionist</h1>
        <a href="{{ route('nutritionist.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Create New Nutritionist
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="nutritionists-table">
                    <thead>
                        <tr>
                            {{-- <th><input type="checkbox" id="master-checkbox" /></th> --}}
                             <th>Actions</th>
                            <th>Nutritionist Details</th>
                            <th>Contact Information</th>
                            <th>Specialties</th>
                            <th>Content</th>
                            <th>Consultations</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function() {
    $('#nutritionists-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('nutritionist.user_list') }}',
        columns: [
            // { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
              { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'contact', name: 'contact', orderable: false, searchable: false },
            { data: 'specialties', name: 'specialties', orderable: false, searchable: false },
            { data: 'content', name: 'content', orderable: false, searchable: false },
            { data: 'consultations', name: 'consultations', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },

        ],
        "language": { "info": "Showing _START_ to _END_ of _TOTAL_ nutritionists" },
        "order": [[1, 'asc']]
    });
});
</script>
@endpush
