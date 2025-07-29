<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardStocks extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'card_stocks';
    
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'ref_product_id', 'id');
    }
}
