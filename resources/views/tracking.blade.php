<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Fitwnata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-5">
                    <h1 class="h2 text-primary">Track Your Order</h1>
                    <p class="text-muted">Enter your tracking number to get real-time delivery updates</p>
                </div>

                <!-- Tracking Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form id="trackingForm">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" class="form-control form-control-lg" id="trackingNumber" placeholder="Enter tracking number (e.g., FW12345678)" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Track Order
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tracking Results -->
                <div id="trackingResults" style="display: none;">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Order Details</h5>
                        </div>
                        <div class="card-body">
                            <div id="orderInfo"></div>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-route me-2"></i>Tracking Updates</h5>
                        </div>
                        <div class="card-body">
                            <div id="trackingUpdates"></div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="alert alert-danger" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="errorText"></span>
                </div>

                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Searching for your order...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $('#trackingForm').on('submit', function(e) {
        e.preventDefault();

        const trackingNumber = $('#trackingNumber').val().trim();

        if (!trackingNumber) {
            showError('Please enter a tracking number');
            return;
        }

        // Show loading
        $('#loadingIndicator').show();
        $('#trackingResults').hide();
        $('#errorMessage').hide();

        // Make API call
        $.ajax({
            url: '/api/v1/tracking/' + encodeURIComponent(trackingNumber),
            method: 'GET',
            success: function(data) {
                $('#loadingIndicator').hide();
                displayTrackingResults(data);
            },
            error: function(xhr) {
                $('#loadingIndicator').hide();
                const errorMsg = xhr.responseJSON?.error || 'An error occurred while tracking your order';
                showError(errorMsg);
            }
        });
    });

    function displayTrackingResults(data) {
        // Display order info
        const orderInfoHtml = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Order Number:</strong> #${data.order.order_number}<br>
                    <strong>Customer:</strong> ${data.order.customer_name}<br>
                    <strong>Status:</strong> <span class="badge bg-primary">${data.order.status}</span>
                </div>
                <div class="col-md-6">
                    <strong>Tracking Number:</strong> ${data.tracking_number}<br>
                    <strong>Carrier:</strong> ${data.carrier || 'Not specified'}<br>
                    <strong>Est. Delivery:</strong> ${data.estimated_delivery || 'TBD'}
                </div>
            </div>
        `;
        $('#orderInfo').html(orderInfoHtml);

        // Display tracking updates
        let updatesHtml = '';
        if (data.updates && data.updates.length > 0) {
            data.updates.forEach(function(update, index) {
                const isLatest = index === 0;
                updatesHtml += `
                    <div class="d-flex mb-3 ${isLatest ? 'bg-light p-3 rounded' : ''}">
                        <div class="flex-shrink-0">
                            <div class="bg-${isLatest ? 'primary' : 'secondary'} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">${update.status}</div>
                            <div class="text-muted">${update.location}</div>
                            ${update.description ? `<div class="small text-muted">${update.description}</div>` : ''}
                            <div class="small text-muted">${update.timestamp}</div>
                        </div>
                    </div>
                `;
            });
        } else {
            updatesHtml = '<p class="text-muted text-center py-4">No tracking updates available yet.</p>';
        }
        $('#trackingUpdates').html(updatesHtml);

        $('#trackingResults').show();
    }

    function showError(message) {
        $('#errorText').text(message);
        $('#errorMessage').show();
    }
    </script>
</body>
</html>
