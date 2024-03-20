<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Cart_item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use App\Http\Resources\Cart_itemResource;
use App\Http\Resources\User_cartResource;
use App\Http\traits\GeneralTrait;

class Cart_itemController extends Controller
{
     use GeneralTrait;

     public function index ()
   {

    $user=Auth::user();
    $product=User_cartResource::make($user);
   return $this->apiResponse ($product);

     }
     public function show (Request $request)
     {
      $validate = Validator::make($request->all(),[
        'product_id' => 'required|exists:cart_items,id',
      
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
  
      $user=Auth::user();
      $cart_item=Cart_item::find($request->product_id);
      //dd($cart_item);
      $cart_item=Cart_itemResource::make($cart_item);
     return $this->apiResponse ($cart_item);
  
       }


    public function store(Request $request)
  
    {   


      $validate = Validator::make($request->all(),[
        'product_id' => 'required|exists:products,id',
      
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
        $user=Auth::user(); 
       
        if($user) 
       { $cart_item=Cart_item::where(['user_id'=>$user->id,'product_id'=>$request->product_id])
        ->first();
     
       if($cart_item){
       $cart_item->quantity+=$request->quantity;
       $cart_item->save();
       return $this->apiResponse(Cart_item::where('user_id',$user->id)->sum('quantity'));
       }
       else{
      $cart_item=Cart_item::create([
     'user_id'=>$user->id,
     'product_id'=>$request->product_id,
     'quantity'=>$request->quantity

     ]);
     return $this->apiResponse(Cart_item::where('user_id',$user->id)->sum('quantity'));
    }}
  //  else{

  // $cart_items=json_decode($request->cookie('bb',"[]"),true) ;
  // $found=false;
  //  foreach($cart_items as $item)
  //   { 
  
  
  //  if($item['product_id']==$request->product_id){
  //   $item['quantity']+=$request->quantity;
  //   $found=true;
  //  break;
  //  }
   
  // 
    }
   public function updateQuantity(Request $request){
    $validate = Validator::make($request->all(),[
      'product_id' => 'required|exists:cart_items,id',
    
      ]);
      if($validate->fails()){
      return $this->requiredField($validate->errors()->first());    
      }
    $user=Auth::user();
    if($user)
  { $product= Cart_item::where(['user_id'=>$user->id,'id'=>$request->product_id])
                      ->update(['quantity'=>$request->quantity]);}

    return $this->apiResponse(['updated successfuly..']) ;

   }
   public function delete(Request $request){
    $user=Auth::user();
    if($user)
  { $product= Cart_item::where(['user_id'=>$user->id,'product_id'=>$request->product_id])->first();
    if($product)
    {
    $product->delete();
    
    return $this->apiResponse(['updated successfuly..']) ;
    }
  
    


  }



   }



     }





