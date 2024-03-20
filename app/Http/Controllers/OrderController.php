<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\traits\GeneralTrait;
use Carbon\Carbon;
use App\Models\Order_product;
use App\Models\Cart_item;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
     use GeneralTrait;

       //..... add order

         public function store(Request $request)

         {

             $user=Auth::user();
             
           $cart_items=$user->cart_items;

              $order=Order::create([

               "user_id"=>$user->id,
                "status"=>"unpaid",
                "subtotal"=>$user->cost

                   ]);

             foreach($cart_items as $cart_item)
             {

            Order_product::create([
             "order_id"=>$order->id,
             "product_id"=>$cart_item->product_id,
              "quantity"=>$cart_item->quantity

              ]); }
          $user->cart_items()->delete();
          $data[]="the order has added successfuly!..";
           return $this->apiResponse ($data);

           }

        
        public function index()
        {
         $user=Auth::user();
         $orders=$user->orders;

        }
        
        

         //......Delete.....


        public function cancel(Request $request){
        $user=Auth::user();
       $order=Order::where(['user_id'=>$user->id,'id'=>$request->order])->first();
      
        if (!$order)
        {
          $data[]="the request is not found";
          return $this->apiResponse ($data);
        }
        $order_time= $order->created_at->addDays(1);

        if(Carbon::now() <= $order_time&& $order->type!='shipped')
        {
         $order->type='canceled';
         $order->save();
         $order->order_products()->delete();
                
         $data[]="the order has been canceled ";
         return $this->apiResponse ($data);
        
        
        }
        else{
            $data[]=" time has passed";
            return $this->unAuthorizeResponse($data);

         }

         }


         public function destroy(Request $request)
         {
          $order=Order::findOrFail($request->order);
         $order->delete();
         $data[]="the order has been deleted ";
         return $this->apiResponse ($data);
         }


         
         public function updateStatu(Request $request)
         {
          $order=Order::findOrFail($request->order);
          $order->type=$request->type;
          $order->save();
         
         $data[]="the order has been deleted ";
         return $this->apiResponse ($data);
         }
     
      }



