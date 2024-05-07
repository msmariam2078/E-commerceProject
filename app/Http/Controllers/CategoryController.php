<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploader;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use GeneralTrait,FileUploader;


  public function index()
    {

$categories=Category::all();
$categories=CategoryResource::collection($categories);

return $this->apiResponse ($categories);

    }




    public function show(Request $request)
    {

      $validate = Validator::make($request->all(),[

        'uuid'=>'required|string|exists:categories,uuid',]);
        
        if($validate->fails()){
    
        return $this->requiredField($validate->errors()->first()); 
           
        }
      $category=Category::where('uuid',$request->category_uuid)->first();
      $category=CategoryResource::make($category);
        return $this->apiResponse ($category);
      
    
     
    }
     

    public function search(Request $request)
    {
        $validate = Validator::make($request->all(),[
           'uuid'=>'required|string|exists:categories,uuid',
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


        $category=Category::where('uuid',$request->uuid)->first();
        $products=Product::where('category_id',$category->id)->where('title','like','%'.$search.'%')
        ->orderBy($sortedBy,$sortedDirection)->get();
        
        $products=ProductResource::collection($products);
         return $this->apiResponse($products);

       }



         public function store(Request $request)
     
         {
          $validate = Validator::make($request->all(),[
          
            'name' => ' required|string|min:2|max:20|unique:categories',
            'image' => 'required |file|max:2000|mimes:jpg,png,jpeg,jfif',
            'desc' => 'required |string|min:7|max:100'
        
            ]);
            if($validate->fails()){
            return $this->requiredField($validate->errors()->first());    
            }
          try{

           $image=$this->uploadImagePublic2($request,'category','image');
           if(!$image)
          {
          return  $this->apiResponse(null, false,'Failed to upload image',500); 
          }
          $category=Category::firstOrCreate([
            'uuid'=>Str::uuid(),
            'name'=>$request->name,
            'image'=>$image,
            'desc'=>$request->descreption,
              ]);
     
         $category=CategoryResource::make($category);
         
         return $this->apiResponse($category) ;
           }catch (\Throwable $th) {
  
              return $this->apiResponse(null,false,$th->getMessage(),500);
              }

         }
       

         public function update (Request $request,$uuid)
         
      { 
      {
        $validate = Validator::make($request->all(),[
          'name' => ' string|min:2|max:20|unique:categories',
          'image' => 'file|max:2000|mimes:jpg,png,jpeg,jfif',
          'desc' => 'string|min:7|max:100'
      
          ]);
          if($validate->fails()){
          return $this->requiredField($validate->errors()->first());    
          }}
       try{
      
        $category=Category::where('uuid',$uuid)->first();

        if(!$category)
        {
           return $this->apiResponse('Category not available');
        }
         $data=$request->all();
   
         $image=$data['image']??null;  
        

         if($image) {
       
        $data['image']=$this->uploadImagePublic2($request,'category','image');
        if(!$data['image']){
        return  $this->apiResponse(null, false,'Failed to upload image',500);
        }
        if($category->image) {
            $this->deleteFile($category->image);
            }
            
         }

         $category->update($data);
    
         return  $this->apiResponse( 'updated successfuly');

         } 
         catch (\Throwable $th) {
  
    return $this->apiResponse(null,false,$th->getMessage(),500);
    }  
}



}