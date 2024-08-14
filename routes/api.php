<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthStc;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

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

  Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::group(['prefix' => 'books'], function () {
        Route::get('/', [BookController::class, 'index']);
        Route::post('/', [BookController::class, 'store']);
        Route::get('/{book}', [BookController::class, 'show']);
        Route::put('/{book}', [BookController::class, 'update']);
        Route::delete('/{book}', [BookController::class, 'destroy']);
    });
  });
