<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Book::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'descr' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'author_id' => 'nullable|integer',
            'pages' => 'nullable|integer',
            'rating' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
        ]);

        // Check if an image file is uploaded
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public'); // Save the file and store the path
            $validatedData['image'] = $imagePath; // Store the path in the database
        }

        // Save the book data to the database
        $book = Book::create($validatedData);

        return response()->json([
            'message' => 'Book Created Successfully',
            'book' => $book,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return $book;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $book->update($request->all());

        return response()->json($book, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(null, 204);
    }
}
