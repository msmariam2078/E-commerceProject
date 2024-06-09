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
            
            'userName' => 'required|unique:users,userName|string|min:2|max:20',
      
      

             ]);
             if($validator->fails()){
              return $this->requiredField($validator->errors()->first());    
              }
    
      
              $user=User::firstOrCreate([
                'uuid'=>Str::uuid(),
                'userName'=>$request->userName,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
              ]);
   
              $token = $user->createToken('MyApp')->plainTextToken;
              $verifiedmail=new Verifiedmail();
              $verifytoken=$verifiedmail->SendVerify($user->email);
            
             $user->verify_token=$verifytoken;
             $user->save();
             $data['massege']='account has beenn registed, please check your email to verify!!';
            $data['token']=$token;
            return $this->apiResponse($data,true,null,201);
            
            }

 




              public function logout(Request $request)
              {
           
               Auth::logout();
               return $this->apiResponse("loggedout successfully!",true,null,201);
     
              }
  

              public function login(Request $request){

                $validator = Validator::make($request->all(),[

                'email' => 'required|email|exists:users,email',

                'password' => 'required|string|min:2|max:30|',
            
        
            ]);
        
            if($validator->fails()){
              return $this->requiredField($validator->errors()->first());    
              }
        
            
        
        
        
            if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')]))
        
            {   
        
                $user=User::where('email',$request->input('email'))->first();
                $token = $user->createToken('MyApp')->plainTextToken;
        
          
             
               return $this->apiResponse($token,true,null,201)->cookie('cart',"[]");
        
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

    
     
      
     $verifiedmail=new Verifiedmail();
  
      $verifiedmail->SendVerify(Auth::user()->email);
      return $this->apiResponse('the code has been resend');
   

    
  



  }


  public function resetPassword(Request $request)
  {

    $validator = Validator::make($request->all(),[

      

      'old_password' => 'required|string|min:2|max:30',
      'password' => 'required|string|min:2|max:30|confirmed',
      'password_confirmation' => 'required|string|min:2|max:30'
  ]);

  if($validator->fails()){
    return $this->requiredField($validator->errors()->first());    
    }
    $user=Auth::user();
   $new_password=$request->password;
   $old_password=$request->old_password;
   $repeat_password=$request->password_confirmation;
 
  
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



  public function resetForgetPassword(Request $request)
  {

    $validator = Validator::make($request->all(),[

      

  
      'password' => 'required|string|min:2|max:30|confirmed',
      'password_confirmation' => 'required|string|min:2|max:30'
  ]);

  if($validator->fails()){
    return $this->requiredField($validator->errors()->first());    
    }
    $user=Auth::user();
   $new_password=$request->password;
   
   $repeat_password=$request->password_confirmation;
 
  
   
    $user->password=Hash::make($new_password);
    $user->save();
    return $this->apiResponse(' passsword has been changed'); 
  
  }






            }
                
