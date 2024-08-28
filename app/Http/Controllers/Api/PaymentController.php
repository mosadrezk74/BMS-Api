<?php

namespace App\Http\Controllers\Api;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\OrderConfirmationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric',
            'stripeToken' => 'required|string',
        ]);

        // Set Stripe API Key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create the charge
            $charge = Charge::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Order Payment',
            ]);

            // Store the order in the database
            $order = Order::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'status' => 0, // default to "processing"
            ]);

            // Return a successful response with the order data
            return response()->json([
                'success' => true,
                'message' => 'Payment successful',
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            // Handle exceptions, such as payment failure
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function confirmOrder(Request $request)
    {
        // Validate request
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        // Find the order
        $order = Order::find($request->order_id);

        if ($order) {
            // Send confirmation email
            Mail::to($request->user())->send(new OrderConfirmationMail($order));

            // Update order status to confirmed
            $order->status = 1; // e.g., 1 could mean 'confirmed'
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed and email sent',
                'order' => $order,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Order not found',
        ], 404);
    }
}
