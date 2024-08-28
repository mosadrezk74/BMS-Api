<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Trait\GeneralTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Book::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'descr' => 'nullable|string',
            'price' => 'required|integer',
            'category_id' => 'nullable|integer',
            'author_id' => 'nullable|integer',
            'pages' => 'nullable|integer',
            'rating' => 'nullable|integer|min:0|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $book = Book::create($validatedData);

        return response()->json([
            'message' => 'Book Created Successfully',
            'book' => $book,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $book = Book::with(['author', 'category', 'reviews'])->find($request->id);

        if (!$book) {
            return response()->json(['message' => 'Book Not Found'], 404);
        }

        $responseData = [
            'id' => $book->id,
            'name' => $book->name,
            'image' => $book->image,
            'desc' => $book->descr,
            'category' => [
                'name' => $book->category->name,
            ],
            'author' => [
                'name' => $book->author->name,
            ],
            'reviews' => $book->reviews()->where('approved', true)->get(),
        ];

        return response()->json($responseData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'descr' => 'nullable|string',
            'price' => 'required|integer',
            'category_id' => 'nullable|integer',
            'author_id' => 'nullable|integer',
            'pages' => 'nullable|integer',
            'rating' => 'nullable|integer|min:0|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image) {
                Storage::delete('public/' . $book->image);
            }

            $imagePath = $request->file('image')->store('images', 'public');
            $validatedData['image'] = $imagePath;
        }

        $book->update($validatedData);
        return response()->json(['message' => 'Book Updated Successfully', 'book' => $book], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book Not Found'], 404);
        }

        if ($book->image) {
            Storage::delete('public/' . $book->image);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted successfully'], 200);
    }

    public function deleteAll()
    {
        Book::truncate();
        ########################################################################################
        return response()->json(['message' => 'All books deleted successfully']);
    }



    public function search(Request $request)
{
    $query = $request->input('query');
    $books = Book::with(['author'])
        ->where('name', 'LIKE', "%{$query}%")
        ->orWhereHas('author', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%");
        })
        ->get();

    if ($books->isEmpty()) {
        return response()->json(['message' => 'No books found'], 404);
    }

    return response()->json($books);
}

public function filterBooks(Request $request)
{
    $query = Book::query();

    if ($request->has('min_price') && $request->has('max_price')) {
        $query->whereBetween('price', [$request->min_price, $request->max_price]);
    }
        
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    if ($request->has('author_id')) {
        $query->whereHas('author', function ($q) use ($request) {
            $q->where('author_id', $request->author_id);
        });
    }

    $books = $query->with(['category', 'author'])->get();

    return response()->json($books);
}

}
