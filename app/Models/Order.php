<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        'uuid',
        "user_id",
        "status",
        "subtotal",
        "session_id"
       
        ];
        protected $appends=['total'];
        
        // protected $casts = [
        //     'user_id' => 'integer',
        //     'subtotal' => 'double',
        //     'status' => 'string',
           
            
           
        // ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function order_products()
        {
            return $this->hasMany(Order_product::class);
        }

        
    public function getTotalAttribute()
    {
    $shipping_cost=20;
     $total=0;
    foreach($this->order_products as $product){
     $total+=($product->product->price)*($product->quantity);

     }
     return $total+$shipping_cost;
    }
}

