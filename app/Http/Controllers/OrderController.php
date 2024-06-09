<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Http\traits\GeneralTrait;
use Carbon\Carbon;
use Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Order_product;
use App\Models\Cart_item;
use App\Models\State;
use App\Models\Customer_address;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersResource;
use Illuminate\Support\Facades\Auth;
use App\Events\orderEvent;
class OrderController extends Controller
{
     use GeneralTrait;




     public function show(Request $request)
     {  
      $validate = Validator::make($request->all(),[
      'order' => 'required|exists:orders,uuid',
    
      ]);
      if($validate->fails()){
      return $this->requiredField($validate->errors()->first());    
      }
      $order=Order::where("uuid",$request->order)->first();
      
      $order=OrderResource::make($order);
      return $this->apiResponse ($order);

     }

     public function paidOrder(){
      
      $orders=Order::where('status','paid')->get()->count();
      $data['paid_orders_count']=$orders;
      return $this->apiResponse ($data);

     }

     
  
         public function store(Request $request)
         {

          $user=Auth::user();
          if($user->is_admin)
          {
            return $this->apiResponse ('this user is admin');
          }
        
          $cart_items=$user->cart_items;
         
         if($cart_items->isEmpty())
         
         {
               return $this->apiResponse ('there are no items to order');
         }

         $customer=Customer::create([
            'uuid'=>Str::uuid(),
            "user_id"=>$user->id,
            "first_name"=>$request->first_name,
            'last_name'=>$request->last_name,
            'phone'=>$request->phone

         ]);
         $shipping=$request->shipping;
        
         $shipping['type']='shipping';
         $shipping['customer_id']=$customer->id;
         $shipping['uuid']=Str::uuid();
         $shipping['state_id']=State::where('uuid',$request->shipping['state_uuid'])->value('id');
         $shipping_address=Customer_address::create($shipping);
         $billing=$request->billing;
         $billing['customer_id']=$customer->id;
         $billing['uuid']=Str::uuid();
         $billing['state_id']=State::where('uuid',$request->billing['state_uuid'])->value('id');
         $billing_address=Customer_address::create($billing);  







       




            $order=Order::create([
               'uuid'=>Str::uuid(),
               "user_id"=>$user->id,
                "status"=>"unpaid",
                "subtotal"=>$user->cost

                   ]);

             foreach($cart_items as $cart_item)
             {

            Order_product::create([
            'uuid'=>Str::uuid(),
             "order_id"=>$order->id,
            "product_id"=>$cart_item->product_id,
            "quantity"=>$cart_item->quantity

              ]); }
          $user->cart_items()->delete();
          $user->customer->active=true;
          $user->customer->save();
          
         event(new orderEvent($order));
          $data="the order has added successfuly!..";
           return $this->apiResponse ($data);

           }

        
        public function index(Request $request)
        {
         $validate = Validator::make($request->all(),[
            'date' => 'date',
          
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }
         $user= auth('sanctum')->user();
         $query=Order::query();
         $date=$request->date??null;
    
         if($user->is_admin==false)
      
         $query=$query->where('user_id',$user->id);
         
       
        
        if($date)
    { 
      
      $query=$query->whereDate('created_at',$date);
   
    } 
        $orders=$query->orderBy('created_at','desc')->get();
         $orders=OrdersResource::collection($orders);
         return $this->apiResponse ($orders);
      }
         //......Delete.....


        public function cancel(Request $request){
         $validate = Validator::make($request->all(),[
            'order' => 'required|exists:orders,uuid',
          
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }

         $user=Auth::user();
         $order=Order::where(['user_id'=>$user->id,'uuid'=>$request->order])->first();
      
        if (!$order)
        {
          $data="this order in not exist";
          return $this->apiResponse ($data);
        }
        $order_time= $order->created_at->addDays(1);

        if(Carbon::now() <= $order_time&& $order->type!='shipped')
        {
         $order->status='cancelled';

         $order->save();
      
       
                
         $data="the order has been canceled ";
         return $this->apiResponse ($data);
        
        
        }
        else{
            $data=" time has passed";
            return $this->unAuthorizeResponse($data);

         }

         }


         public function destroy(Request $request)
         {

            
       
          $validate = Validator::make($request->all(),[
            'order' => 'required|exists:orders,uuid',
          
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }
            $order=Order::where('uuid',$request->order)->first();
        
          if($order->status=='cancelled')
         {
         $user=$order->user;
        $order->delete();
        $order->order_products()->delete();
   
        if($user->orders->isEmpty()){
        $user->customer->active=false;
        $user->customer->save();
        }
         $data="the order has been deleted ";
         return $this->apiResponse ($data);
      }
         $data="You cannot delete this order ";
         return $this->unAuthorizeResponse ($data);

         }


         
         public function updateStatu(Request $request)
         { $validate = Validator::make($request->all(),[
            'status' => 'required|in:paid,completed,shipped',
           'order'=>'string|exists:orders,uuid'
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());}
          $order=Order::where('uuid',$request->order)->first();
          $order->status=$request->status;
          $order->save();
         
         $data="the order status has updated... ";
         return $this->apiResponse ($data);
         }
     
      }



