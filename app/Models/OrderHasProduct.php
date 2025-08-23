<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHasProduct extends Model
{
    // use HasFactory;
    protected $fillable = [
        'ref_order_id',
        'ref_product_id',
        'price',
        'quantity',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'order_has_products';

    public function order()
    {
        return $this->belongsTo(Order::class, 'ref_order_id');
    }
}
