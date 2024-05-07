<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable=[
        "order_id",
        "type",
        "status",
        "amount"
       
        ];
        
        protected $casts = [
            'order_id' => 'interger',
            'type' => 'string',
            'status' => 'string',
            "amount"=>"decimal"
           
            
           
        ];
        public function order()
        {
            return $this->belongsTo(Order::class);
        }
    
}
