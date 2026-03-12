<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = [
        'name'
    ];

    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'product_type';


}
