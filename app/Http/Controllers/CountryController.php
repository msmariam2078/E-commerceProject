<?php

namespace App\Http\Controllers;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;
use App\Http\Resources\CountryResource;
class CountryController extends Controller
{
    use GeneralTrait;
    public function index()
{

   $countries= Country::all();
   $countries=$customer=CountryResource::collection($countries);
   return $this->apiResponse($countries) ;  

}




}
