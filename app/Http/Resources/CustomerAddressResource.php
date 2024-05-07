<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
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
            'uuid'=>$this->uuid,
            'type'=>$this->type,
            'address1'=>$this->address1,
            'address2'=>$this->address2,
            'type'=>$this->type,
            'country_code'=>$this->country_code,
            'state'=>$this->state->name??null
        ];
    }
}
