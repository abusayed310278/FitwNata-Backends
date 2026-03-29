@extends('layouts.adminApp')

@section('title', 'Category Details')

@push('styles')
    <style>
        .category-image {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px;
        }

        .info-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .badge-large {
            padding: 8px 16px;
            font-size: 14px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="page-title">Category Details</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning me-2">
                    <i class="ti-pencil"></i> Edit Category
                </a>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                    <i class="ti-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="mb-0">{{ $category->name }}</h4>
                        <div>
                            <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }} badge-large">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    @if ($category->slug)
                        <p class="text-muted mb-2"><strong>Slug:</strong> /categories/{{ $category->slug }}</p>
                    @endif

                    @if ($category->parent)
                        <p class="text-muted mb-2">
                            <strong>Parent Category:</strong>
                            <a href="{{ route('categories.show', $category->parent) }}" class="text-decoration-none">
                                {{ $category->parent->name }}
                            </a>
                        </p>
                    @endif

                    @if ($category->description)
                        <div class="mb-3">
                            <h6>Description</h6>
                            <p>{{ $category->description }}</p>
                        </div>
                    @endif

                    @if ($category->image)
                        <div class="mb-3">
                            <h6>Category Image</h6>
                            <img src="{{ $category->image }}" alt="{{ $category->name }}" class="category-image">
                        </div>
                    @endif
                </div>

                @if ($category->children->count() > 0)
                    <div class="info-card">
                        <h5 class="mb-3">Sub-categories ({{ $category->children->count() }})</h5>
                        <div class="row">
                            @foreach ($category->children as $child)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="card-title mb-1">
                                                        <a href="{{ route('categories.show', $child) }}"
                                                            class="text-decoration-none">
                                                            {{ $child->name }}
                                                        </a>
                                                    </h6>
                                                    @if ($child->description)
                                                        <p class="card-text small text-muted">
                                                            {{ Str::limit($child->description, 100) }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <span
                                                        class="badge {{ $child->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $child->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">{{ $child->products->count() }}
                                                        products</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($category->products->count() > 0)
                    <div class="info-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Products in this Category ({{ $category->products->count() }})</h5>
                            <a href="{{ route('product.index') }}?category={{ $category->id }}"
                                class="btn btn-sm btn-outline-primary">
                                View All Products
                            </a>
                        </div>

                        <div class="product-grid">
                            @foreach ($category->products->take(6) as $product)
                                <div class="product-card">
                                    <div class="d-flex align-items-start">
                                        @if ($product->images && count($product->images) > 0)
                                            <img src="{{ asset('storage/' . $product->images[0]) }}"
                                                alt="{{ $product->name }}" class="product-image me-3">
                                        @else
                                            <div
                                                class="product-image me-3 bg-light d-flex align-items-center justify-content-center">
                                                <i class="ti-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <a href="{{ route('product.show', $product) }}"
                                                    class="text-decoration-none">
                                                    {{ $product->name }}
                                                </a>
                                            </h6>
                                            <p class="mb-1 small text-muted">SKU: {{ $product->sku }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong
                                                    class="text-primary">${{ number_format($product->price, 2) }}</strong>
                                                @if ($product->stock_quantity <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @elseif($product->stock_quantity <= 10)
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">In Stock</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($category->products->count() > 6)
                            <div class="text-center mt-3">
                                <a href="{{ route('product.index') }}?category={{ $category->id }}"
                                    class="btn btn-outline-primary">
                                    View {{ $category->products->count() - 6 }} More Products
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="info-card text-center">
                        <div class="py-4">
                            <i class="ti-package" style="font-size: 48px; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">No Products in this Category</h5>
                            <p class="text-muted">This category doesn't have any products yet.</p>
                            <a href="{{ route('product.create') }}?category={{ $category->id }}" class="btn btn-primary">
                                <i class="ti-plus"></i> Add First Product
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <!-- Category Statistics -->
                <div class="info-card">
                    <h5 class="mb-3">Category Statistics</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Products:</strong></td>
                            <td>{{ $category->products->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Active Products:</strong></td>
                            <td>{{ $category->products->where('is_active', true)->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Sub-categories:</strong></td>
                            <td>{{ $category->children->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Sort Order:</strong></td>
                            <td>{{ $category->sort_order ?? 'Not set' }}</td>
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

                <!-- Product Summary -->
                @if ($category->products->count() > 0)
                    <div class="info-card">
                        <h5 class="mb-3">Product Summary</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Total Value:</strong></td>
                                <td>${{ number_format($category->products->sum('price'), 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Average Price:</strong></td>
                                <td>${{ number_format($category->products->avg('price'), 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Stock:</strong></td>
                                <td>{{ $category->products->sum('stock_quantity') }} units</td>
                            </tr>
                            <tr>
                                <td><strong>Featured Products:</strong></td>
                                <td>{{ $category->products->where('is_featured', true)->count() }}</td>
                            </tr>
                            <tr>
                                <td><strong>Out of Stock:</strong></td>
                                <td>{{ $category->products->where('stock_quantity', 0)->count() }}</td>
                            </tr>
                        </table>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="info-card">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning">
                            <i class="ti-pencil"></i> Edit Category
                        </a>
                        <a href="{{ route('product.create') }}?category={{ $category->id }}"
                            class="btn btn-outline-primary">
                            <i class="ti-plus"></i> Add Product to Category
                        </a>
                        @if ($category->products->count() == 0 && $category->children->count() == 0)
                            <button class="btn btn-outline-danger" onclick="deleteCategory({{ $category->id }})">
                                <i class="ti-trash"></i> Delete Category
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function deleteCategory(categoryId) {
            showConfirm("Are you sure?", "This action cannot be undone.")
                .then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/categories/${categoryId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': getCsrfToken(),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Deleted!", data.message, "success")
                                        .then(() => {
                                            window.location.href = "{{ route('categories.index') }}";
                                        });
                                } else {
                                    Swal.fire("Error", data.message || "Something went wrong.", "error");
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire("Error", "An error occurred while deleting the category.", "error");
                            });
                    }
                });
        }
    </script>
@endpush
