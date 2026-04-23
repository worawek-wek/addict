<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryStock extends Model
{
    // use HasFactory;
    protected $fillable = [
        'ref_product_id',
        'quantity',
        'quantity_type',
        'stock_before_quantity',
        'stock_after_quantity',
    ];
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'history_stocks';

    public function product()
    {
        return $this->belongsTo(Product::class, 'ref_product_id');
    }
}
