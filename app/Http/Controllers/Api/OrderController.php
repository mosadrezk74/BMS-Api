<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'shipping_address' => 'required|string|max:255',
        ]);

        // Get the current user
        $user = auth()->user();

        // Retrieve user's cart items
        $cartItems = Cart::where('user_id', $user->id)->with('book','ship')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty'], 400);
        }

        // Calculate total amount
        $totalAmount = $cartItems->sum(function($item) {
            return ($item->quantity * $item->book->price)+$item->ship->price;
        });

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'shipping_address' => $validatedData['shipping_address'],
            'total_amount' => $totalAmount,
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $item->book_id,
                'quantity' => $item->quantity,
                'price' => $item->book->price,
            ]);
        }

        // Clear the user's cart
        Cart::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id]);
    }
    public function orderHistory()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->with('orderItems.book')->get();

        $filteredOrders = $orders->filter(function ($order) {
            return $order->status == 0;
        });

        return response()->json($filteredOrders);
    }

    public function index()
    {

        $orders = Order::with('user', 'orderItems.book')->get();

        return response()->json($orders);
    }

    public function show()
{
    $user = auth()->user();
    $order = Order::with('user', 'orderItems.book')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->first();

    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    return response()->json($order);
}


    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = $request->status;
        $order->save();

        $statusText = $this->getStatusText($order->status);

        return response()->json([
            'message' => "Order status updated to: $statusText",
            'order' => $order
        ]);
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Processing';
            case 1:
                return 'In Road';
            case 2:
                return 'Delivered';
            default:
                return 'Unknown Status';
        }
    }


}
