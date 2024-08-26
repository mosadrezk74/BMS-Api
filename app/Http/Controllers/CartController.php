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
        'quantity' => 'required|integer|min:1',
        'ship_id' => 'required|integer|min:1'
    ]);

    $user = auth()->user();

    $cartItem = Cart::where('user_id', $user->id)
                        ->where('book_id', $validatedData['book_id'])->with('book', 'user')
                        ->first();

    if ($cartItem) {
        $cartItem->quantity += $validatedData['quantity'];
        $cartItem->save();
    } else {
        Cart::create([
            'user_id' => $user->id,
            'book_id' => $validatedData['book_id'],
            'quantity' => $validatedData['quantity'],
            'ship_id' => $validatedData['ship_id'],

        ]);
    }

    return response()->json(['message' => 'Book added to cart successfully.']);
}


    // View Cart
    public function viewCart()
    {
        $user = auth()->user();

        $cartItems = Cart::with('book','ship')->where('user_id', $user->id)->get();

        $cartDetails = $cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'book_id' => $item->book_id,
                'book_name' => $item->book->name,
                'quantity' => $item->quantity,
                'price_per_item' => $item->book->price,
                'Shipping_Address' => $item->ship->name,
                'total_price' => ($item->quantity * $item->book->price)+$item->ship->price,
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

        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $user = auth()->user();


        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }


        $cartItem->quantity = $validatedData['quantity'];
        $cartItem->save();

        return response()->json(['message' => 'Cart item updated successfully']);
    }


    // Remove Item from Cart
    public function removeFromCart($id)
    {
        $user = auth()->user();
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->first();
        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed successfully']);
    }

}

