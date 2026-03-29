@extends('layouts.adminApp')

@section('title', 'Edit Product')

@push('styles')
<style>
    .preview-images { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .preview-item { position: relative; display: inline-block; }
    .preview-item img { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .preview-item .remove-btn { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; }
    .existing-images { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    .existing-image { position: relative; display: inline-block; }
    .existing-image img { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .existing-image .remove-btn { position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; }
    .specification-row { display: flex; gap: 10px; margin-bottom: 10px; }
    .specification-key, .specification-value { flex: 1; }
    .remove-specification { align-self: center; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">Edit Product</h3>
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
            <form action="{{ route('product.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                        </div>

                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="3" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Full Description</label>
                            <textarea class="form-control" id="description" name="description" rows="6">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Specifications</label>
                            <div id="specifications-container">
                                @forelse($product->productSpecifications as $spec)
                                    <div class="specification-row">
                                        <div class="specification-key">
                                            <input type="text" class="form-control" name="specifications_key[]" value="{{ $spec->key }}" placeholder="Key">
                                        </div>
                                        <div class="specification-value">
                                            <input type="text" class="form-control" name="specifications_value[]" value="{{ $spec->value }}" placeholder="Value">
                                        </div>
                                        <div class="remove-specification">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeSpecification(this)">Remove</button>
                                        </div>
                                    </div>
                                @empty
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
                                @endforelse
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price *</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Sale Price</label>
                                    <input type="number" class="form-control" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            @if($product->featured_image)
                                <div class="mb-2">
                                    <img src="{{ $product->featured_image }}" alt="Featured Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                            <small class="text-muted">This image will be used as the main product image</small>
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label">Current Images</label>
                            @if($product->images && count($product->images) > 0)
                                <div class="existing-images" id="existing-images-container">
                                    @foreach($product->images as $image)
                                        <div class="existing-image">
                                            <img src="{{ asset('storage/' . $image) }}" alt="Product Image">
                                            <button type="button" class="remove-btn" onclick="removeExistingImage(this, '{{ $image }}')">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No images uploaded</p>
                            @endif
                        </div> --}}

                        {{-- <div class="mb-3">
                            <label for="images" class="form-label">Add New Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div id="preview-images" class="preview-images"></div>
                            <small class="text-muted">New images will be added to existing ones</small>
                        </div> --}}

                        <div class="mb-3">
                            <label class="form-label">Existing Images</label>
                            @if($product->images && count($product->images) > 0)
                                <div class="existing-images" id="existing-images-container">
                                    @foreach($product->images as $image)
                                        <div class="existing-image">
                                            <img src="{{ $image }}" alt="Product Image">
                                            <button type="button" class="remove-btn" onclick="removeExistingImage(this, '{{ $image }}')">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No images uploaded</p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Add New Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <div id="preview-images" class="preview-images"></div>
                            <small class="text-muted">You can add multiple images. Click “×” to remove before uploading.</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured Product</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="255">
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="500">{{ old('meta_description', $product->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('product.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Hidden input to track images to remove
let imagesToRemove = [];

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

// function removeExistingImage(button, imagePath) {
//     // Add image path to remove list
//     imagesToRemove.push(imagePath);

//     // Create hidden input for each image to remove
//     const hiddenInput = document.createElement('input');
//     hiddenInput.type = 'hidden';
//     hiddenInput.name = 'remove_images[]';
//     hiddenInput.value = imagePath;
//     document.querySelector('form').appendChild(hiddenInput);

//     // Remove the image element from DOM
//     button.closest('.existing-image').remove();
// }

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

function removeExistingImage(button, imagePath) {
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'remove_images[]';
    hiddenInput.value = imagePath;
    document.querySelector('form').appendChild(hiddenInput);

    button.closest('.existing-image').remove();
}
</script>
@endpush
