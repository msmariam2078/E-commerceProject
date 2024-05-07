<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_item extends Model
{
    use HasFactory;
  
    protected $fillable=[
        'uuid',
        "user_id",
        "product_id",
        "quantity"
       
       
        ];
        
        // protected $casts = [
        //     'product_id' => 'integer',
        //     'user_id' => 'integer',
        //     'quantity' => 'integer',
            
          
        // ];
        public function user()
        {
            return $this->belongsTo(User::class);
        }


        public function product()
        {
            return $this->belongsTo(Product::class);
        }

      


}
