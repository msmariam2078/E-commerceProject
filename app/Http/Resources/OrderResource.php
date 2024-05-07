<?php

namespace App\Http\Resources;
use App\Http\Resources\Order_productResource;
use App\Http\Resources\CustomerAddressResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'full name'=>$this->user->customer->full,
            
            'status'=>$this->status,
            'subtotal'=>$this->total,
            'order_items'=>Order_productResource::collection($this->order_products),
            
            'addresses'=>CustomerAddressResource::collection($this->user->customer->addresses)

        ];
    }
}
