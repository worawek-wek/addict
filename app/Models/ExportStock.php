<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportStock extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'export_stocks';
    
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'ref_product_id', 'id');
    }
    public function card_stock()
    {
        return $this->hasOne('App\Models\CardStocks', 'ref_lot_id', 'id');
    }
}
