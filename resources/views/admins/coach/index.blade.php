@extends('layouts.adminApp')

@section('title', 'Content & Plan')

@push('styles')
{{-- Include DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

{{-- Add custom styles for badges and table --}}
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 15px;
    }
    .badge-custom {
        padding: 0.5em 0.9em;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    .badge-purple {
        background-color: #f3e8ff;
        color: #8e44ad;
    }
    .badge-green {
        background-color: #e6f4ea;
        color: #28a745;
    }
    .badge-danger {
        background-color: #f8d7da;
        color: #dc3545;
    }
    .badge-warning {
        background-color: #fff3cd;
        color: #856404;
    }
    .badge-primary {
        background-color: #cce7ff;
        color: #004085;
    }
    .filters-container select {
        border-radius: 8px;
        padding: 8px 12px;
        border: 1px solid #e0e0e0;
        background-color: #fff;
    }
    .filters-container .form-control {
        border-radius: 8px;
    }
    /* Style DataTables elements to match your theme */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3em 0.8em;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #007bff;
        color: white !important;
        border-radius: 4px;
        border-color: #007bff;
    }
    .dataTables_wrapper .dataTables_info {
        color: #6c757d;
    }
    .page-header {
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Coach Management</h3>
        <a href="{{ route('coach.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Create New Coach
        </a>
    </div>
    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card" style="border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
        <div class="card-body">


            {{-- Coaches Table --}}
            <div class="table-responsive">
                <table class="table table-hover" id="coaches-table">
                    <thead>
                        <tr>
                            {{-- <th><input type="checkbox" id="master-checkbox" /></th> --}}
                             <th>Actions</th>
                            <th>Coach Details</th>
                            <th>Contact Information</th>
                            <th>Appointments</th>
                            <th>Availability</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        {{-- The table body will be populated by DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Include jQuery, then DataTables JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function() {
    var table = $('#coaches-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('coach.user_list') }}',
        columns: [
            // { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'contact', name: 'contact', orderable: false, searchable: false },
            { data: 'appointments', name: 'appointments', orderable: false, searchable: false },
            { data: 'availability', name: 'availability', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },

        ],
        "language": {
            "info": "Showing _START_ to _END_ of _TOTAL_ coaches"
        },
        "order": [[1, 'asc']] // Default order by name
    });

    // Optional: Add select-all functionality for the master checkbox
    $('#master-checkbox').on('click', function(e) {
        let isChecked = $(this).is(':checked');
        $('#coaches-table tbody').find('input[type="checkbox"]').prop('checked', isChecked);
    });

    // Filter functionality
    $('#availability-filter, #status-filter').on('change', function() {
        table.draw();
    });
});
</script>
@endpush
