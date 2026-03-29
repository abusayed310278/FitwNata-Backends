<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fff; }
        .invoice-box { max-width: 900px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .table th, .table td { vertical-align: middle; }
        .header { border-bottom: 2px solid #007bff; margin-bottom: 20px; padding-bottom: 10px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #007bff; }
        .footer { border-top: 1px solid #ccc; text-align: center; padding-top: 10px; font-size: 13px; color: #666; margin-top: 30px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <div class="header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="invoice-title">INVOICE</h2>
            <p class="mb-0">Order #{{ $order->order_number }}</p>
            <p class="text-muted mb-1">{{ $order->order_date?->format('M d, Y') ?? now()->format('M d, Y') }}</p>
            <span class="badge bg-{{ $order->status_color }} px-3 py-2">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        <div class="text-end">
            <img src="{{ asset('assets/logo.png') }}" alt="fitwnata logo" style="max-height:60px;" />
        </div>
    </div>

    <div class="row mb-4 mt-3">
        <div class="col-md-6">
            <h6 class="text-primary">Billed To:</h6>
            <p class="mb-0"><strong>{{ $order->user->name }}</strong></p>
            <p class="mb-0">{{ $order->user->email }}</p>
            @if($order->user->phone)
                <p class="mb-0">{{ $order->user->phone }}</p>
            @endif
        </div>
        <div class="col-md-6 text-end">
            <h6 class="text-primary">Shipping Address:</h6>
            @php
                $address = $order->shipping_address ?? [];
            @endphp
            <p class="mb-0">{{ $address['street'] ?? '' }}</p>
            <p class="mb-0">{{ $address['city'] ?? '' }} {{ $address['postal_code'] ?? '' }}</p>
            <p class="mb-0">{{ strtoupper($address['country'] ?? '') }}</p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product->name ?? 'Product not found' }}<br>
                        <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">${{ number_format($item->price, 2) }}</td>
                    <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-light">
                <th colspan="4" class="text-end">Subtotal:</th>
                <th class="text-end">${{ number_format($order->total_amount, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="text-end mt-4">
        <button onclick="window.print()" class="btn btn-primary no-print">
            <i class="fas fa-print me-2"></i>Print Invoice
        </button>
    </div>

    <div class="footer">
        Thank you for shopping with us!  
        <br>© {{ date('Y') }} Fitwnata. All rights reserved.
    </div>
</div>

<!-- Font Awesome for print icon -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>