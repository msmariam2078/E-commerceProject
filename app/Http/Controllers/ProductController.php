<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\Response;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use App\Http\Traits\GeneralTrait; 
//use App\Http\Traits\FileUploader;
class ProductController extends Controller
{
    use GeneralTrait;

public function index(Request $request)
{

    $validate = Validator::make($request->all(),[
        'search' => 'string|min:2|max:100',
        'sortedBy' => 'string|in:title,price,image,desc,status,created_at,updated_at',
        'sortedD' => 'in:asc,desc|string',
      
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
       $sortedBy=request('sortedBy','updated_at');
       $sortedDirection=request('sortedD','desc');
       $search=request('search','');

       $products=Product::query()
       ->where('title','like','%'.$search.'%')
       ->orderBy($sortedBy,$sortedDirection)->get();

       $products=ProductResource::collection($products);
     return $this->apiResponse($products);
}



public function store(Request $request)
{
    
    $validate = Validator::make($request->all(),[
    'title' => 'string|min:2|max:20',
    'desc' => 'string|min:7|max:100',
    'image' => 'string|max:2000',
    "status"=>'string|in:availble,not_availble',
    'price'=>'min:1,max:1000'
    ]);
    if($validate->fails()){
    return $this->requiredField($validate->errors()->first());    
    }
    try{
    // $image=$this->uploadImagePublic2($request,'product','image');
    // if(!$image)
    // {
    // return  $this->apiResponse(null, false,['Failed to upload image'],500); 
    //  }

   

    $product=Product::firstOrCreate([
    'title'=>$request->title,
    'status'=>$request->status,
    'image'=>$request->image,
    'price'=>$request->price,

]);

$product=ProductResource::make($product);
    
    return $this->apiResponse($product) ;  
    
   


} catch (\Throwable $th) {
  
    return $this->apiResponse(null,false,$th->getMessage(),500);
    }
}


public function update(Request $request,$id)
    {  $validate = Validator::make($request->all(),[
     
        'title' => 'string|min:2|max:20',
        'desc' => 'string|min:7|max:100',
       // 'image' => 'string|max:2000',
        "status"=>'string|in:availble,not_availble',
        'price'=>'min:1,max:1000'
    
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
        try{
        $product=Product::findOrFail($id);
        
        $data=$request->all();
      
          $image=$data['image']??null;  
        if($image) {
       
        $data['image']=$this->uploadImagePublic2($request,'products','image');
        if(!$data['image']){
        return  $this->apiResponse(null, false,['Failed to upload image'],500);
        }
    }

        if($product->image) {
        $this->deleteFile($product->image);
        }
        $product->update($data);
   
    return  $this->apiResponse( ['updated successfuly']);
    
    }

    catch (\Throwable $th) {
      
        return $this->apiResponse(null,false,$th->getMessage(),500);
        }
}

public function destroy( $id)
{   
   $product=Product::findOrFail($id); 
  
   $product->delete();
   return $this->apiResponse(["deleted successfully!"]);
  // return response()->noContent();
}

    public function ff()
    {  //  $cart_items= Cookie::queue(Cookie::make('car', 'hi', 23));
        return response('')->cookie(cookie('nn','[]', 33));

    // $product=Product::all();
    // $c=Arr::keyBy($product,'price');
    // return $c;
    }
 public function cc(Request $request)
    {    $f=json_decode($request->cookie('bb')) ;
        dd( $f);
       // Cookie::queue(Cookie::forget('cart_item'));
      // $value=json_decode( request()->cookie("ss"));
//$value =request()->cookie('car');
//         $response = new Response('Hello World');
// $response->withCookie(cookie('cart','hi',23));

dd( $value);
    }
public function show($id){
    $product=Product::findOrFail($id);
   
    $product=new ProductResource($product);
     return $this->apiResponse($product);
     }















}
