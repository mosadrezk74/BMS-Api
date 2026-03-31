<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/', [WebController::class, 'index']);
Route::get('/show/{id}', [WebController::class, 'show'])->name('book.show');


Route::get('cart', [WebController::class, 'viewCart'])->name('cart');
Route::post('checkout', [WebController::class, 'checkout'])->name('checkout');
Route::post('/cart/add/{id}', [WebController::class, 'addToCart'])->name('addToCart');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
