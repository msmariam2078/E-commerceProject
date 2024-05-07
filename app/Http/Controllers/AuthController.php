<?php

namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;
use Session;
use App\Models\Customer;
use Illuminate\Support\Facades\Cookie;
use App\Models\Cart_item;
use App\Models\State;
use App\Models\Password_reset;
use App\Custom\CartManage;
use Illuminate\Support\Arr;
use App\Models\Customer_address;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GeneralTrait; 
use Laravel\Sanctum\HasApiTokens;
use App\Http\Traits\FileUploader;
use App\Custom\Verifiedmail;
class AuthController extends Controller
{
   use GeneralTrait;


    public function rigester(Request $request){
    
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|ends_with:yahoo.com,hotmail.com,gmail.com|unique:users,email',
            'password'=>'required|string|min:2|max:30|confirmed',
            'password_confirmation'=>'required|string|min:2|max:30',
            //|in:'.$request->password,
            'first_name' => 'required|string|min:2|max:20',
            'last_name' => 'required|string|min:2|max:30',
            'phone' => 'required|string|max:20',
            'shipping'=>'required|array',
           'shipping.*address1'=>'required|string|min:7|max:100',
           'shipping.*address2'=>'required|string|min:7|max:100',
            'shipping.*country_code'=>'required|string|exists:countries,code',
           'shipping.*state_uuid'=>'required|in:'.implode(',',State::where('country_code',$request->shipping['country_code'])->pluck('uuid')->toArray()),
          'billing'=>'required|array',
            'billing.*address1'=>'required|string|min:7|max:100',
            'billing.*address2'=>'required|string|min:7|max:100',
              'billing.*country_code'=>'required|string|exists:countries,code',
             'billing.*state_uuid'=>'required|in:'.implode(',',State::where('country_code',$request->billing['country_code'])->pluck('uuid')->toArray())
             ]);
             if($validator->fails()){
              return $this->requiredField($validator->errors()->first());    
              }
    
      
              $user=User::firstOrCreate([
                'uuid'=>Str::uuid(),
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
              ]);
   
 
    
          
          
             $customer=Customer::FirstOrCreate([
              'uuid'=>Str::uuid(),
              'user_id'=>$user->id,
              'first_name'=>$request->first_name,
              'last_name'=>$request->last_name,
              'phone'=>$request->phone
            
             ]);
             $shippingState=State::where('uuid',$request->shipping['state_uuid'])->first();
             $billingState=State::where('uuid',$request->billing['state_uuid'])->first();
             $shippingAddress=Customer_address::FirstOrCreate([
              'uuid'=>Str::uuid(),
              'customer_id'=>$customer->id,
              'address1'=>$request->shipping['address1'],
              'address2'=>$request->shipping['address2'],
              'country_code'=>$request->shipping['country_code'],
              'state_id'=>$shippingState->id,
            'type'=>'shipping'
             ]);
             $billingAddress=Customer_address::FirstOrCreate([
              'uuid'=>Str::uuid(),
              'customer_id'=>$customer->id,
              'address1'=>$request->billing['address1'],
              'address2'=>$request->billing['address2'],
              'country_code'=>$request->shipping['country_code'],
              'state_id'=>$billingState->id,
            'type'=>'billing'
             ]);
             
             $verifiedmail=new Verifiedmail();
             $token=$verifiedmail->SendVerify($user->email);
            $user->verify_token=$token;
            $user->save();
            $request->session()->put('email',$user->email);
            return $this->apiResponse('account has brrn registed, please check your email to verify!!',true,null,201);
            
            }

 




              public function logout(Request $request)
              {
           
               Auth::logout();
               return $this->apiResponse("loggedout successfully!",true,null,201);
     
              }
  

              public function login(Request $request,CartManage $cartManage){

                $validator = Validator::make($request->all(),[

                'email' => 'required|email|exists:users,email',

                'password' => 'required|string|min:2|max:30|',
            
        
            ]);
        
            if($validator->fails()){
              return $this->requiredField($validator->errors()->first());    
              }
        
            $remember_me = $request->has('remember') ? true : false;
        
        
        
            if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $remember_me))
        
            {   
        
                $user = auth()->user();
                $cartManage->MoveItemToDb($user);
           
        
               
            
             
               return $this->apiResponse('you logged in successfully!',true,null,201)->cookie('cart',"[]");
        
            }else{
        
                return $this->requiredField('email or password  uncorrect!');
        
            }
        



              }


              public function destroy()
              {   

                $user=Auth::id();

                $user=User::findOrFail($user); 
                
                $user->delete();
                return $this->apiResponse("deleted successfully!");
             
              }
              

            public function verified(Request $request)
            {
              $user=User::where('verify_token',$request->code)->first();
              if(!$user)
              {
                return $this->requiredField('uncorrect code ');
              }
              $user->is_verified=true;
              $user->email_verified_at=Carbon::now();
              $user->verify_token='';
              $user->save();
              return $this->apiResponse('your account is verified successfuly!!');
            }




  public function resend(Request $request)
  {

    if ($request->session()->has('email')) {
      $user_email = $request->session()->get('email');

      $user=User::where('email',$user_email)->get();
      
      if($user)
     { $verifiedmail=new Verifiedmail();
  
      $verifiedmail->SendVerify($user_email);
      return $this->apiResponse('the code has been resend');
    }
      else{
        return $this->notFoundResponse('no such user ');
      }
  }
  return $this->notFoundResponse('cant send email ');


  }


  public function resetPassword(Request $request)
  {

    $validator = Validator::make($request->all(),[

      

      'old_password' => 'required|string|min:2|max:30',
      'new_password' => 'required|string|min:2|max:30',
      'repeat_password' => 'required|string|min:2|max:30'
  ]);

  if($validator->fails()){
    return $this->requiredField($validator->errors()->first());    
    }
    $user=Auth::user();
   $new_password=$request->new_password;
   $old_password=$request->old_password;
   $repeat_password=$request->repeat_password;
   if($new_password!=$repeat_password)
   return $this->requiredField(' password  desmatch!'); 
  
   if(Hash::check($old_password,$user->password))
   {
    $user->password=Hash::make($new_password);
    $user->save();
    return $this->apiResponse(' passsword has been changed'); 
   }
   else{
    return $this->requiredField(' incorrect passsword'); 
   }
  }

  public function forgetPassword(Request $request){


    $validator = Validator::make($request->all(),[

      'email' => 'required|email|exists:users,email',

   
      
  

  ]);

  if($validator->fails()){
    return $this->requiredField($validator->errors()->first());    
    }

  $user=User::where('email',$request->email)->first();
  
    $verifiedmail=new Verifiedmail();
   
    $token=$verifiedmail->SendVerify($user->email);
   $record= Password_reset::where('email',$request->email)->first();
   if($record)
   $record->update(['token'=>$token]);
   else 
  Password_reset::create(['email'=>$request->email,'token'=>$token]);

   return $this->apiResponse(' the code to reset password has been sent to your email!!',true,null,201);
  




  }

  public function verifyResetPassword(Request $request)
  {
    $record=Password_reset::where('token',$request->code)->first();
    if(!$record)
    {
      return $this->requiredField('uncorrect code ');
    }
    $record->update(['token'=>'']);
 
    
   
    return $this->apiResponse('your account is verified successfuly!!');
  }
            }
                
