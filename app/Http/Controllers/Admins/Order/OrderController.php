<?php

namespace App\Http\Controllers\Admins\Order;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderStatusUpdate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');

        return view('admins.order.index', compact(
            'totalOrders', 'pendingOrders', 'processingOrders',
            'shippedOrders', 'deliveredOrders', 'totalRevenue'
        ));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'tracking.updates', 'statusUpdates.updatedBy']);
        return view('admins.order.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admins.order.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // dd($request->all());

        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        // Create status update record
        OrderStatusUpdate::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        // Update tracking if provided
        if (isset($validated['tracking_number'])) {
            $order->tracking()->updateOrCreate(
                ['order_id' => $order->id],
                ['tracking_number' => $validated['tracking_number']]
            );
        }

        // Send notification to user (you can implement this)
        // NotificationService::sendOrderStatusUpdate($order, $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('order.index')
                ->with('error', 'Only pending orders can be deleted!');
        }

        $order->delete();
        return redirect()->route('order.index')
            ->with('success', 'Order deleted successfully!');
    }

    public function getOrders(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['user', 'items']);

            // Apply filters
            if ($request->has('status') && $request->status !== 'all') {
                $orders = $orders->where('status', $request->status);
            }

            return DataTables::of($orders)
                ->addIndexColumn()
                ->editColumn('order_number', function($row) {
                    return '
                        <div>
                            <strong>#'.$row->order_number.'</strong><br>
                            <small class="text-muted">'.($row->order_date ? $row->order_date->format('M d, Y') : 'N/A').'</small>
                        </div>';
                })
                ->editColumn('user_id', function($row) {
                    return '
                        <div>
                            <strong>'.$row->user->name.'</strong><br>
                            <small class="text-muted">'.$row->user->email.'</small>
                        </div>';
                })
                ->editColumn('total_amount', function($row) {
                    return '$'.number_format($row->total_amount ?? 0, 2);
                })
                ->editColumn('status', function($row) {
                    $color = $row->status_color;
                    return '<span class="badge bg-'.$color.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('items_count', function($row) {
                    return $row->items->count().' item(s)';
                })
                ->addColumn('actions', function($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="'.route('order.show', $row->id).'">
                                    <i class="ti-eye"></i> View Details
                                </a></li>
                                <li><a class="dropdown-item" href="'.route('order.edit', $row->id).'">
                                    <i class="ti-pencil"></i> Update Status
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="printInvoice('.$row->id.')">
                                    <i class="ti-printer"></i> Print Invoice
                                </a></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['order_number', 'user_id', 'status', 'actions'])
                ->make(true);
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        // Create status update record
        OrderStatusUpdate::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    public function printInvoice(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admins.order.invoice', compact('order'));
    }

    public function export(Request $request, $format)
    {
        $orders = Order::with('user')
            ->when($request->status && $request->status !== 'all', fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'No orders found to export.');
        }

        if ($format === 'csv') {
            return $this->exportAsCSV($orders);
        } elseif ($format === 'pdf') {
            return $this->exportAsPDF($orders);
        }

        return back()->with('error', 'Invalid export format.');
    }

    protected function exportAsCSV($orders)
    {
        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['Order #', 'Customer Name', 'Email', 'Status', 'Total Amount', 'Date'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    '#' . $order->order_number,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    ucfirst($order->status),
                    number_format($order->total_amount, 2),
                    optional($order->order_date)->format('M d, Y') ?? $order->created_at->format('M d, Y'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    protected function exportAsPDF($orders)
    {
        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = Pdf::loadView('admins.order.exports.pdf', compact('orders'));
        return $pdf->download($filename);
    }
}
