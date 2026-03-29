@extends('layouts.adminApp')

@section('title', 'Content Manager')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Content Manager</h1>
            <p class="mb-0">Create and manage workout videos, nutrition tips, and blog posts</p>
        </div>
        <a href="{{ route('coach.content.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Content
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Content</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Published</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Drafts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['drafts'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['this_month'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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

    <!-- Content Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">My Content</h6>
            <div class="d-flex align-items-center">
                <select id="status-filter" class="form-select form-select-sm me-2">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="scheduled">Scheduled</option>
                </select>
                <select id="type-filter" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="workout_video">Workout Videos</option>
                    <option value="nutrition_tip">Nutrition Tips</option>
                    <option value="blog_post">Blog Posts</option>
                    <option value="recipe">Recipes</option>
                    <option value="motivation">Motivational</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="content-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Target Audience</th>
                            <th>Published Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Media Upload Modal -->
<div class="modal fade" id="mediaUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mediaUploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="media" class="form-label">Select Media File</label>
                        <input type="file" class="form-control" id="media" name="media"
                               accept="image/*,video/*" required>
                        <div class="form-text">
                            Supported formats: JPG, PNG, WebP (images), MP4, MOV, AVI (videos)<br>
                            Maximum size: 10MB
                        </div>
                    </div>
                    <div id="upload-progress" class="progress mb-3" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="upload-result" class="alert" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#content-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('coach.content.data') }}",
            data: function(d) {
                d.status = $('#status-filter').val();
                d.type = $('#type-filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'content_type', name: 'content_type' },
            { data: 'target_audience', name: 'target_audience' },
            { data: 'published_at', name: 'published_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']]
    });

    // Filter functionality
    $('#status-filter, #type-filter').on('change', function() {
        table.draw();
    });
});

function deleteContent(contentId) {
    if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
        $.ajax({
            url: `/coach/content/${contentId}`,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            }
        })
        .done(function(response) {
            if (response.success) {
                $('#content-table').DataTable().ajax.reload();
                alert(response.message);
            } else {
                alert(response.message);
            }
        })
        .fail(function() {
            alert('Error deleting content');
        });
    }
}

function showMediaUpload() {
    $('#mediaUploadModal').modal('show');
}

// Handle media upload
$('#mediaUploadForm').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}');

    $('#upload-progress').show();
    $('#upload-result').hide();

    $.ajax({
        url: "{{ route('coach.content.upload-media') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    const percentComplete = evt.loaded / evt.total;
                    const percent = Math.round(percentComplete * 100);
                    $('.progress-bar').css('width', percent + '%');
                }
            }, false);
            return xhr;
        }
    })
    .done(function(response) {
        $('#upload-progress').hide();
        if (response.success) {
            $('#upload-result')
                .removeClass('alert-danger')
                .addClass('alert-success')
                .html(`
                    <strong>Upload successful!</strong><br>
                    URL: <a href="${response.url}" target="_blank">${response.url}</a><br>
                    <small>You can copy this URL to use in your content.</small>
                `)
                .show();
        } else {
            $('#upload-result')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .text('Upload failed: ' + (response.message || 'Unknown error'))
                .show();
        }
    })
    .fail(function() {
        $('#upload-progress').hide();
        $('#upload-result')
            .removeClass('alert-success')
            .addClass('alert-danger')
            .text('Upload failed: Network error')
            .show();
    });
});

// Reset modal when closed
$('#mediaUploadModal').on('hidden.bs.modal', function() {
    $('#mediaUploadForm')[0].reset();
    $('#upload-progress').hide();
    $('#upload-result').hide();
    $('.progress-bar').css('width', '0%');
});
</script>
@endpush
