<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Customer;
use App\Models\Customer_address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GeneralTrait; 
use Laravel\Sanctum\HasApiTokens;
use App\Http\Traits\FileUploader;

class AuthController extends Controller
{
   use GeneralTrait;


    public function rigester(Request $request){
      
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
            ]);
            if($validator->fails()){
            return $this->notFoundResponse('notfound');    
            }
           
            $user = User::FirstOrCreate([
            
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
             ]);
             $customer=Customer::FirstOrCreate([
              'user_id'=>$user->id,
            
             ]);
             $shippingAddress=Customer_address::FirstOrCreate([
              'customer_id'=>$customer->id,
            'type'=>'shipping'
             ]);
             $billingAddress=Customer_address::FirstOrCreate([
              'customer_id'=>$customer->id,
            'type'=>'billing'
             ]);
            return $this->apiResponse($user,true,null,201);
            
            }


            public function login(Request $request){
        
                $validator = Validator::make($request->all(),[
         
                  'email' => 'required',
                  'password' => 'required'
                  ]);
                  if($validator->fails()){
                  return $this->requiredField('enter email and password');    
                  }

                  
              $credentials = $request->only('email', 'password','remember');
              $remember= $credentials['remember']?? false;
//               if(auth()->guard('admin')->attempt($credentials))
// {
//                return 1;

              if(!Auth::attempt( $credentials ))
              {

                                 
               return $this->apiResponse(null,false,'password or emal is incorrect',422);

              }
              $user=Auth::user();
             
              // if(! $user->is_admin)
              // {
              //  Auth::logout();
              // return $this->apiResponse(null,false,'you dont have permission to authenticate as admin',403);
              // }
              $token=$user->createToken('auth_token')->plainTextToken;
              return response([
               'user'=>$user,
               'token'=>$token

              ]);
            }

              public function logout()
              {
                $user=Auth::user();
                dd($user);
                $user->tokens()->delete();
              }
  
              public function index(Request $request)
              {
                  // $sortedBy=request('sortedBy','updated_at');
                  // $sortedDirection=request('sortedD','desc');
                  // $search=request('search','');
                   $products=User::all();
                
                   return $this->apiResponse($products);
              }
                
      
}
