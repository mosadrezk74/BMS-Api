<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthStc;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CatAndAuthorStc;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register',[AuthStc::class,'register']);
Route::post('login',[AuthStc::class,'login']);
Route::post('logout',[AuthStc::class,'logout'])
  ->middleware('auth:sanctum');


  Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::group(['prefix' => 'books'], function () {
        Route::get('/', [BookController::class, 'index']); // List all books
        Route::post('/', [BookController::class, 'store']); // Create a new book
        Route::get('/show', [BookController::class, 'show']); // Show a specific book
        Route::put('/{id}', [BookController::class, 'update']); // Update a book
        Route::delete('/{id}', [BookController::class, 'destroy']); // Delete a book
        Route::delete('/DeleteAll', [BookController::class, 'deleteAll']);
    });


    Route::group(['prefix' => 'category'], function () {
        Route::get('/', [CatAndAuthorStc::class, 'index']); // List all books
        Route::post('/', [CatAndAuthorStc::class, 'store']); // Create a new book
        Route::get('/show', [CatAndAuthorStc::class, 'show']); // Show a specific book
        Route::put('/{id}', [CatAndAuthorStc::class, 'update']); // Update a book
        Route::delete('/{id}', [CatAndAuthorStc::class, 'destroy']); // Delete a book
    });



    Route::group(['prefix' => 'author'], function () {
        Route::get('/', [CatAndAuthorStc::class, 'author_index']); // List all books
        Route::post('/', [CatAndAuthorStc::class, 'author_store']); // Create a new book
        Route::get('/show', [CatAndAuthorStc::class, 'author_show']); // Show a specific book
        Route::put('/{id}', [CatAndAuthorStc::class, 'author_update']); // Update a book
        Route::delete('/{id}', [CatAndAuthorStc::class, 'author_destroy']); // Delete a book
    });

    Route::get('/books/search', [BookController::class, 'search']);
    ###############################################################################################
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders/history', [OrderController::class, 'orderHistory']);

    Route::get('/orders', [OrderController::class, 'index']); // View All Orders
    Route::put('/orders/{id}', [OrderController::class, 'updateOrderStatus']);
    ###############################################################################################


    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::get('/', [CartController::class, 'viewCart']);
        Route::put('/update/{id}', [CartController::class, 'updateCart']);
        Route::delete('/remove/{id}', [CartController::class, 'removeFromCart']);
    });

});



