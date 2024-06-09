<?php

namespace App\Models;
use App\Models\Cat_item;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail 
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'userName',
        "email",
        "password",
        "is_admin",
        "is_verified",
        "verify_token"

    ];
    protected $appends=['cost'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $casts = [
       
    //     // 'name' => 'string',
    //     'email' => 'string',
    //     'password' => 'string',
    //     'is_admin'=>'boolean',
    //     'email_verified_at' => 'datetime',
       
        
       
    // ];




    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
    public function orders()
    { 
        return $this->hasMany(Order::class);
    }
    public function cart_items()
    { 
        return $this->hasMany(Cart_item::class);
    }
    public function customer()
    { 
        return $this->hasOne(Customer::class);
    }
     

    public function getCostAttribute()
    {
     $total=0;
    foreach($this->cart_items as $item){
     $total+=($item->product->price)*($item->quantity);

     }
     return $total;
    }


 


}
