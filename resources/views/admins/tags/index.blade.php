@extends('layouts.adminApp')

@section('title', 'Product Categories')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        .badge {
            padding: 0.5em 0.9em;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="page-title">Tags</h3>

            </div>
            <div>
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tagModal"
                    onclick="openCreateModal()"><i class="ti ti-plus"></i> Add Tag</button>
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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tags-table">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>Title</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tagModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="tagForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add Tag</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tag Name</label>
                            <input type="text" name="name" id="tagName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $('#tags-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('tags.list') }}',
                columns: [{
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').innerText = "Add Tag";
            document.getElementById('tagForm').action = "{{ route('tags.store') }}";
            document.getElementById('formMethod').value = "POST";
            document.getElementById('tagName').value = "";
        }

        $(document).on('click', '.edit-tag-btn', function() {
            let tagId = $(this).data('id');
            let tagName = $(this).data('name');

            openEditModal(tagId, tagName);
        });

        // Delete button click
        $(document).on('click', '.delete-tag-btn', function() {
            let tagId = $(this).data('id');
            deleteTag(tagId);
        });

        function openEditModal(id, name) {
            document.getElementById('modalTitle').innerText = "Edit Tag";
            document.getElementById('tagForm').action = "/admin/tags/" + id;
            document.getElementById('formMethod').value = "PUT";
            document.getElementById('tagName').value = name;

            $('#tagModal').modal('show');
        }

        function deleteTag(tagId) {
            Swal.fire({
                title: "Are you sure?",
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/tags/${tagId}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    "content"),
                                "Content-Type": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Deleted!", data.message, "success");
                                $('#tags-table').DataTable().ajax.reload();
                            } else {
                                Swal.fire("Error", data.message || "Something went wrong.", "error");
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire("Error", "An error occurred while deleting the tag.", "error");
                        });
                }
            });
        }
    </script>
@endpush
