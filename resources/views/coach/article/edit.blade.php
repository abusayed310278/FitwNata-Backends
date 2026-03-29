@extends('layouts.adminApp')

@section('title', 'Edit Article')

@push('styles')
    <!-- Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Edit Article</h3>
                    <h6 class="font-weight-normal mb-0">Update article: {{ $article->title }}</h6>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="justify-content-end d-flex">
                        <a href="{{ route('article.show', $article) }}" class="btn btn-outline-info me-2">
                            <i class="ti-eye"></i> Preview
                        </a>
                        <a href="{{ route('article.index') }}" class="btn btn-outline-secondary">
                            <i class="ti-arrow-left"></i> Back to Articles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Article Details</h4>
                    <div class="badge bg-{{ $article->published_at ? 'success' : 'warning' }}">
                        {{ $article->published_at ? 'Published' : 'Draft' }}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('article.update', $article) }}" method="POST" id="articleForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Article Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $article->title) }}"
                                        placeholder="Enter article title" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Current slug: <code>{{ $article->slug }}</code>
                                        <span class="text-info">(Will be updated if title changes)</span>
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label for="body" class="form-label">Article Content <span
                                            class="text-danger">*</span></label>
                                    <div id="editor" style="height: 400px;">
                                        {!! old('body', $article->body) !!}
                                    </div>
                                    <textarea name="body" id="body" style="display: none;" required>{{ old('body', $article->body) }}</textarea>
                                    @error('body')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                @include('coach.components.selectedTags')

                            </div>

                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Publishing Options</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="author_id" class="form-label">Author <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('author_id') is-invalid @enderror"
                                                id="author_id" name="author_id" required>
                                                <option value="">Select Author</option>
                                                @foreach ($authors as $author)
                                                    <option value="{{ $author->id }}"
                                                        {{ old('author_id', $article->author_id) == $author->id ? 'selected' : '' }}>
                                                        {{ $author->name }} ({{ $author->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('author_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="published_at" class="form-label">Publish Date *</label>
                                            <input type="datetime-local"
                                                class="form-control @error('published_at') is-invalid @enderror"
                                                id="published_at" name="published_at"
                                                value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}">
                                            @error('published_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            {{-- <small class="form-text text-muted">Leave empty to save as draft</small> --}}
                                        </div>

                                        {{-- <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="publishNow"
                                                   {{ $article->published_at ? 'checked' : '' }}>
                                            <label class="form-check-label" for="publishNow">
                                                Published
                                            </label>
                                        </div>
                                    </div> --}}

                                        <hr>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti-check"></i> Update Article
                                            </button>
                                            {{-- <button type="button" class="btn btn-outline-secondary" id="saveDraft">
                                            <i class="ti-save"></i> Save as Draft
                                        </button> --}}
                                            <a href="{{ route('article.index') }}" class="btn btn-outline-danger">
                                                <i class="ti-close"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Article Statistics -->
                                <div class="card border mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Article Info</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <strong>{{ $article->created_at->format('M d, Y') }}</strong>
                                                </div>
                                                <small class="text-muted">Created</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-2">
                                                    <strong>{{ $article->updated_at->format('M d, Y') }}</strong>
                                                </div>
                                                <small class="text-muted">Updated</small>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <div class="mb-2">
                                                <strong id="wordCount">0</strong>
                                            </div>
                                            <small class="text-muted">Word Count</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEO Preview -->
                                {{-- <div class="card border mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">SEO Preview</h6>
                                </div>
                                <div class="card-body">
                                    <div class="seo-preview">
                                        <div class="seo-title text-primary" id="seoTitle">{{ $article->title }}</div>
                                        <div class="seo-url text-success small" id="seoUrl">{{ url('/articles/') }}/{{ $article->slug }}</div>
                                        <div class="seo-description text-muted small" id="seoDescription">Article preview...</div>
                                    </div>
                                </div>
                            </div> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Rich Text Editor -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        let quill;

        $(document).ready(function() {
            // Initialize Quill editor
            quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Write your article content here...',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        ['blockquote', 'code-block'],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            // Update hidden textarea when content changes
            quill.on('text-change', function() {
                $('#body').val(quill.root.innerHTML);
                updateSEOPreview();
                updateWordCount();
            });

            // Title change handler
            $('#title').on('input', function() {
                updateSEOPreview();
            });

            // Publish checkbox
            $('#publishNow').on('change', function() {
                if (this.checked) {
                    if (!$('#published_at').val()) {
                        const now = new Date();
                        const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
                            .toISOString().slice(0, 16);
                        $('#published_at').val(localDateTime);
                    }
                } else {
                    $('#published_at').val('');
                }
            });

            // Save as draft
            $('#saveDraft').on('click', function() {
                $('#published_at').val('');
                $('#publishNow').prop('checked', false);
                $('#articleForm').submit();
            });

            // Form submission
            $('#articleForm').on('submit', function() {
                // Ensure the hidden textarea is updated
                $('#body').val(quill.root.innerHTML);
            });

            // Initial updates
            updateSEOPreview();
            updateWordCount();
        });

        function updateSEOPreview() {
            const title = $('#title').val() || 'Article Title';
            const content = quill.getText() || '';
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');

            $('#seoTitle').text(title);
            $('#seoUrl').text(`{{ url('/articles/') }}/${slug}`);

            // Create description from content (first 160 characters)
            const description = content.substring(0, 160) + (content.length > 160 ? '...' : '');
            $('#seoDescription').text(description || 'Article preview will appear here...');
        }

        function updateWordCount() {
            const text = quill.getText();
            const wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;
            $('#wordCount').text(wordCount);
        }
    </script>
@endpush
