<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHasDrink extends Model
{
    // use HasFactory;
    protected $fillable = [
        'ref_order_id',
        'ref_drink_id',
        'price',
        'quantity',
        'cost',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'order_has_drinks';

    public function order()
    {
        return $this->belongsTo(Order::class, 'ref_order_id');
    }
    public function drink()
    {
        return $this->belongsTo(Drink::class, 'ref_drink_id');
    }
}
