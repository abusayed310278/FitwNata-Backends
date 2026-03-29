@extends('layouts.adminApp')

@section('title', 'Create Product')

@push('styles')
<style>
    .preview-images { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .preview-item { position: relative; display: inline-block; }
    .preview-item img { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .preview-item .remove-btn { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; }
    .specification-row { display: flex; gap: 10px; margin-bottom: 10px; }
    .specification-key, .specification-value { flex: 1; }
    .remove-specification { align-self: center; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Create Product</h3>
        
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

    <div class="card">
        <div class="card-body">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (Optional)</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}">
                            <small class="text-muted">Leave empty to auto-generate from name</small>
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="3" maxlength="500">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specifications</label>
                            <div id="specifications-container">
                                <div class="specification-row">
                                    <div class="specification-key">
                                        <input type="text" class="form-control" name="specifications_key[]" placeholder="Key">
                                    </div>
                                    <div class="specification-value">
                                        <input type="text" class="form-control" name="specifications_value[]" placeholder="Value">
                                    </div>
                                    <div class="remove-specification">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSpecification(this)">Remove</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="addSpecification()">Add Specification</button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $request->category == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price *</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Sale Price</label>
                                    <input type="number" class="form-control" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku') }}" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateSKU()">Generate</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                            <small class="text-muted">This image will be used as the main product image</small>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Product Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div id="preview-images" class="preview-images"></div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured Product</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Generate slug from name
document.getElementById('name').addEventListener('input', function() {
    if (!document.getElementById('slug').value) {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        document.getElementById('slug').value = slug;
    }
});

// Generate SKU
function generateSKU() {
    const name = document.getElementById('name').value;
    if (name) {
        const prefix = name.replace(/[^A-Za-z0-9]/g, '').substring(0, 3).toUpperCase();
        const suffix = Math.floor(Math.random() * 9000) + 1000;
        document.getElementById('sku').value = prefix + suffix;
    } else {
        const suffix = Math.floor(Math.random() * 9000) + 1000;
        document.getElementById('sku').value = 'PRD' + suffix;
    }
}

// Image preview
document.getElementById('images').addEventListener('change', function() {
    const previewContainer = document.getElementById('preview-images');
    previewContainer.innerHTML = '';

    Array.from(this.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-btn" onclick="removeImage(${index})">&times;</button>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeImage(index) {
    const input = document.getElementById('images');
    const dt = new DataTransfer();
    const files = Array.from(input.files);

    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));

    input.files = dt.files;
    input.dispatchEvent(new Event('change'));
}

function addSpecification() {
    const container = document.getElementById('specifications-container');
    const row = document.createElement('div');
    row.className = 'specification-row';
    row.innerHTML = `
        <div class="specification-key">
            <input type="text" class="form-control" name="specifications_key[]" placeholder="Key">
        </div>
        <div class="specification-value">
            <input type="text" class="form-control" name="specifications_value[]" placeholder="Value">
        </div>
        <div class="remove-specification">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeSpecification(this)">Remove</button>
        </div>
    `;
    container.appendChild(row);
}

function removeSpecification(button) {
    const row = button.closest('.specification-row');
    row.remove();
}
</script>
@endpush
