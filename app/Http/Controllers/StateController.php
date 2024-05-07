<?php

namespace App\Http\Controllers;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\StateResource;
class StateController extends Controller
{
    use GeneralTrait;
    public function indexByCountry()
{  $validate = Validator::make($request->all(),[

    'country_uuid'=>'string|exists:countries,uuid',]);
    
    if($validate->fails()){

    return $this->requiredField($validate->errors()->first()); 
       
    }
    $country=Category::where('uuid',$request->country_uuid)->first();
   $states= State::where('country_code',$request->country->code)->get();
   $states=StateResource::collection($states);
   return $this->apiResponse($states) ;  

}
}
