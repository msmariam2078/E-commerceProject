<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Cart_item;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Custom\CartManage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use App\Http\Resources\Cart_itemResource;
use App\Http\Resources\User_cartResource;
use App\Http\traits\GeneralTrait;

class Cart_itemController extends Controller
{
     use GeneralTrait;

     public function index (Request $request)
   {

    $user=auth('sanctum')->user();
 
    $cart=User_cartResource::make($user);
   return $this->apiResponse ($cart);
  

     }


     public function show (Request $request)
     {
      $validate = Validator::make($request->all(),[
        'product_uuid' => 'required|string|exists:products,uuid',
      
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
  
        $user=auth('sanctum')->user();
        $product=Product::where('uuid',$request->product_uuid)->first();
        if($user)
    { 
       $cart_item=Cart_item::where(['user_id'=>$user->id,'product_id'=>$product->id])->first();
       $cart_item=Cart_itemResource::make($cart_item);

        return $this->apiResponse ($cart_item);
     
       }

      }


    public function store(Request $request)
  
    {   


      $validate = Validator::make($request->all(),[
        'product_uuid' => 'required|exists:products,uuid',
      
        ],['product_uuid.exists'=>'this product dosent exists']);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
       
       $product=Product::where('uuid',$request->product_uuid)->first();
       $user=Auth::user();
       
       $cart_item=Cart_item::where(['user_id'=>$user->id,'product_id'=>$product->id])
       ->first();
    
      if($cart_item){
      $cart_item->quantity+=$request->quantity;
      $cart_item->save();
      return $this->apiResponse('the item has been added');
   
      }
      else{
      
     $cart_item=Cart_item::create([
     'uuid'=>Str::uuid(),
    'user_id'=>$user->id,
    'product_id'=>$product->id,
    'quantity'=>$request->quantity

    ]);
    return $this->apiResponse('the item has been added');
   }

    }


     





   public function updateQuantity(Request $request){
    $validate = Validator::make($request->all(),[
      'product_uuid' => 'required|exists:products,uuid',
      'quantity' => 'required|min:1|max:1000',
      ]);
      if($validate->fails()){
      return $this->requiredField($validate->errors()->first());    
      }
      $user=Auth::user();
      $product=Product::where('uuid',$request->product_uuid)->first();
    
   
      $cart_updated= Cart_item::where(['user_id'=>$user->id,'product_id'=>$product->id])->first();
     
      if( $cart_updated)
      {$cart_updated->update(['quantity'=>$request->quantity]);
        return $this->apiResponse('updated successfuly..');
      }
      else return $this->apiResponse('this item doest exist') ;

   
   


}




   public function delete(Request $request){
   
    $validate = Validator::make($request->all(),[
      'product_uuid' => 'required|exists:products,uuid',
    
      ]);
      if($validate->fails()){
      return $this->requiredField($validate->errors()->first());    
      }

      $product=Product::where('uuid',$request->product_uuid)->value("id");
      $user=Auth::user();
     $query= Cart_item::where(['user_id'=>$user->id,'product_id'=>$product])->first();
    if($query)
    {
    $query->delete();
    
    return $this->apiResponse('deleted successfuly..') ;
    }
    else{
      return $this->apiResponse('this item doest exist') ;
    }




  



   



     





  }}