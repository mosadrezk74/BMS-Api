<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Store a new review
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'book_id' => $bookId,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false, // Default to false, admin needs to approve
        ]);

        return response()->json([
            'message' => 'Review submitted successfully, awaiting approval.',
            'review' => $review
        ], 201);
    }

    public function approve($id)
{
    $review = Review::findOrFail($id);
    $review->approved = true;
    $review->save();

    return response()->json(['message' => 'Review approved successfully.']);
}

public function destroy($id)
{
    $review = Review::findOrFail($id);
    $review->delete();

    return response()->json(['message' => 'Review deleted successfully.']);
}

}
