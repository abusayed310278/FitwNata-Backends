@extends('layouts.adminApp')

@section('title', 'Product Categories')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .table th, .table td { vertical-align: middle; }
    .badge { padding: 0.5em 0.9em; border-radius: 20px; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Product Categories</h3>

        </div>
        <div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="ti-plus"></i> Add New Category
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\ProductCategory::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\ProductCategory::active()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Root Categories</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\ProductCategory::root()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-layers fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Product::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="ti-package fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="categories-table">
                    <thead>
                        <tr>
                              <th>Actions</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Products Count</th>
                            <th>Sort Order</th>

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
    $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('categories.list') }}',
        columns: [
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'is_active', name: 'is_active', orderable: false, searchable: false },
            { data: 'products_count', name: 'products_count' },
            { data: 'sort_order', name: 'sort_order' }
        ],
        order: [[3, 'asc']]
    });
});

// Function to show alert messages
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Remove existing alerts
    $('.alert').remove();

    // Add new alert at the top of content
    $('.content-wrapper').prepend(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush
