<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;

class ShopController extends BaseApiController
{
    /**
     * Get all products.
     */
    public function products(Request $request): JsonResponse
    {
        $query = Product::with('productSpecifications', 'reviews.user')->orderByDESC('id');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->where('stock_quantity', '>', 0)
            ->paginate(150);

        return $this->paginatedSuccess($products, 'Products retrieved successfully');
    }

    /**
     * Get product details.
     */
    public function productDetails(Product $product): JsonResponse
    {
        $product->load('productSpecifications');
        return $this->success($product, 'Product details retrieved');
    }

    /**
     * Get all categories.
     */
    public function categories(): JsonResponse
    {
        $categories = ProductCategory::all();
        return $this->success($categories, 'Categories retrieved successfully');
    }

    /**
     * Get products by category.
     */
    public function productsByCategory(ProductCategory $category): JsonResponse
    {
        $products = $category->products()
            ->with('productSpecifications')
            ->where('stock_quantity', '>', 0)
            ->paginate(12);

        return $this->paginatedSuccess($products, 'Products retrieved successfully');
    }

    /**
     * Add item to cart.
     */
    public function addToCart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        if ($product->stock_quantity < $request->quantity) {
            return $this->error('Insufficient stock available');
        }

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // already in cart → update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // new entry
            $cartItem = CartItem::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price, // snapshot price
            ]);
        }

        return $this->success([
            'cart_item' => $cartItem->load('product'),
            'cart_count' => CartItem::where('user_id', $user->id)->count(),
        ], 'Item added to cart successfully');
    }

    /**
     * Get cart contents.
     */
    public function cart(Request $request): JsonResponse
    {
        $user = $request->user();

        $cartItems = $user->cartItems()->with('product')->get();

        $total = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        return $this->success([
            'items' => $cartItems->map(function ($item) {
                return [
                    'cart_id'  => $item->id,
                    'product'  => $item->product,
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ];
            }),
            'total' => $total,
            'count' => $cartItems->count(),
        ], 'Cart retrieved successfully');
    }

    /**
     * Update cart item quantity.
     */
    public function updateCartItem(Request $request, CartItem $item): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Ensure item belongs to current user
        if ($item->user_id != $request->user()->id) {
            return $this->error('Unauthorized action', 403);
        }

        // Check stock availability
        if ($item->product->stock_quantity < $request->quantity) {
            return $this->error('Insufficient stock available');
        }

        $item->quantity = $request->quantity;
        $item->save();

        return $this->success([
            'cart_item' => $item->load('product'),
            'subtotal'  => $item->price * $item->quantity,
        ], 'Cart item updated successfully');
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(Request $request, CartItem $item): JsonResponse
    {
        // Ensure item belongs to current user
        if ($item->user_id != $request->user()->id) {
            return $this->error('Unauthorized action', 403);
        }

        $item->delete();

        return $this->success([], 'Item removed from cart successfully');
    }



    public function checkout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.street' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.country' => 'required|string',
            'payment_method' => 'required|string|in:bpay',
            'client_phone' => 'required|string|min:8|max:15',
            'passcode' => 'required|string|min:4|max:8',
            'language' => 'nullable|string|in:EN,FR,AR,en,fr,ar',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Get cart items
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty');
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->price * $item->quantity;
        }

        try {
            $operationId = 'FITWNATA_' . uniqid();
            $language = strtoupper((string) $request->input('language', 'FR'));
            $bpay = $this->processBpayPayment(
                (string) $request->client_phone,
                (string) $request->passcode,
                (float) $total,
                $language,
                $operationId
            );

            if (!$bpay['success']) {
                return $this->error($bpay['message'] ?? 'B-PAY payment failed', 422);
            }

            $orderData = [
                'user_id'         => $user->id,
                'order_number'    => 'ORD-' . time(),
                'total_amount'    => $total,
                'status'          => 'confirmed',
                'shipping_address'=> $request->shipping_address,
                'order_date'      => now(),
            ];

            if (Schema::hasColumn('orders', 'payment_id')) {
                $orderData['payment_id'] = $bpay['transaction_id'] ?? $operationId;
            }

            if (Schema::hasColumn('orders', 'payment_method')) {
                $orderData['payment_method'] = 'bpay';
            }

            $order = Order::create($orderData);

            // Order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $cartItem->price,
                ]);

                // Update stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();

            return $this->success(
                $order->load('items.product'),
                'Order placed and payment successful'
            );

        } catch (\Exception $e) {
            return $this->serverError('Payment error: ' . $e->getMessage());
        }
    }

    private function bpayAuthenticate(): array
    {
        $baseUrl = rtrim(config('services.bpay.base_url'), '/');
        $username = config('services.bpay.username');
        $password = config('services.bpay.password');
        $clientId = config('services.bpay.client_id', 'ebankily');

        if (empty($baseUrl) || empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'B-PAY is not configured on server'];
        }

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(20)
                ->post("{$baseUrl}/authentification", [
                    'grant_type' => 'password',
                    'username' => $username,
                    'password' => $password,
                    'client_id' => $clientId,
                ]);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'B-PAY authentication timeout'];
        }

        $data = $response->json() ?? [];
        $token = $data['access_token'] ?? null;
        if ($response->successful() && !empty($token)) {
            return ['success' => true, 'access_token' => $token];
        }

        return [
            'success' => false,
            'message' => $data['error_description'] ?? $data['error'] ?? 'B-PAY authentication failed',
        ];
    }

    private function processBpayPayment(
        string $clientPhone,
        string $passcode,
        float $amount,
        string $language,
        string $operationId
    ): array {
        $auth = $this->bpayAuthenticate();
        if (!$auth['success']) {
            return ['success' => false, 'message' => $auth['message'] ?? 'B-PAY auth failed'];
        }

        $baseUrl = rtrim(config('services.bpay.base_url'), '/');
        $token = $auth['access_token'];

        try {
            $response = Http::timeout(30)
                ->withToken($token)
                ->acceptJson()
                ->post("{$baseUrl}/payment", [
                    'clientPhone' => $clientPhone,
                    'passcode' => $passcode,
                    'amount' => number_format($amount, 2, '.', ''),
                    'language' => $language,
                    'operationId' => $operationId,
                ]);
        } catch (\Throwable $e) {
            return $this->checkBpayAfterTimeout($operationId);
        }

        $data = $response->json() ?? [];
        if ($response->successful() && (string)($data['errorCode'] ?? '') === '0') {
            return [
                'success' => true,
                'transaction_id' => $data['transactionId'] ?? null,
                'message' => $data['errorMessage'] ?? 'B-PAY payment successful',
            ];
        }

        return [
            'success' => false,
            'message' => $data['errorMessage'] ?? "B-PAY payment failed ({$response->status()})",
            'transaction_id' => $data['transactionId'] ?? null,
        ];
    }

    private function checkBpayAfterTimeout(string $operationId): array
    {
        $auth = $this->bpayAuthenticate();
        if (!$auth['success']) {
            return ['success' => false, 'message' => 'Payment timed out and B-PAY check auth failed'];
        }

        $baseUrl = rtrim(config('services.bpay.base_url'), '/');
        $lastMessage = 'Payment timed out and status could not be confirmed';

        // Retry a few times because B-PAY status can lag briefly.
        for ($attempt = 1; $attempt <= 4; $attempt++) {
            try {
                $response = Http::timeout(20)
                    ->withToken($auth['access_token'])
                    ->acceptJson()
                    ->post("{$baseUrl}/checkTransaction", [
                        'operationId' => $operationId,
                    ]);
            } catch (\Throwable $e) {
                $lastMessage = 'Payment timeout and checkTransaction also timed out';
                if ($attempt < 4) {
                    usleep(400000);
                    continue;
                }
                return ['success' => false, 'message' => $lastMessage];
            }

            $data = $response->json() ?? [];
            $rawStatus = $data['status'] ?? $data['transactionStatus'] ?? $data['state'] ?? '';
            $status = strtoupper((string) $rawStatus);
            $errorCode = (string) ($data['errorCode'] ?? '');
            $transactionId = $data['transactionId'] ?? null;

            if ($response->successful()) {
                // Canonical and observed success forms.
                if (in_array($status, ['TS', 'SUCCESS', 'SUCCEEDED', 'DONE', 'OK'], true)) {
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'message' => 'Payment succeeded after verification',
                    ];
                }

                // Some integrations signal success with errorCode=0 plus transactionId.
                if ($errorCode === '0' && !empty($transactionId)) {
                    return [
                        'success' => true,
                        'transaction_id' => $transactionId,
                        'message' => 'Payment succeeded after verification',
                    ];
                }

                if (in_array($status, ['TF', 'FAILED', 'ERROR', 'KO'], true)) {
                    return ['success' => false, 'message' => 'Payment failed after verification'];
                }

                if (in_array($status, ['TA', 'AMBIGUOUS'], true)) {
                    return ['success' => false, 'message' => 'Payment status is ambiguous. Verify transaction manually'];
                }
            }

            $lastMessage = $data['errorMessage'] ?? $data['message'] ?? $lastMessage;

            if ($attempt < 4) {
                usleep(400000);
            }
        }

        return ['success' => false, 'message' => $lastMessage];
    }


    /**
     * Get user orders.
     */
    public function orders(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->paginatedSuccess($orders, 'Orders retrieved successfully');
    }

    /**
     * Get order details.
     */
    public function orderDetails(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id != $request->user()->id) {
            return $this->forbidden('You can only view your own orders');
        }

        return $this->success($order->load('items.product'), 'Order details retrieved');
    }
}
