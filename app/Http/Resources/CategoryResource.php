<?php

namespace App\Http\Resources;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name'=>$this->name,
            'image'=>$this->image,
            'descreption'=>$this->desc,
            'products'=>ProductResource::collection($this->products)

        ];
    }
}
