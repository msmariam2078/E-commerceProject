<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:sanctum',App\Http\Middleware\AdminMiddleware::class])->group(function () {
   
    Route::post('/add/products', [App\Http\Controllers\ProductController::class, 'store']);
    Route::post('/edit/products/{id}', [App\Http\Controllers\ProductController::class, 'update']);
    Route::get('/remove/product/{id}', [App\Http\Controllers\ProductController::class, 'destroy']);
    Route::get('/remove/order', [App\Http\Controllers\ProductController::class, 'destroy']);
    Route::get('/edit/statu/order', [App\Http\Controllers\ProductController::class, 'updsteStatu']);
    
   
});
Route::post('/view/products', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/viewOne/product/{id}', [App\Http\Controllers\ProductController::class, 'show']);

Route::middleware(["auth:sanctum"])->group(function () {
  
    //cart_routes
    Route::post('/add/cart', [App\Http\Controllers\Cart_itemController::class, 'store']);   
    Route::get('/show/cart', [App\Http\Controllers\Cart_itemController::class, 'show']); 
    Route::get('/view/cart', [App\Http\Controllers\Cart_itemController::class, 'index']);   
    Route::post('/update/cart', [App\Http\Controllers\Cart_itemController::class, 'updateQuantity']);   
    Route::get('/remove/cart', [App\Http\Controllers\Cart_itemController::class, 'delete']);
     //customer_routes
   
    Route::get('/view/customer', [App\Http\Controllers\CustomerController::class, 'show']);
    Route::post('/update/profile', [App\Http\Controllers\CustomerController::class, 'store']);
  //order_routes
  Route::get('/add/order', [App\Http\Controllers\OrderController::class, 'store']);
  Route::get('/cancel/order', [App\Http\Controllers\OrderController::class, 'destroy']);
});
    //->middleware('auth:sanctum');

    
Route::get('/ff', [App\Http\Controllers\ProductController::class, 'ff']);
Route::get('/cc', [App\Http\Controllers\ProductController::class, 'cc']);
Route::post('/rigester', [App\Http\Controllers\AuthController::class, 'rigester']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);