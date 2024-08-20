<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Add to Cart
    public function addToCart(Request $request)
{
    $validatedData = $request->validate([
        'book_id' => 'required|exists:books,id',
        'quantity' => 'required|integer|min:1'
    ]);

    // Retrieve the currently authenticated user
    $user = auth()->user();

    // Check if the item is already in the cart
    $cartItem = Cart::where('user_id', $user->id)
                        ->where('book_id', $validatedData['book_id'])
                        ->first();

    if ($cartItem) {
        // Update quantity if the item is already in the cart
        $cartItem->quantity += $validatedData['quantity'];
        $cartItem->save();
    } else {
        // Create new cart item
        Cart::create([
            'user_id' => $user->id,
            'book_id' => $validatedData['book_id'],
            'quantity' => $validatedData['quantity'],
        ]);
    }

    return response()->json(['message' => 'Book added to cart successfully.']);
}


    // View Cart
    public function viewCart()
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // Retrieve all items in the user's cart
        $cartItems = Cart::with('book')->where('user_id', $user->id)->get();

        // Calculate the total price for each item and the overall total
        $cartDetails = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'book_id' => $item->book_id,
                'book_name' => $item->book->name,
                'quantity' => $item->quantity,
                'price_per_item' => $item->book->price, // Assuming book has a price field
                'total_price' => $item->quantity * $item->book->price
            ];
        });

        return response()->json([
            'cart_items' => $cartDetails,
            'total_price' => $cartDetails->sum('total_price')
        ]);
    }


    // Update Cart Item Quantity
    public function updateCart(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Get the currently authenticated user
        $user = auth()->user();

        // Find the cart item
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Update the quantity
        $cartItem->quantity = $validatedData['quantity'];
        $cartItem->save();

        return response()->json(['message' => 'Cart item updated successfully']);
    }


    // Remove Item from Cart
    public function removeFromCart($id)
    {
        // Get the currently authenticated user
        $user = auth()->user();

        // Find the cart item
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        // Delete the cart item
        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed successfully']);
    }

}

