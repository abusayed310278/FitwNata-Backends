@extends('layouts.adminApp')

@section('title', 'Edit Category')

@push('styles')
<style>
    .current-image {
        max-width: 150px;
        height: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Edit Category</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active">Edit: {{ $category->name }}</li>
            </ol>
        </nav>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $category->slug) }}">
                                    <small class="text-muted">Leave empty to auto-generate from name</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">Parent Category</label>
                                    <select class="form-control" id="parent_id" name="parent_id">
                                        <option value="">None (Root Category)</option>
                                        @foreach($categories as $parentCategory)
                                            <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                                {{ $parentCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{$category->image }}" alt="Current Image" class="current-image">
                                </div>
                            @else
                                <p class="text-muted">No image uploaded</p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">New Category Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Upload a new image to replace the current one (max 2MB)</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active Category</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Category Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Slug:</strong></td>
                            <td>{{ $category->slug }}</td>
                        </tr>
                        <tr>
                            <td><strong>Products Count:</strong></td>
                            <td>{{ $category->products()->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Child Categories:</strong></td>
                            <td>{{ $category->children()->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $category->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Last Updated:</strong></td>
                            <td>{{ $category->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($category->products()->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Associated Products</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($category->products()->limit(5)->get() as $product)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $product->name }}</strong><br>
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">${{ number_format($product->price, 2) }}</span>
                                </div>
                            @endforeach
                            @if($category->products()->count() > 5)
                                <div class="text-center mt-2">
                                    <small class="text-muted">and {{ $category->products()->count() - 5 }} more...</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($category->children()->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Sub-categories</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($category->children as $child)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $child->name }}</strong>
                                        @if(!$child->is_active)
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </div>
                                    <span class="badge bg-info rounded-pill">{{ $child->products()->count() }} products</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate slug from name when editing
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slug').value = slug;
});
</script>
@endpush
