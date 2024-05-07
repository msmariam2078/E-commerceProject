<?php

namespace App\Http\Controllers;
use App\Models\State;
use App\Models\customer;
use App\Models\Customer_address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Validator;
class CustomerController extends Controller
{
 use GeneralTrait;
public function show(Request $request)

{
$user=Auth::user();
//dd($user);
$customer=$user->customer;
$customer=CustomerResource::make($customer);

return $this->apiResponse($customer) ;  
    
}



public function index()
{

   $customers= Customer::all();
   $customers=$customer=CustomerResource::collection($customers);
   return $this->apiResponse($customers) ;  

}

public function active()
{
   $customers=Customer::where('active',true)->get()->count();
   $data['active_customers_count']=$customers;
   return $this->apiResponse($data) ; 
}





public function update(Request $request)
{
   
    $validate = Validator::make($request->all(),[
    'first_name' => 'string|min:2|max:20',
    'last_name' => 'string|min:2|max:30',

    'phone' => 'string|max:20',
    'shipping'=>'array',
    'shipping.*address1'=>'string|min:7|max:100',
    'shipping.*address2'=>'string|min:7|max:100',
     'shipping.*country_code'=>'string|exists:countries,code',
    'shipping.*state_uuid'=>'string|exists:states,uuid',
    
     'billing.*address1'=>'string|min:7|max:100',
     'billing.*address2'=>'string|min:7|max:100',
      'billing.*country_code'=>'string|exists:countries,code',
      'billing.*state_uuid'=>'string|exists:states,uuid'
     ]);
    if($validate->fails()){
    return $this->requiredField($validate->errors()->first());    
    }
   // try{
  $user=Auth::user();

  $customer=$user->customer;
  
  $customer->update($request->only('first_name','last_name','phone'));
  $shipping_address=Customer_address::where('type','shipping')
                                    ->where('customer_id',$customer->id)
                                    ->first();
  $billing_address=Customer_address::where('type','billing')
                                    ->where('customer_id',$customer->id)
                                    ->first();

if($request->shipping)
  {
    $shipping_data=$request->shipping;
$country_code=$shipping_data['country_code']??$shipping_address->country_code; 
$state_uuid=$shipping_data['state_uuid']??null;
if($state_uuid){
$state_id=State::where('uuid',$shipping_data['state_uuid'])->value('id'); 
}
else{
  $state_id= $shipping_address->state_id;
}
$states=State::where('country_code',$country_code)->pluck('id')->toArray();
if(in_array($state_id,$states))
  { 
    $shipping_data['state_id']=$state_id;
    $shipping_address->update($shipping_data);
  }
   else  return $this->requiredField('invalid state or country code');  
 
  }

 

if($request->billing) 

{  $billing_data=$request->billing;
  $state_uuid=$billing_data['state_uuid']??null;
if($state_uuid){
$state_id=State::where('uuid',$billing_data['state_uuid'])->value('id'); 
}
else{
  $state_id= $billing_address->state_id;
}
$country_code=$billing_data['country_code']??$billing_address->country_code; 

$states=State::where('country_code',$country_code)->pluck('id')->toArray();
if(in_array($state_id,$states))
  { 
    $billing_data['state_id']=$state_id;
    $billing_address->update($billing_data);
    
  
  }
   else  return $this->requiredField('invalid state or country code');  
}


$customer=CustomerResource::make($customer);
    
return $this->apiResponse($customer) ;  
    
   


// } catch (\Throwable $th) {
  
//     return $this->apiResponse(null,false,$th->getMessage(),500);
//     }
 }







}
