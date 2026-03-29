<?php

namespace App\Http\Controllers\Admins\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\TrackingUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['tracking.updates', 'user']);
        return view('admins.order.tracking', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:255',
            'carrier' => 'required|string|max:255',
            'estimated_delivery' => 'nullable|date',
        ]);

        $order->tracking()->updateOrCreate(
            ['order_id' => $order->id],
            $validated
        );

        return redirect()->back()->with('success', 'Tracking information updated successfully!');
    }

    public function addUpdate(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'timestamp' => 'required|date',
        ]);

        $tracking = $order->tracking;
        if (!$tracking) {
            return response()->json(['error' => 'No tracking information found'], 400);
        }

        TrackingUpdate::create([
            'order_tracking_id' => $tracking->id,
            'status' => $validated['status'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'timestamp' => $validated['timestamp'],
        ]);

        return response()->json(['success' => true, 'message' => 'Tracking update added successfully!']);
    }

    public function generateTrackingNumber()
    {
        // Generate a unique tracking number
        do {
            $trackingNumber = 'FW' . strtoupper(Str::random(8)) . rand(1000, 9999);
        } while (OrderTracking::where('tracking_number', $trackingNumber)->exists());

        return response()->json(['tracking_number' => $trackingNumber]);
    }

    public function trackingApi(Request $request)
    {
        $trackingNumber = $request->get('tracking_number');

        if (!$trackingNumber) {
            return response()->json(['error' => 'Tracking number is required'], 400);
        }

        $tracking = OrderTracking::where('tracking_number', $trackingNumber)
            ->with(['order.user', 'updates'])
            ->first();

        if (!$tracking) {
            return response()->json(['error' => 'Tracking number not found'], 404);
        }

        return response()->json([
            'tracking_number' => $tracking->tracking_number,
            'carrier' => $tracking->carrier,
            'estimated_delivery' => $tracking->estimated_delivery,
            'order' => [
                'order_number' => $tracking->order->order_number,
                'customer_name' => $tracking->order->user->name,
                'status' => $tracking->order->status,
            ],
            'updates' => $tracking->updates->map(function ($update) {
                return [
                    'status' => $update->status,
                    'location' => $update->location,
                    'description' => $update->description,
                    'timestamp' => $update->timestamp->format('M d, Y H:i'),
                ];
            }),
        ]);
    }
}
