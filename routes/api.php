<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthStc;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CatAndAuthorStc;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ShippingAddController;

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
        Route::get('/', [BookController::class, 'index']);
        Route::post('/', [BookController::class, 'store']);
        Route::get('/show', [BookController::class, 'show']);
        Route::put('/{id}', [BookController::class, 'update']);
        Route::delete('/{id}', [BookController::class, 'destroy']);
        Route::delete('/DeleteAll', [BookController::class, 'deleteAll']);
        Route::get('/filter', [BookController::class, 'filterBooks']);

    });


    Route::group(['prefix' => 'category'], function () {
        Route::get('/', [CatAndAuthorStc::class, 'index']);
        Route::post('/', [CatAndAuthorStc::class, 'store']);
        Route::get('/show', [CatAndAuthorStc::class, 'show']);
        Route::put('/{id}', [CatAndAuthorStc::class, 'update']);
        Route::delete('/{id}', [CatAndAuthorStc::class, 'destroy']);
    });



    Route::group(['prefix' => 'author'], function () {
        Route::get('/', [CatAndAuthorStc::class, 'author_index']);
        Route::post('/', [CatAndAuthorStc::class, 'author_store']);
        Route::get('/show', [CatAndAuthorStc::class, 'author_show']);
        Route::put('/{id}', [CatAndAuthorStc::class, 'author_update']);
        Route::delete('/{id}', [CatAndAuthorStc::class, 'author_destroy']);
    });

    Route::get('/books/search', [BookController::class, 'search']);
    ###############################################################################################
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders/history', [OrderController::class, 'orderHistory']);
    ###############################################################################################
    Route::post('/pay_checkout', [PaymentController::class, 'processPayment']);
    Route::post('/payment/confirm', [PaymentController::class, 'confirmOrder']);
    ###############################################################################################
    Route::get('/orders', [OrderController::class, 'index']); // View All Orders
    Route::get('/lastOrder/show', [OrderController::class, 'show']); // View All Orders
    Route::put('/orders/{id}', [OrderController::class, 'updateOrderStatus']);
    ###############################################################################################


    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::get('/', [CartController::class, 'viewCart']);
        Route::put('/update/{id}', [CartController::class, 'updateCart']);
        Route::delete('/remove/{id}', [CartController::class, 'removeFromCart']);
    });

    ###############################################################################################
    Route::prefix('shipping')->group(function () {
        Route::post('/create', [ShippingAddController::class, 'CreateShipping']);
        Route::get('/', [ShippingAddController::class, 'ViewShipping']);
        Route::put('/update/{id}', [ShippingAddController::class, 'updateShipping']);
        Route::delete('/remove/{id}', [ShippingAddController::class, 'removeShipping']);
    });
    ###############################################################################################

    Route::post('books/{book}/reviews', [ReviewController::class, 'store']);
########################################################################################################################################

    Route::middleware('auth', 'admin')->group(function () {
        Route::put('reviews/{review}/approve', [ReviewController::class, 'approve']);
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
    });


});



