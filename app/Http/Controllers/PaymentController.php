<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Stripe\Checkout\Session;
class PaymentController extends Controller
{ 
   
    public function checkout()
    {
    
      \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $products = Product::all();
        $lineItems = [];
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->title,
                        //'images' => [$product->image]
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => 1,
            ];
            
        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
             'success_url' => route('success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
             'cancel_url' => route('cancel', [], true),
        ]);

        dd($session);
        $order = new Order();
        $order->status = 'unpaid';
        $order->total_price = $totalPrice;
        $order->session_id = $session->id;
        $order->save();
       dd($session->url);
        // return redirect($session->url);
    }



}
}