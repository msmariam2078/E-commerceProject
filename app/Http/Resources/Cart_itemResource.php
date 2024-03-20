<?php

namespace App\Http\Resources;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Cart_itemResource extends JsonResource
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
       "name"=>$this->product->title,
       "price"=>$this->product->price,
       "description"=>$this->product->desc,
       "quantity"=>$this->quantity,
    //    "total_cost"=>UserResource::make($this->user),

        ];
    }
}
