<?php
namespace App\Custom;
use App\Http\traits\GeneralTrait;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart_item;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
class CartManage{
use GeneralTrait;
public function AddItemToCookie(object $product,$quantity)
{

        $cart_items=json_decode(request()->cookie('cart',"[]"),true) ;
      
        $found=false;
       foreach($cart_items as &$item)
        { 
      
      
       if($item['product_id']==$product->id){
        $oquantity= $item['quantity'];
        unset( $item['quantity']);
        $item['quantity']= $oquantity+$quantity;
     
        $found=true;
         break;
         }
         }
       if(!$found)
    {
      $cart_items[]=[
    'product_id'=>$product->id,
    'quantity'=>$quantity
    
    
      ];
     
    }
           

   return $this->apiResponse('the item has been added')->cookie('cart',json_encode($cart_items),60*24*360);
         }  





      public function AddItemToDb(object $user,object $product,$quantity)
      {


        $cart_item=Cart_item::where(['user_id'=>$user->id,'product_id'=>$product->id])
        ->first();
     
       if($cart_item){
       $cart_item->quantity+=$quantity;
       $cart_item->save();
       return $this->apiResponse('the item has been added');
    
       }
       else{
       
      $cart_item=Cart_item::create([
      'uuid'=>Str::uuid(),
     'user_id'=>$user->id,
     'product_id'=>$product->id,
     'quantity'=>$quantity

     ]);
     return $this->apiResponse('the item has been added');
    }


      }



    public function updateInDb(object $user,object $product,$quantity)

   {
     

    $cart_updated= Cart_item::where(['user_id'=>$user->id,'product_id'=>$product->id])->first();
    // dd($cart_updated);
    if( $cart_updated)
    {$cart_updated->update(['quantity'=>$quantity]);
      return $this->apiResponse('updated successfuly..');
    }
    else return $this->apiResponse('this item doest exist') ;
    

    }

    public function updateInCookie(object $product,$quantity)

{

  $cart_items=json_decode(request()->cookie('cart',"[]"),true) ;
  foreach($cart_items as &$item)
  {
   if($item['product_id']==$product->id)
   {
     unset($item['quantity']);
     $item['quantity']=$quantity;
    
     return $this->apiResponse ('updated successfully')->cookie('cart',json_encode($cart_items));
   }
   else{
    return $this->apiResponse ('item doesent exist');
  }

}}

   public function getCartFromCookie(){

    $data=['products'=>[],
  'subtotal'=>0];
 
    $cart_items=json_decode(request()->cookie('cart',"[]"),true) ;
    $cost=0;
   foreach($cart_items as $item)
   {
  $product=Product::find($item['product_id']);
  //dd($product);
  $produc['uuid']=$product->uuid;
  $produc['title']=$product->title;
  $produc['price']=$product->price;
  $produc['desc']=$product->desc;
  $produc['quantity']=$item['quantity'];

  $data['products'][]=$produc;
  $cost+=$product->price*($item['quantity']);
   }
 
   $data['subtotal']=$cost."$";
 
   return $this->apiResponse ($data);
 

   }




   
              public function MoveItemToDb($user)
              {

              $cart_items=json_decode(request()->cookie('cart'),true);
              $found=false;
              if($cart_items)
            {
  
            $db_cartitems=Cart_item::where('user_id',$user->id)->get();
          
            foreach($cart_items as &$cart_item )
            {
              foreach($db_cartitems as $db_cartitem)
              {
               
               if($db_cartitem->product_id==$cart_item['product_id'])
           {
           
            $db_cartitem->quantity= $cart_item['quantity']+$db_cartitem->quantity;
            $db_cartitem->save();
            $found=true;
            continue;
    
  
            }}
        
             
             
            
            }
          if(!$found)
           { Cart_item::create(
              ['uuid'=>Str::uuid(),
              'user_id'=>$user->id,
              'product_id'=>$cart_item['product_id'],
              'quantity'=>$cart_item['quantity']
   
              ]
   
              );
          
            }
          }
  
      }
}
?>