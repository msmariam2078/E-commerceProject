<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use HasFactory;
    use SoftDeletes; 
    protected $fillable=[
        "title",
        "price",
        "image",
        "desc",
        "status",
        ];
        protected $dates = ['deleted_at'];
        protected $casts = [
            'title' => 'string',
            'price' => 'double',
            'status' => 'string',
            'image' => 'string',
            'desc' => 'string',
           
        ];
}
