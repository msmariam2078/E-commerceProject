<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_product extends Model
{
    use HasFactory;


    protected $fillable=[
        'uuid',
        "order_id",
        "product_id",
        "quantity"
       
        ];
        
    //     protected $casts = [
    //         'product_id' => 'integer',
    //         'order_id' => 'integer',
    //         'quantity'=>"integer"
           
    //  ];

     public function order()
     {
         return $this->belongsTo(Order::class);
     }
 

     public function product()
     {
         return $this->belongsTo(Product::class);
     }



}
