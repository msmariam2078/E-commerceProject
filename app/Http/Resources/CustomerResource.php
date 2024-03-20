<?php

namespace App\Http\Resources;
use App\Http\Resources\CustomerAddressResource;
use Illuminate\Http\Resources\Json\JsonResource;
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user_name'=>$this->user_name,
            "first_name"=>$this->first_name,
            "lastt_name"=>$this->lastt_name,
            "email"=>$this->user->email,
       
            "phone_namber"=>$this->phone,
            'addresses'=>CustomerAddressResource::collection($this->addresses)

        ];
    }
}
