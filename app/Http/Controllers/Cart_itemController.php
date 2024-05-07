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

     public function index (Request $request,CartManage $cartManage)
   {

    $user=auth('sanctum')->user();
    if($user){
    $cart=User_cartResource::make($user);
   return $this->apiResponse ($cart);}
   else{
    return $cartManage->getCartFromCookie();
   
   }

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
    else
     {
      $cart_items=json_decode($request->cookie('cart',"[]"),true) ;
       foreach($cart_items as $item)
       {
        if($item['product_id']==$product->id)
        {
          $product=Product::find($product->id);
          $produc['id']=$product->title;
          $produc['title']=$product->title;
          $produc['price']=$product->price;
          $produc['desc']=$product->desc;
          $produc['quantity']=$item['quantity'];
          return $this->apiResponse ($produc);
        }
       }
     }
       }




    public function store(Request $request,CartManage $cartManage)
  
    {   


      $validate = Validator::make($request->all(),[
        'product_uuid' => 'required|exists:products,uuid',
      
        ],['product_uuid.exists'=>'this product dosent exists']);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
        $user=auth('sanctum')->user();
       $product=Product::where('uuid',$request->product_uuid)->first();
        if($user) 
       {
     return  $cartManage->AddItemToDb($user,$product,$request->quantity);

       }
  else{

    return  $cartManage->AddItemToCookie($product,$request->quantity);

     }


    }


     
//     public function cook(Request $request)
// {//Cookie::queue(Cookie::forget('cart'));
//   $cart_items=Cookie::get('cart') ;
//   dd($cart_items);
  
// }




   public function updateQuantity(Request $request,CartManage $cartManage){
    $validate = Validator::make($request->all(),[
      'product_uuid' => 'required|exists:products,uuid',
      'quantity' => 'required|min:1|max:1000',
      ]);
      if($validate->fails()){
      return $this->requiredField($validate->errors()->first());    
      }
      $user=Auth::user();
      $product=Product::where('uuid',$request->product_uuid)->first();
    
      if($user)
    {
      if($product)
    return $cartManage->updateInDb($user,$product,$request->quantity);

     }

    else{
      return $cartManage->updateInCookie( $product,$request->quantity) ;
      }
   


}




   public function delete(Request $request,CartManage $cartManage){
   
    $validate = Validator::make($request->all(),[
      'product_uuid' => 'required|exists:products,uuid',
    
      ]);
      if($validate->fails()){
      return $this->requiredField($validate->errors()->first());    
      }

      $product=Product::where('uuid',$request->product_uuid)->value("id");
      $user=Auth::user();
      if($user)
   
  { $query= Cart_item::where(['user_id'=>$user->id,'product_id'=>$product])->first();
    if($query)
    {
    $query->delete();
    
    return $this->apiResponse('deleted successfuly..') ;
    }
    else{
      return $this->apiResponse('this item doest exist') ;
    }}



    else{

      $cart_items=json_decode($request->cookie('cart',"[]"),true) ;
  
     $found=false;
 
     if(empty($cart_items))
     return  $this->apiResponse ('cart is empty');
      for($i=0; $i<=count($cart_items) ; $i++)
      {
    
       if($cart_items[$i]['product_id']==$product)
       {
          unset($cart_items[$i]);
        $found=true;
         return $this->apiResponse ('deleted successfully')->cookie('cart',json_encode($cart_items));
       }
      
      }

     if(!$found)

 return $this->apiResponse('this item doest exist') ;

    }
    


  



   



     





  }}