<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $orders = Order::where('user_id', Auth::id())
            ->whereNotIn('status', ['delivered', 'returned'])
            ->pluck('id')
            ->toArray();

        $product_ids = OrderItem::whereIn('order_id', $orders)
            ->pluck('product_id')
            ->toArray();

        foreach($product_ids as $item)
        {
            if($request->product_id == $item)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Your order is currently not delivered, you cannot review this product for now',
                ]);
            }
        }

        $exists = ProductReview::where('product_id', $request->product_id)
            ->where('user_id', Auth::Id())
            ->first();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'You have already reviewed this product',
            ], 400);
        }

        $review = ProductReview::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::Id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review submitted successfully',
        ]);
    }
}
