<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CatAndAuthorStc;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ShippingAddController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->name('user.get');

Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('auth.logout');

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'index'])->name('books.index');
        Route::post('/', [BookController::class, 'store'])->name('books.store');
        Route::get('/show', [BookController::class, 'show'])->name('books.show');
        Route::put('/{id}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/{id}', [BookController::class, 'destroy'])->name('books.destroy');
        Route::delete('/delete-all', [BookController::class, 'deleteAll'])->name('books.deleteAll');
        Route::get('/filter', [BookController::class, 'filterBooks'])->name('books.filterBooks');
    });

    Route::prefix('category')->group(function () {
        Route::get('/', [CatAndAuthorStc::class, 'index'])->name('category.index');
        Route::post('/', [CatAndAuthorStc::class, 'store'])->name('category.store');
        Route::get('/show', [CatAndAuthorStc::class, 'show'])->name('category.show');
        Route::put('/{id}', [CatAndAuthorStc::class, 'update'])->name('category.update');
        Route::delete('/{id}', [CatAndAuthorStc::class, 'destroy'])->name('category.destroy');
    });

    Route::prefix('author')->group(function () {
        Route::get('/', [CatAndAuthorStc::class, 'index'])->name('author.index');
        Route::post('/', [CatAndAuthorStc::class, 'store'])->name('author.store');
        Route::get('/show', [CatAndAuthorStc::class, 'show'])->name('author.show');
        Route::put('/{id}', [CatAndAuthorStc::class, 'update'])->name('author.update');
        Route::delete('/{id}', [CatAndAuthorStc::class, 'destroy'])->name('author.destroy');
    });

    Route::get('/books-search', [BookController::class, 'search'])->name('books.search');

    Route::post('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/orders/history', [OrderController::class, 'orderHistory'])->name('orders.history');

    Route::post('/pay-checkout', [PaymentController::class, 'processPayment'])->name('payment.processPayment');
    Route::post('/payment/confirm', [PaymentController::class, 'confirmOrder'])->name('payment.confirmOrder');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/lastOrder/show', [OrderController::class, 'show'])->name('orders.showLast');
    Route::put('/orders/{id}', [OrderController::class, 'updateOrderStatus'])->name('orders.updateStatus');

    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'addToCart'])->name('cart.addToCart');
        Route::get('/', [CartController::class, 'viewCart'])->name('cart.viewCart');
        Route::put('/update/{id}', [CartController::class, 'updateCart'])->name('cart.updateCart');
        Route::delete('/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.removeFromCart');
    });

    Route::prefix('shipping')->group(function () {
        Route::post('/create', [ShippingAddController::class, 'createShipping'])->name('shipping.createShipping');
        Route::get('/', [ShippingAddController::class, 'viewShipping'])->name('shipping.viewShipping');
        Route::put('/update/{id}', [ShippingAddController::class, 'updateShipping'])->name('shipping.updateShipping');
        Route::delete('/remove/{id}', [ShippingAddController::class, 'removeShipping'])->name('shipping.removeShipping');
    });

    Route::post('books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::put('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

});
