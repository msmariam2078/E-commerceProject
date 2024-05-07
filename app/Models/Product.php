<?php

namespace App\Models;
use App\Enums\UserTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory;
    use SoftDeletes; 
    protected $fillable=[
        'uuid',
        "title",
        "price",
        "image",
        "desc",
        "category_id",
        "status",
        ];
        protected $dates = ['deleted_at'];
        protected $casts = [
            'title' => 'string',
            'price' => 'double',
          
            'image' => 'string',
            'desc' => 'string',
            "status"=>UserTypeEnum::class
        ];


        public function ordered_products()
        {

        return $this->hasMany(Order_product::class);


        }
        public function category()
        {

        return $this->belongsTo(Category::class);


        }
}
