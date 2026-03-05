<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrinkCardStocks extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'drink_card_stocks';
    
    public function product()
    {
        return $this->hasOne('App\Models\Drink', 'ref_drink_id', 'id');
    }
}
