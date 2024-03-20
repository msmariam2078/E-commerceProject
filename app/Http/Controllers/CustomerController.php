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


public function store(Request $request)
{
    
    $validate = Validator::make($request->all(),[
    'first_name' => 'string|min:2|max:20',
    'last_name' => 'string|min:2|max:30',

    'phone' => 'string|max:20',
    'shipping'=>'array',
    'shipping.*.address1'=>'string|min:7|max:100',
    'shipping.*.address2'=>'string|min:7|max:100',
     'shipping.*.country_code'=>'string|exists:countries,code',
     'shipping.*.state_id'=>'string|in:'.implode(',',State::where('country_code',$request->country_code)->pluck('id')->toArray()),
    
     'billing.*.address1'=>'string|min:7|max:100',
     'billing.*.address2'=>'string|min:7|max:100',
      'billing.*.country_code'=>'string|exists:countries,code',
      //'billing_state'=>'string|in:'
      //.implode(',',State::where('country_code',$request->country_code)->pluck('id')->toArray())
     ]);
    if($validate->fails()){
    return $this->requiredField($validate->errors()->first());    
    }
    try{
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
    $shipping_address->update($request->shipping);
 }

if($request->biipping) 
{
$billing_address->update($request->billing);
}


$customer=CustomerResource::make($customer);
    
return $this->apiResponse($customer) ;  
    
   


} catch (\Throwable $th) {
  
    return $this->apiResponse(null,false,$th->getMessage(),500);
    }
 }

}
