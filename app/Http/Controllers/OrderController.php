<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Events\OrderUpdated;

class OrderController extends Controller
{
    public function index() // get the soft deleted products as well
    {
        $orders = Order::with(['items.product' => function($query) {
            $query->withTrashed();
        }])->where('user_id', Auth::id())->latest()->get();

        return response()->json($orders);
    }

    public function adminIndex()
    {
        $orders = Order::with(['user', 'items.product' => function($query) {
            $query->withTrashed();
        }]) 
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $newStatus = $request->status;
        $order = $user->isAdmin()
            ? Order::findOrFail($id)
            : Order::where('user_id', Auth::id())->findOrFail($id);

        // user
        if (!$user->isAdmin()) {
            // Only allow cancel
            if ($newStatus !== 'cancelled') {
                return response()->json([
                    'message' => 'Unauthorized action'
                ], 403);
            }

            // Only if pending
            if ($order->status !== 'pending') {
                return response()->json([
                    'message' => 'Order cannot be cancelled'
                ], 400);
            }
        }

        // admin 
        if ($user->isAdmin()) {
            if ($newStatus !== 'completed') {
                return response()->json([
                    'message' => 'Admin can only complete pending orders'
                ], 403);
            }

            if ($order->status !== 'pending') {
                return response()->json([
                    'message' => 'Only pending orders can be completed'
                ], 400);
            }
        }

        $order->update([
            'status' => $newStatus
        ]);

        event(new OrderUpdated($order));

        return response()->json([
            'message' => 'Status updated',
            'order' => $order
        ]);
    }

    public function buyNow($id, $quantity) {
        $product = Product::findOrFail($id);

        if ($quantity > $product->stock) {
            return response()->json([
                'message' => "Not enough stock for product - {$product->name}, available: {$product->stock}, requested: {$quantity}"
            ], 400);
        }

        $product->decrement('stock', $quantity);

        $order = Order::create([
            'user_id' => Auth::id(), 
            'total_price' => 0,
            'status' => 'pending'
        ]);

        $subtotal = $product->price * $quantity;
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price
        ]);

        $order->update([
            'total_price' => $subtotal
        ]);

        event(new OrderUpdated($order));

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order->load('items')
        ]);
    }

    public function checkout()
    {
        try {
            $cart = session()->get('cart', []);

            if (empty($cart)) {
                return response()->json([
                    'message' => 'Cart is empty'
                ], 400);
            }

            $total = 0;
            // Check stock availability
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if ($item['quantity'] > $product->stock) {
                    return response()->json([
                        'message' => "Not enough stock for product - {$product->name}, available: {$product->stock}, requested: {$item['quantity']}"
                    ], 400);
                }

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(), 
                'total_price' => 0,
                'status' => 'pending'
            ]);

            // Create order items
            foreach ($cart as $productId => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            // Update total
            $order->update([
                'total_price' => $total
            ]);

            event(new OrderUpdated($order));

            // Clear cart
            session()->forget('cart');

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('items') // Load order items with product details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error placing order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
