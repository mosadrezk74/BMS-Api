<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category; // Assuming Category is the model for categories

class CatAndAuthorStc extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all categories
        return response()->json(Category::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new category
        $category = Category::create($validatedData);

        return response()->json([
            'message' => 'Category Created Successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Find a specific category by its ID
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json(['message' => 'Category Not Found'], 404);
        }

        return response()->json($category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category Not Found'], 404);
        }

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update the category
        $category->update($validatedData);

        return response()->json([
            'message' => 'Category Updated Successfully',
            'category' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category Not Found'], 404);
        }

        // Delete the category
        $category->delete();

        return response()->json(['message' => 'Category Deleted Successfully'], 200);
    }
    #########################################################################
    #########################################################################
    #########################################################################
    public function author_index()
    {
        return Author::all();
    }

    public function author_store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $author = Author::create($validatedData);

        return response()->json([
            'message' => 'Author Created Successfully',
            'author' => $author,
        ]);
    }

    public function author_show(Request $request)
    {
        $author = Author::find($request->id);

        if (!$author) {
            return response()->json(['message' => 'Author Not Found'], 404);
        }

        return response()->json($author);
    }

    public function author_update(Request $request, $id)
    {
        $author = Author::find($id);

        if (!$author) {
            return response()->json(['message' => 'Author Not Found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $author->update($validatedData);

        return response()->json(['message' => 'Author Updated Successfully', 'author' => $author]);
    }

    public function author_destroy($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return response()->json(['message' => 'Author Not Found'], 404);
        }

        $author->delete();

        return response()->json(['message' => 'Author Deleted Successfully']);
    }
}
