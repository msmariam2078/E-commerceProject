<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order_product;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use App\Http\Traits\GeneralTrait; 
use App\Http\Traits\FileUploader;

class ProductController extends Controller
{
    use GeneralTrait,FileUploader;



public function index(Request $request)
{

    $validate = Validator::make($request->all(),[
        'search' => 'string|max:100',
        'sortedBy' => 'string|in:title,price,image,desc,status,created_at,updated_at',
        'sortedD' => 'in:asc,desc|string',
      
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
       $sortedBy=request('sortedBy','updated_at');
       $sortedDirection=request('sortedD','desc');
       $search=request('search','');

       $query=Product::query()
       ->where('title','like','%'.$search.'%')
       ->orderBy($sortedBy,$sortedDirection);
       $user= auth('sanctum')->user();
      
       if(!$user||$user&&!$user->is_admin)
       {
        $query->where('status','availble');
       }
       $products=$query->get();
       $products=ProductResource::collection($products);
     return $this->apiResponse($products);
}



public function activeProduct(){

    $products=Product::where('status','availble')->get()->count();

    $data['active_products_count']=$products;   
     return  $this->apiResponse( $data);
     
    

}



public function store(Request $request)
{
    
    $validate = Validator::make($request->all(),[
    'title' => 'required|string|min:2|max:20',
    'descreption' => 'string|min:7|max:2000',
    'image' => 'required|file|max:2000|mimes:jpg,png,jpeg,jfif',
    'images'=>'array|min:1|max:3',
    'images.*image1'=> 'file|max:2000|mimes:jpg,png,jpeg,jfif',
    'images.*image2'=> 'file|max:2000|mimes:jpg,png,jpeg,jfif',
    'images.*image3'=> 'file|max:2000|mimes:jpg,png,jpeg,jfif',
    'category_uuid' => 'required|exists:categories,uuid',
    "status"=>'required|string|in:availble,not_availble',
    'price'=>'required|min:1'
    ]);

    if($validate->fails()){
    return $this->requiredField($validate->errors()->first());    
    }

    try{
    $category =Category::where('uuid',$request->category_uuid)->first();

   
    $image=$this->uploadImagePublic2($request,'product','image');
    if(!$image)
    {
     return  $this->apiResponse(null, false,'Failed to upload image',500); 
     }

     $images =$request->images;
  
     if ($images)
    {
         $urls= $this->uploadMultiImage2($request,'product'); 
     
    }    
    
      $product=Product::firstOrCreate([
       'uuid'=>Str::uuid(),
       'title'=>$request->title,
       'price'=>$request->price,
       'image'=>$image,
       'image1'=>$urls['image1']??"",
       'image2'=>$urls['image2']??"",
       'image3'=>$urls['image3']??"",
       'category_id'=>$category->id,
       'desc'=>$request->descreption,
       'status'=>$request->status,
     
         ]);

   $product=ProductResource::make($product);
    
    return $this->apiResponse($product) ;  
    
   


} 
catch (\Throwable $th) {
  
    return $this->apiResponse(null,false,$th->getMessage(),500);
    }
}





public function update(Request $request)
    {  
        $validate = Validator::make($request->all(),[
        'uuid'=>'string|exists:products,uuid',
        'title' => 'string|min:2|max:20',
        'desc' => 'string|min:7|max:100',
        'image' => 'file|max:2000',
        'images'=>'array|min:1|max:3',
        'image1'=> 'file|max:2000|mimes:jpg,png,jpeg,jfif',
        'image2'=> 'file|max:2000|mimes:jpg,png,jpeg,jfif',
        'image3'=> 'file|max:2000|mimes:jpg,png,jpeg,jfif',
        "status"=>'string|in:availble,not_availble',
        'price'=>'min:1,max:1000',
       
    
        ]);
        if($validate->fails()){
        return $this->requiredField($validate->errors()->first());    
        }
        try{

        $product=Product::where('uuid',$request->uuid)->first();

     
       
         $data=$request->all();
   
         $image=$data['image']??null;  
        

         if($image) {
       
        $data['image']=$this->uploadImagePublic2($request,'product','image');
        if(!$data['image']){
        return  $this->apiResponse(null, false,'Failed to upload image',500);
        }
        if($product->image) {
            $this->deleteFile($product->image);
            }
         }


         if($data['images']) {
       
       foreach($data['images'] as $key=>$image)
     {
    
    switch($key){

     case "image1":
        $this->deleteFile($product->image1);
       $url= $this->uploadImagePublic3($request,'product','images',"image1");
        $data[$key]=$url;

        break;

    case "image2":

            $this->deleteFile($product->image2);
           $url= $this->uploadImagePublic3($request,'product','images',"image2");
           $data[$key]=$url;
            break;

           
      case "image3":

                $this->deleteFile($product->image3);
               $url= $this->uploadImagePublic3($request,'product','images',"image3");
               $data[$key]=$url;
                break;

           }
            
             }


            }
           
 
        $product->update($data);
    
        return  $this->apiResponse( 'updated successfuly');
    
    }

     catch (\Throwable $th) {
      
        return $this->apiResponse(null,false,$th->getMessage(),500);
        }
}








public function destroy( Request $request)
{     $validate = Validator::make($request->all(),[
    'uuid'=>'string|exists:products,uuid',]);
    if($validate->fails()){

    return $this->requiredField($validate->errors()->first()); 

    }
   $product=Product::where('uuid',$request->uuid)->first(); 
 
   $product->delete();
   return $this->apiResponse("deleted successfully!");

}

    
   public function show(Request $request){

   $validate = Validator::make($request->all(),[

    'uuid'=>'required|string|exists:products,uuid',]);
    
    if($validate->fails()){

    return $this->requiredField($validate->errors()->first()); 
       
    }
    $product=Product::where('uuid',$request->uuid)->first();

     $product=new ProductResource($product);
     return $this->apiResponse($product);
     }





    public function best_product(){

    $categories=Category::all()->pluck('id')->toArray();
    $ordered_products=Order_product::all();
    if($ordered_products->isEmpty())
    {
        return $this->apiResponse('np results');
    }
    $products=[];
    foreach($ordered_products as $p) {
        $found=false; 
        foreach($products as &$pro)
        {
            foreach($pro as $k => $i)
        if( $k ==$p->product_id)
     {  $quantity= $pro[$k];
        unset($pro[$k]); 
        $pro[$k]=$p->quantity+ $quantity;
        
        $found=true;
       break;
        }
    }


       if($found==false)
        {   
            foreach($categories as $e){
                $category=Product::find($p->product_id)->category_id;
                if($category==$e)
                {
                if(in_array($e,array_keys($products)))
                {
                 
               $products[$e][$p->product_id]=$p->quantity;
                 
                }    
                else{
                $products[$e]=[
                    $p->product_id=>$p->quantity ];
                }  } }
          
          } }

    $best_selling=[];
    foreach($products as $key => $element)
    {
     $max=max($element);
     $id_product=array_search($max,$element);
   
     
     $best_seller[]= $id_product;
    }
 
 if($best_seller)
    { $products=Product::whereIn("id",$best_seller)->get();
     return   $this->apiResponse($products);
     }
else  return   $this->apiResponse('no results');

}





}