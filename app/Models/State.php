<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $fillable=[
        "name",
        "country_code",
       
       
        ];
        
        protected $casts = [
            'name' => 'string',
            'country_code' => 'string',
            
           
            
           
        ];
        public function country()
        {
            return $this->belongsTo(Country::class,"country_code");
        }
}
