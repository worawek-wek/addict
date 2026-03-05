<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkStockReadyForSale extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'drink_stock_ready_for_sales';
    
    public function drink()
    {
        return $this->hasOne('App\Models\Drink', 'ref_drink_id', 'id');
    }
}
