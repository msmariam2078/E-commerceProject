<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable=[
        "user_id",
        "phone",
        'password',
        "active",
       'first_name',
       'last_name'
        ];
        
        protected $casts = [
           
            
        
            "phone"=>"string",
            "active"=>"boolean",
           'first_name'=>"string",
           'last_name'=>"string"
           
            
           
        ];

        public function addresses()
        {
            return $this->hasMany(Customer_address::class);
        }
        public function user()
        {
            return $this->belongsTo(User::class);
        }

}
