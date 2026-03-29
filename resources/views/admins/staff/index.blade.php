@extends('layouts.adminApp')

@section('title', 'Users')

@push('styles')
    {{-- Include DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    {{-- Add custom styles for badges to match the image --}}
    <style>
        .table th,
        .table td {
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
            background-color: #fce8e6;
            color: #dc3545;
        }

        .actions-menu {
            border: none;
            background: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .filters-container select,
        .filters-container .btn {
            border-radius: 8px;
        }

        .filters-container .form-control {
            border: 1px solid #e0e0e0;
            background-color: #fff;
        }

        /* Style DataTables elements to match your theme */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.3em 0.8em;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #007bff;
            /* Primary color */
            color: white !important;
            border-radius: 4px;
            border-color: #007bff;
        }

        .dataTables_wrapper .dataTables_info {
            color: #6c757d;
            /* text-muted color */
        }

        /* filter bar wrapper sits outside the card */
        .filter-bar { padding: 6px 0; }

        /* pill container */
        .chip { position: relative; }

        /* pill select */
        .chip-select{
        appearance:none;-webkit-appearance:none;-moz-appearance:none;
        background: #ffffff;
        border:1px solid #e5e7eb;
        border-radius:10px;                /* rounded like mock */
        padding:10px 44px 10px 14px;       /* room for chevron */
        min-width:240px;                   /* wide labels e.g. “Subscription: All” */
        font-size:14px; font-weight:500; color:#6b7280;
        line-height:20px; outline:none;
        box-shadow:0 1px 0 rgba(0,0,0,.02);
        transition:border-color .15s ease, box-shadow .15s ease;
        background-image:url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 8l4 4 4-4' stroke='%2399A0A7' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 12px center; background-size:18px;
        }
        .chip-select:focus{
        border-color:#d1d5db;
        box-shadow:0 0 0 3px rgba(99,102,241,.08);
        }

        /* table card look stays clean */
        .card{ border:none; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,.05); }
    </style>
@endpush


@section('content')
    <div class="content-wrapper">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Users Management</h3>
            {{-- <a href="{{ route('staff.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Create New Staff
            </a> --}}
        </div>
        {{-- FILTER BAR (outside table card, matches screenshot) --}}
        <div class="filter-bar d-flex flex-wrap gap-3 align-items-center mb-3">
            <!-- <div class="chip">
                <select id="role-filter" class="chip-select">
                <option value="">Role: All</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->id }}">{{ ucfirst($rol->name) }}</option>
                @endforeach
                </select>
            </div> -->

            <div class="chip">
                <select id="subscription-filter" class="chip-select">
                <option value="">Subscription: All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="chip">
                <select id="bulk-actions-select" class="chip-select">
                <option value="">Bulk Actions</option>
                <option value="delete">Delete Selected</option>
                <!-- <option value="export">Export Selected</option> -->
                </select>
            </div>
            
            <div class="col-md-3">
                <button id="apply-bulk-action" class="btn btn-primary">Apply</button>
            </div>

            <!-- ✅ Reset Link -->
            <div class="chip">
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary" style="border-radius:10px; padding:10px 20px;">
                    <i class="fas fa-undo me-1"></i> Reset Filters
                </a>
            </div>
        </div>

        <div class="card" style="border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);">
            <div class="card-body">


                {{-- Filter Controls
                <div class="row mb-4 filters-container align-items-center">
                    <div class="col-md-3">
                        <select id="role-filter" class="form-control">
                            <option value="">Role: All</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="subscription-filter" class="form-control">
                            <option value="">Subscription: All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="bulk-actions-select" class="form-control">
                            <option value="">Bulk Actions</option>
                            <option value="delete">Delete Selected</option>
                            <!-- <option value="export">Export Selected</option> -->
                        </select>
                    </div>
                    <div class="chip">
                        <button id="apply-bulk-action" class="btn btn-primary" style="border-radius:10px; padding:10px 20px;">
                            Apply
                        </button>
                    </div>
                </div>

                --}}
                {{-- Users Table --}}
                <div class="table-responsive">
                    <table class="table table-hover" id="users-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="master-checkbox" /></th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Subscription</th>
                                <!-- <th>Last Active</th> -->
                                <th>Actions</th>
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
        $(function () {
            // Initialize the DataTable once
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('staff.user_list') }}',
                    type: 'GET',
                    cache: false,
                    data: function (d) {
                        d.role = $('#role-filter').val() || '';
                        d.subscription = $('#subscription-filter').val() || '';
                    }
                },
                order: [[1, 'asc']],
                columns: [
                    { data: 'checkbox', orderable:false, searchable:false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', orderable:false, searchable:false },
                    { data: 'subscription', orderable:false, searchable:false },
                    // { data: 'last_active', searchable:false },
                    { data: 'actions', orderable:false, searchable:false },
                ],
                language: { info: "Showing _START_ to _END_ of _TOTAL_ results" }
            });

            // 🔹 Hook filter changes
            $('#subscription-filter').on('change', function () {
                table.ajax.reload(null, true);
            });

            // 🔹 Select-all checkbox
            $('#master-checkbox').on('click', function() {
                let isChecked = $(this).is(':checked');
                $('#users-table tbody input[type="checkbox"]').prop('checked', isChecked);
            });

            // 🔹 Bulk actions (optional)
            $('#apply-bulk-action').on('click', function () {
                var action = $('#bulk-actions-select').val();
                var selectedIds = [];

                $('#users-table tbody input[type="checkbox"]:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                if (!selectedIds.length) {
                    alert('Please select at least one user.');
                    return;
                }

                if (action === 'delete') {
                    // SweetAlert confirmation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this action!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete them!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("staff.bulk_delete") }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    ids: selectedIds
                                },
                                success: function (res) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: res.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        // Reload the table when user clicks OK
                                        $('#master-checkbox').prop('checked', false);
                                        $('#users-table').DataTable().ajax.reload(null, false);
                                    });
                                },
                                error: function () {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Something went wrong. Please try again.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                }
                else if (action === 'export') {
                    // Trigger export route
                    window.location.href = '{{ route("staff.bulk_export") }}?ids=' + selectedIds.join(',');
                } else {
                    alert('Please select a bulk action.');
                }
            });
        });
    </script>
@endpush
