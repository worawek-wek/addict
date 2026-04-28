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
        return $this->hasOne('App\Models\Product', 'id', 'ref_product_id');
    }
    public function stock_ready_for_sales()
    {
        return $this->hasMany('App\Models\StockReadyForSale', 'ref_lot_id', 'id');
    }
    public function export_stocks()
    {
        return $this->hasMany('App\Models\ExportStock', 'ref_lot_id', 'id');
    }
}
