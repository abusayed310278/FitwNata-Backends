@extends('layouts.adminApp')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Order #{{ $order->order_number }}</h1>
            <p class="mb-0">View and manage order details</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('order.edit', $order) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Update Status
            </a>
            <a href="{{ route('order.invoice', $order) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-file-invoice me-2"></i>Print Invoice
            </a>
            <a href="{{ route('order.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Order Number:</strong>
                            <p class="mb-0">#{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $order->status_color }} ms-2">{{ ucfirst($order->status) }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Order Date:</strong>
                            <p class="mb-0">{{ $order->order_date ? $order->order_date->format('M d, Y H:i') : $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Amount:</strong>
                            <p class="mb-0 fw-bold text-success">${{ number_format($order->total_amount ?? 0, 2) }}</p>
                        </div>
                    </div>

                    @if($order->tracking && $order->tracking->tracking_number)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tracking Number:</strong>
                            <p class="mb-0 font-family-monospace">{{ $order->tracking->tracking_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Estimated Delivery:</strong>
                            <p class="mb-0">{{ $order->tracking->estimated_delivery ? $order->tracking->estimated_delivery->format('M d, Y') : 'TBD' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->images && count($item->product->images) > 0)
                                                <img src="{{ asset('storage/'.$item->product->images[0]) }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product ? $item->product->name : 'Product Not Found' }}</strong>
                                                @if($item->product && $item->product->category)
                                                    <br><small class="text-muted">{{ $item->product->category->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->product ? $item->product->sku : 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No items found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th>${{ number_format($order->total_amount ?? 0, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Delivery Tracking -->
            @if($order->tracking)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-truck me-2"></i>Delivery Tracking
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($order->tracking->updates as $update)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">{{ $update->status }}</div>
                            <div class="text-muted">{{ $update->location }}</div>
                            <div class="small text-muted">{{ $update->timestamp->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No tracking updates available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Customer & Status Information -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if (!empty($order->user->profile_photo_url))
                            <img src="{{ asset($order->user->profile_photo_url) }}" 
                                alt="{{ $order->user->name }}'s Avatar"
                                class="rounded-circle" 
                                style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        @endif
                    </div>
                    <div class="text-center">
                        <h5 class="mb-1">{{ $order->user->name }}</h5>
                        <p class="mb-1 text-muted">{{ $order->user->email }}</p>
                        <p class="mb-0 text-muted">{{ $order->user->phone ?? 'No phone provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            @if(!empty($order->shipping_address))
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Shipping Address</h6>
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        {{ $order->shipping_address['street'] ?? '' }}<br>
                        {{ $order->shipping_address['city'] ?? '' }},
                        {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                        {{ strtoupper($order->shipping_address['country'] ?? '') }}
                    </address>
                </div>
            </div>
            @endif

            <!-- Quick Status Update -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Status Update</h6>
                </div>
                <div class="card-body">
                    <form id="quickStatusForm">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status History -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                </div>
                <div class="card-body">
                    @forelse($order->statusUpdates as $update)
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">{{ ucfirst($update->old_status) }} → {{ ucfirst($update->new_status) }}</div>
                            @if($update->notes)
                                <div class="text-muted small">{{ $update->notes }}</div>
                            @endif
                            <div class="small text-muted">
                                {{ $update->created_at->format('M d, Y H:i') }}
                                @if($update->updatedBy)
                                    by {{ $update->updatedBy->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted mb-0">No status changes recorded</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$('#quickStatusForm').on('submit', function(e) {
    e.preventDefault();

    const formData = {
        _token: $('[name="_token"]').val(),
        status: $('#status').val(),
        notes: $('#notes').val()
    };

    $.ajax({
        url: '{{ route("order.update-status", $order) }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('Status updated successfully!');
                location.reload();
            }
        },
        error: function() {
            alert('Error updating status. Please try again.');
        }
    });
});
</script>
@endpush
@endsection
