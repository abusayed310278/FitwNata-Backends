@extends('layouts.adminApp')

@section('title', 'Articles Management')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Articles Management</h3>
                <h6 class="font-weight-normal mb-0">Manage all content articles for the fitness platform</h6>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-6 col-xl-3 mb-4 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $totalArticles }}</h3>
                        </div>
                        <h6 class="text-muted font-weight-normal">Total Articles</h6>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-success">
                            <span class="ti-files text-success"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $publishedArticles }}</h3>
                        </div>
                        <h6 class="text-muted font-weight-normal">Published</h6>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-success">
                            <span class="ti-eye text-success"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ $draftArticles }}</h3>
                        </div>
                        <h6 class="text-muted font-weight-normal">Drafts</h6>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-warning">
                            <span class="ti-write text-warning"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3 mb-4 stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{ number_format(($publishedArticles / max($totalArticles, 1)) * 100, 1) }}%</h3>
                        </div>
                        <h6 class="text-muted font-weight-normal">Publish Rate</h6>
                    </div>
                    <div class="col-3">
                        <div class="icon icon-box-info">
                            <span class="ti-stats-up text-info"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Articles Table -->
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">All Articles</h4>
                <div>
                    <button class="btn btn-outline-secondary btn-sm me-2" id="bulkActions" style="display: none;">
                        <i class="ti-layers"></i> Bulk Actions
                    </button>
                    <a href="{{ route('article.create') }}" class="btn btn-primary btn-sm">
                        <i class="ti-plus"></i> Add New Article
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="articlesTable">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Content Preview</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Select an action to perform on <span id="selectedCount">0</span> selected articles:</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-success" onclick="executeBulkAction('publish')">
                        <i class="ti-eye"></i> Publish Selected
                    </button>
                    <button class="btn btn-warning" onclick="executeBulkAction('unpublish')">
                        <i class="ti-eye-off"></i> Unpublish Selected
                    </button>
                    <button class="btn btn-danger" onclick="executeBulkAction('delete')">
                        <i class="ti-trash"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

    <script>
        let table;
        let selectedArticles = [];

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#articlesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("article.data") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'author_id', name: 'author.name'},
                    {data: 'published_at', name: 'published_at'},
                    {data: 'body', name: 'body', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at'}
                ],
                order: [[5, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf'
                ]
            });

            // Select All Checkbox
            $('#selectAll').on('change', function() {
                $('.article-checkbox').prop('checked', this.checked);
                updateSelectedArticles();
            });

            // Individual Checkbox
            $(document).on('change', '.article-checkbox', function() {
                updateSelectedArticles();
            });

            // Bulk Actions Button
            $('#bulkActions').on('click', function() {
                $('#selectedCount').text(selectedArticles.length);
                $('#bulkModal').modal('show');
            });
        });

        function updateSelectedArticles() {
            selectedArticles = [];
            $('.article-checkbox:checked').each(function() {
                selectedArticles.push($(this).val());
            });

            if (selectedArticles.length > 0) {
                $('#bulkActions').show();
            } else {
                $('#bulkActions').hide();
            }

            // Update select all checkbox
            const totalCheckboxes = $('.article-checkbox').length;
            const checkedCheckboxes = $('.article-checkbox:checked').length;
            $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
            $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
        }

        function togglePublish(articleId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("article.toggle-publish", ":id") }}'.replace(':id', articleId),
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error('Action failed. Please try again.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        }

        function deleteArticle(articleId) {
            if (confirm('Are you sure you want to delete this article? This action cannot be undone.')) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("article.destroy", ":id") }}'.replace(':id', articleId),
                    type: 'DELETE',
                    success: function(response) {
                        toastr.success('Article deleted successfully');
                        table.ajax.reload(null, false);
                    },
                    error: function() {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        }

        function executeBulkAction(action) {
            if (selectedArticles.length === 0) {
                toastr.warning('Please select at least one article.');
                return;
            }

            const confirmMessage = action === 'delete'
                ? 'Are you sure you want to delete the selected articles? This action cannot be undone.'
                : `Are you sure you want to ${action} the selected articles?`;

            if (confirm(confirmMessage)) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("article.bulk") }}',
                    type: 'POST',
                    data: {
                        action: action,
                        articles: selectedArticles
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                            $('#bulkModal').modal('hide');
                            selectedArticles = [];
                            $('#bulkActions').hide();
                            $('#selectAll').prop('checked', false);
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        }
    </script>
@endpush
