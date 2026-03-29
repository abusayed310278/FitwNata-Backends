@extends('layouts.adminApp')

@section('title', 'Product Details')

@push('styles')
<style>
    .product-images { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
    .product-image { width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
    .info-card { border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .badge-large { padding: 8px 16px; font-size: 14px; }
    .spec-table th { background-color: #f8f9fa; width: 30%; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Product Details</h3>
        </div>
        <div>
            <a href="{{ route('product.edit', $product) }}" class="btn btn-warning me-2">
                <i class="ti-pencil"></i> Edit Product
            </a>
            <a href="{{ route('product.index') }}" class="btn btn-secondary">
                <i class="ti-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="mb-0">{{ $product->name }}</h4>
                    <div>
                        @if($product->is_featured)
                            <span class="badge bg-warning text-dark badge-large me-2">Featured</span>
                        @endif
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }} badge-large">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                @if($product->slug)
                    <p class="text-muted mb-2"><strong>Slug:</strong> {{ $product->slug }}</p>
                @endif

                @if($product->short_description)
                    <div class="mb-3">
                        <h6>Short Description</h6>
                        <p>{{ $product->short_description }}</p>
                    </div>
                @endif

                @if($product->description)
                    <div class="mb-3">
                        <h6>Full Description</h6>
                        <div>{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif

                @if($product->images && count($product->images) > 0)
                    <div class="mb-3">
                        <h6>Product Images</h6>
                        <div class="product-images">
                            @foreach($product->images as $image)
                                <img src="{{ $image }}" alt="Product Image" class="product-image">
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($product->featured_image)
                    <div class="mb-3">
                        <h6>Featured Image</h6>
                        <div class="product-images">
                                <img src="{{ asset($product->featured_image) }}" alt="Product Image" class="product-image">
                        </div>
                    </div>
                @endif

                @if($product->specifications)
                    <div class="mb-3">
                        <h6>Specifications</h6>
                        <table class="table table-sm spec-table">
                            @foreach($product->specifications as $key => $value)
                                <tr>
                                    <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <!-- Pricing Information -->
            <div class="info-card">
                <h5 class="mb-3">Pricing & Stock</h5>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Price:</strong></td>
                        <td>${{ number_format($product->price, 2) }}</td>
                    </tr>
                    @if($product->sale_price)
                        <tr>
                            <td><strong>Sale Price:</strong></td>
                            <td class="text-danger"><strong>${{ number_format($product->sale_price, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Discount:</strong></td>
                            <td class="text-success">
                                ${{ number_format($product->price - $product->sale_price, 2) }}
                                ({{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 1) }}% off)
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>SKU:</strong></td>
                        <td>{{ $product->sku }}</td>
                    </tr>
                    <tr>
                        <td><strong>Stock:</strong></td>
                        <td>
                            {{ $product->stock_quantity }}
                            @if($product->stock_quantity <= 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($product->stock_quantity <= 10)
                                <span class="badge bg-warning">Low Stock</span>
                            @else
                                <span class="badge bg-success">In Stock</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <button class="btn btn-primary btn-sm w-100" onclick="updateStock({{ $product->id }})">
                    <i class="ti-package"></i> Update Stock
                </button>
            </div>

            <!-- Category Information -->
            <div class="info-card">
                <h5 class="mb-3">Category</h5>
                @if($product->category)
                    <p><strong>{{ $product->category->name }}</strong></p>
                    @if($product->category->description)
                        <p class="text-muted small">{{ $product->category->description }}</p>
                    @endif
                @else
                    <p class="text-muted">No category assigned</p>
                @endif
            </div>

            <!-- SEO Information -->
            @if($product->meta_title || $product->meta_description)
                <div class="info-card">
                    <h5 class="mb-3">SEO Information</h5>
                    @if($product->meta_title)
                        <div class="mb-2">
                            <strong>Meta Title:</strong>
                            <p class="mb-0">{{ $product->meta_title }}</p>
                        </div>
                    @endif
                    @if($product->meta_description)
                        <div>
                            <strong>Meta Description:</strong>
                            <p class="mb-0">{{ $product->meta_description }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Product Statistics -->
            <div class="info-card">
                <h5 class="mb-3">Statistics</h5>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Average Rating:</strong></td>
                        <td>
                            @if($product->reviews_count > 0)
                                {{ number_format($product->average_rating, 1) }}/5
                                <small class="text-muted">({{ $product->reviews_count }} reviews)</small>
                            @else
                                <span class="text-muted">No reviews yet</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $product->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $product->updated_at->format('M d, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStockModalLabel">Update Stock for: {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStockForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_stock_quantity" class="form-label">Current Stock: <strong>{{ $product->stock_quantity }}</strong></label>
                        <input type="number" class="form-control" id="new_stock_quantity" name="stock_quantity"
                               value="{{ $product->stock_quantity }}" min="0" required>
                        <small class="text-muted">Enter the new stock quantity</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStock(productId) {
    $('#updateStockModal').modal('show');
}

$(document).ready(function() {
    $('#updateStockForm').on('submit', function(e) {
        e.preventDefault();

        const productId = {{ $product->id }};
        const stockQuantity = $('#new_stock_quantity').val();

        $.ajax({
            url: `/admin/product/${productId}/update-stock`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                stock_quantity: stockQuantity
            },
            success: function(response) {
                $('#updateStockModal').modal('hide');

                if (response.success) {
                    // Show success message
                    $('body').prepend(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `);

                    // Reload page to show updated stock
                    setTimeout(() => window.location.reload(), 1500);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = 'An error occurred while updating stock.';

                if (errors && errors.stock_quantity) {
                    errorMessage = errors.stock_quantity[0];
                }

                $('body').prepend(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
            }
        });
    });
});
</script>
@endpush
