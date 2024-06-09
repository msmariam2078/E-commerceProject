<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Notifications\EmailVerifyNotification;
use Illuminate\Support\Facades\Notification;
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
//  ,,,,,,,admin,,,,,,

Route::middleware(['auth:sanctum',App\Http\Middleware\AdminMiddleware::class])->group(function () {
   //............prouducts

Route::prefix('products/')->controller(App\Http\Controllers\ProductController::class)->group(function(){

   Route::post('add', 'store');
   Route::post('edit', 'update');
   Route::get('remove', 'destroy');
   Route::get('active', 'activeProduct');

});
   
   //.........orders
   Route::prefix('orders/')->controller(App\Http\Controllers\OrderController::class)->group(function(){
      Route::get('paid', 'paidOrder');
      Route::post('views', 'index');
   
   });
 
   //...........customers
   Route::prefix('customers/')->controller(App\Http\Controllers\CustomerController::class)->group(function(){
      Route::get('active', 'active');
      Route::get('view', 'index');
   
   });

});




///////////////////verified user routes...............
Route::middleware(["auth:sanctum",App\Http\Middleware\VerifyMiddleware::class])->group(function () {
  
    //...user


   Route::get('/cancel/user', [App\Http\Controllers\AuthController::class, 'destroy']);
     //customer_routes
   

     //order_routes
   Route::prefix('orders/')->controller(App\Http\Controllers\OrderController::class)->group(function(){
   Route::post('add', 'store');
   Route::get('view', 'show');
   Route::post('view', 'index');
   Route::get('cancel', 'cancel');
   
   });

 //user
 Route::post('/reset/password', [App\Http\Controllers\AuthController::class, 'resetPassword']);
});



//////user routes..
Route::middleware("auth:sanctum")->group(function () {

   Route::get('/view/customer', [App\Http\Controllers\CustomerController::class, 'show']);
   Route::post('/update/profile', [App\Http\Controllers\CustomerController::class, 'update']);
    //cart_routes

 Route::prefix('cart/')->controller(App\Http\Controllers\Cart_itemController::class)->group(function(){
   Route::get('show',  'show');
   Route::post('add',  'store'); 
   Route::post('update',  'updateQuantity');   
   Route::get('remove',  'delete');
   Route::get('view',  'index');
   
   });

   Route::get('/resend',  [App\Http\Controllers\AuthController::class, 'resend'])->middleware("auth:sanctum");
});
   //...user



  
  

 
//--ghaust routes


 


   Route::prefix('products/')->controller(App\Http\Controllers\ProductController::class)->group(function(){
      Route::post('/view', 'index');

      Route::get('/viewOne', 'show');
      
      Route::get('/bestselling', 'best_product');
      
      });




 



  
  

////category...
Route::prefix('categories/')->controller(App\Http\Controllers\CategoryController::class)->group(function(){
   Route::get('viewOne',  'show'); 
   Route::get('view' , 'index'); 
   Route::post('search' , 'search'); 
   
   });

//country
Route::get('/view/countries', [App\Http\Controllers\CountryController::class, 'index']);
//state
Route::get('/view/countries/state', [App\Http\Controllers\StateController::class, 'index']);
//auth
Route::controller(App\Http\Controllers\AuthController::class)->group(function(){
   Route::post('/rigester',  'rigester');
   Route::post('/login',  'login');
   Route::get('/logout',  'logout');
   Route::post('/verified',  'verified');
 
   ///password
   Route::post('/forget/password',  'forgetPassword');
   Route::post('/verify/reset/password',  'verifyResetPassword');
   Route::post('/verify/reset/forgetpassword',  'resetForgetPassword');
});

//payment
Route::get('/checkout', [App\Http\Controllers\PaymentController::class, 'checkout']);
Route::get('/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('success');
Route::get('/cancel', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');





