<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer_address extends Model
{
    use HasFactory;
    protected $fillable=[
        'uuid',
        "customer_id",
        "country_code",
        "address1",
        "address2",
        "state_id",
        "type"
       
       
        ];
        
        protected $casts = [
            'user_id' => 'integer',
            'country_code' => 'string',
            'address1' => 'string',
            'address2' => 'string',
            'state_id' => 'integer',
            'type' => 'string',
            
           
            
           
        ];
        public function country()
        {
            return $this->belongsTo(Country::class,"country_code");
        }
        public function state()
        {
            return $this->belongsTo(State::class);
        }
}
