@extends('layouts.adminApp')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Article Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('article.index') }}">Articles</a></li>
                            <li class="breadcrumb-item active">{{ $article->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Article Content -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $article->title }}</h5>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('article.edit', $article) }}">
                                            <i class="ti-pencil me-2"></i>Edit Article
                                        </a></li>
                                    @if ($article->published_at)
                                        <li><a class="dropdown-item" href="#"
                                                onclick="unpublishArticle({{ $article->id }})">
                                                <i class="ti-eye-off me-2"></i>Unpublish
                                            </a></li>
                                    @else
                                        <li><a class="dropdown-item" href="#"
                                                onclick="publishArticle({{ $article->id }})">
                                                <i class="ti-eye me-2"></i>Publish
                                            </a></li>
                                    @endif
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item text-danger" href="#"
                                            onclick="deleteArticle({{ $article->id }})">
                                            <i class="ti-trash me-2"></i>Delete
                                        </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Article Meta -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Author:</small>
                                    <p class="mb-1"><strong>{{ $article->author->name ?? 'Unknown' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Published:</small>
                                    <p class="mb-1">
                                        @if ($article->published_at)
                                            <span
                                                class="badge bg-success">{{ $article->published_at->format('M d, Y g:i A') }}</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small class="text-muted">Content Type:</small>
                                    <p class="mb-1"><span
                                            class="badge bg-info">{{ ucfirst($article->content_type) }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Target Audience:</small>
                                    <p class="mb-1"><span
                                            class="badge bg-secondary">{{ ucfirst($article->target_audience) }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Article Excerpt -->
                        @if ($article->excerpt)
                            <div class="mb-4">
                                <h6>Excerpt</h6>
                                <div class="alert alert-light">
                                    {{ $article->excerpt }}
                                </div>
                            </div>
                        @endif

                        <!-- Article Content -->
                        <div class="article-content">
                            <h6>Content</h6>
                            <div class="border rounded p-3" style="min-height: 400px;">
                                {!! $article->body !!}
                            </div>
                        </div>

                        <!-- SEO Information -->
                        @if ($article->meta_title || $article->meta_description)
                            <div class="mt-4">
                                <h6>SEO Information</h6>
                                <div class="row">
                                    @if ($article->meta_title)
                                        <div class="col-md-6">
                                            <small class="text-muted">Meta Title:</small>
                                            <p>{{ $article->meta_title }}</p>
                                        </div>
                                    @endif
                                    @if ($article->meta_description)
                                        <div class="col-md-6">
                                            <small class="text-muted">Meta Description:</small>
                                            <p>{{ $article->meta_description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Article Sidebar -->
            <div class="col-lg-4">
                <!-- Publishing Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Publishing</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                @if ($article->published_at)
                                    <span class="badge bg-success fs-6">Published</span>
                                @else
                                    <span class="badge bg-warning fs-6">Draft</span>
                                @endif
                            </div>
                        </div>

                        @if ($article->published_at)
                            <div class="mb-3">
                                <label class="form-label">Published on</label>
                                <p class="mb-0">{{ $article->published_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Created</label>
                            <p class="mb-0">{{ $article->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Modified</label>
                            <p class="mb-0">{{ $article->updated_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <code>{{ $article->slug }}</code>
                        </div>
                    </div>
                </div>

                <!-- Article Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Word Count</label>
                            <p class="mb-0 fs-5"><strong
                                    id="wordCount">{{ str_word_count(strip_tags($article->content)) }}</strong> words</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Character Count</label>
                            <p class="mb-0">{{ strlen(strip_tags($article->content)) }} characters</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reading Time</label>
                            <p class="mb-0">{{ ceil(str_word_count(strip_tags($article->content)) / 200) }} min read</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4>Assign Plans to Article</h4>
                        <form action="{{ route('assign.doc.plans', ['type' => 'article', 'id' => $article->id]) }}"
                            method="POST">
                            @csrf
                            <select name="plans[]" class="form-control select2" multiple>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}"
                                        {{ $article->plans->contains($plan->id) ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary mt-3">Save</button>
                        </form>
                    </div>
                </div>

                {{-- <!-- Preview Link -->
            @if ($article->published_at)
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ url('/article/' . $article->slug) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="ti-external-link me-2"></i>View Article
                    </a>
                </div>
            </div>
            @endif --}}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function publishArticle(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to publish this article!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Publish it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/coach/articles/${id}/publish`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Published!', 'The article has been published.', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error!', 'Error publishing article', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Error publishing article', 'error');
                            });
                    }
                });
            }

            function unpublishArticle(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to unpublish this article!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Unpublish it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/coach/articles/${id}/unpublish`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Unpublished!', 'The article has been unpublished.', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error!', 'Error unpublishing article', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Error unpublishing article', 'error');
                            });
                    }
                });
            }


            function deleteArticle(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone. Do you really want to delete this article?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/coach/article/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Deleted!', 'The article has been deleted.', 'success')
                                        .then(() => window.location.href = "{{ route('article.index') }}");
                                } else {
                                    Swal.fire('Error!', 'Error deleting article', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error!', 'Error deleting article', 'error');
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
