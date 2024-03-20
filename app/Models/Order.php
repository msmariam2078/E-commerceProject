<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        "user_id",
        "subtotal",
        "status",
       
        ];
        
        protected $casts = [
            'user_id' => 'integer',
            'subtotal' => 'double',
            'status' => 'string',
           
            
           
        ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function order_products()
        {
            return $this->hasMany(Order_product::class);
        }
}
