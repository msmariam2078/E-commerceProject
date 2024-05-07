<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable=[
        'uuid',
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
        protected $appends= [
            'full'
        ];
        public function getFullAttribute()
        {
        
         return $this->first_name." ".$this->last_name;;
        }
        public function addresses()
        {
            return $this->hasMany(Customer_address::class);
        }
        public function user()
        {
            return $this->belongsTo(User::class);
        }

}
