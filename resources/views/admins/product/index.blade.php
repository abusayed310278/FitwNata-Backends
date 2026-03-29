@extends('layouts.adminApp')

@section('title', 'Product Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .table th, .table td { vertical-align: middle; }
    .badge { padding: 0.5em 0.9em; border-radius: 20px; font-weight: 500; }

    .rating-stars {
        color: #fbb034; /* gold */
        font-size: 1.1rem;
        line-height: 1;
    }
    .table td .rating-summary {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .rating-summary small {
        color: #6c757d;
    }
    .review-card {
        transition: all 0.2s ease-in-out;
    }
    .review-card:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }
    .review-card img {
        object-fit: cover;
    }
    #reviewsContainer::-webkit-scrollbar {
        width: 6px;
    }
    #reviewsContainer::-webkit-scrollbar-thumb {
        background-color: #d0d0d0;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class=" ">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="page-title">Product Management</h3>

        </div>
        <div>
            <a href="{{ route('product.create') }}" class="btn btn-primary">
                <i class="ti-plus"></i> Add New Product
            </a>
        </div>
    </div>

    

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="products-table">
                    <thead>
                        <tr>
                            <th>Actions</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock Status</th>
                            <th>Category</th>
                            <th>Rating & Reviews</th>

                        </tr>
                    </thead>
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
                    <h5 class="modal-title" id="updateStockModalLabel">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStockForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product:</label>
                            <input type="text" class="form-control" id="product_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="current_stock" class="form-label">Current Stock:</label>
                            <input type="text" class="form-control" id="current_stock" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="new_stock_quantity" class="form-label">New Stock Quantity *</label>
                            <input type="number" class="form-control" id="new_stock_quantity" name="stock_quantity" min="0" required>
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

    <!-- View Reviews Modal -->
    <div class="modal fade" id="viewReviewsModal" tabindex="-1" aria-labelledby="viewReviewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold" id="viewReviewsModalLabel">
                        Product Reviews
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <div id="reviewsContainer">
                        <div class="text-center text-muted py-4" id="reviewsLoader">
                            <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem"></div>
                            <p class="mt-2 mb-0">Loading reviews...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
let currentProductId = null;
let dataTable;

$(function() {
    dataTable = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,  
        ajax: '{{ route('product.list') }}',
        columns: [
            // { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'actions', name: 'actions',searchable: false },
            { data: 'name', name: 'name' ,orderable: false},
            { data: 'price', name: 'price',orderable: false },
            { data: 'stock_status', name: 'stock_status',searchable: false },
            { data: 'category', name: 'category',searchable: false },
            { data: 'rating', name: 'rating', searchable: false },

        ]
    });
});

// Function to update stock - called from action buttons
function updateStock(productId) {
    currentProductId = productId;

    // Get product data from the current row
    const row = dataTable.row(function(idx, data, node) {
        return data.id == productId;
    }).data();

    if (row) {
        $('#product_name').val($(row.name).text() || 'Loading...');
        $('#current_stock').val(row.stock_quantity || 'Loading...');
        $('#new_stock_quantity').val(row.stock_quantity || 0);
    }

    $('#updateStockModal').modal('show');
}

// Handle stock update form submission
$('#updateStockForm').on('submit', function(e) {
    e.preventDefault();

    if (!currentProductId) {
        alert('Product ID not found');
        return;
    }

    const stockQuantity = $('#new_stock_quantity').val();

    $.ajax({
        url: `/admin/product/${currentProductId}/update-stock`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            stock_quantity: stockQuantity
        },
        beforeSend: function() {
            $('#updateStockForm button[type="submit"]').prop('disabled', true).text('Updating...');
        },
        success: function(response) {
            $('#updateStockModal').modal('hide');

            if (response.success) {
                // Show success message
                showAlert('success', response.message);

                // Refresh the DataTable
                dataTable.ajax.reload(null, false);
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while updating stock.';

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                if (errors.stock_quantity) {
                    errorMessage = errors.stock_quantity[0];
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            showAlert('danger', errorMessage);
        },
        complete: function() {
            $('#updateStockForm button[type="submit"]').prop('disabled', false).text('Update Stock');
        }
    });
});

// Function to show alert messages
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Remove existing alerts
    $('.alert').remove();

    // Add new alert at the top of content
    $('.content-wrapper').prepend(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

// Reset modal when it's hidden
$('#updateStockModal').on('hidden.bs.modal', function() {
    currentProductId = null;
    $('#updateStockForm')[0].reset();
});

function viewReviews(productId, productName) {
    $('#viewReviewsModalLabel').text(`Reviews for: ${productName}`);
    $('#reviewsContainer').html(`
        <div class="text-center text-muted py-4" id="reviewsLoader">
            <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem"></div>
            <p class="mt-2 mb-0">Loading reviews...</p>
        </div>
    `);

    $('#viewReviewsModal').modal('show');

    $.ajax({
        url: `/admin/product/${productId}/reviews`,
        method: 'GET',
        success: function(response) {
            const reviews = response.reviews;
            let html = '';

            if (!reviews || reviews.length === 0) {
                html = `
                    <div class="text-center py-5 text-muted">
                        <h6>No reviews yet</h6>
                        <p class="small">This product hasn’t received any feedback yet.</p>
                    </div>
                `;
                $('#reviewsContainer').html(html);
                return;
            }

            reviews.forEach(review => {
                const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
                const avatar = review.user?.profile_photo_url
                    ? review.user.profile_photo_url
                    : `assets/images/default.png`;

                html += `
                    <div class="review-card bg-white rounded shadow-sm p-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <img src="${avatar}" alt="Avatar" class="rounded-circle me-3" width="45" height="45">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">${review.user?.name || 'Anonymous'}</h6>
                                <div class="text-warning small">${stars}</div>
                            </div>
                            <small class="text-muted">${new Date(review.created_at).toLocaleDateString()}</small>
                        </div>

                        ${review.title ? `<p class="fw-semibold mb-1">${review.title}</p>` : ''}
                        <p class="text-muted small mb-2">${review.comment || ''}</p>
                        ${review.is_verified
                            ? '<span class="badge bg-success text-white small">Verified Purchase</span>'
                            : ''}
                    </div>
                `;
            });

            $('#reviewsContainer').html(html);
        },
        error: function() {
            $('#reviewsContainer').html(`
                <div class="text-center text-danger py-4">
                    <i class="ti-alert"></i> Failed to load reviews. Please try again later.
                </div>
            `);
        }
    });
}
</script>
@endpush
