<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'drinks';

    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
    public function order_has_drink()
    {
        return $this->hasOne('App\Models\OrderHasDrink', 'ref_drink_id', 'id');
    }
      // ✅ ความสัมพันธ์กับ stock
    public function cardStocks()
    {
        return $this->hasMany(DrinkCardStocks::class, 'ref_drink_id', 'id');
    }

    // ✅ ถ้าอยากได้ stock ล่าสุด (เช่นดูจาก updated_at ล่าสุด)
    public function latestStock()
    {
        return $this->hasOne(DrinkCardStocks::class, 'ref_drink_id', 'id')->latestOfMany();
    }

    // ✅ หรือถ้าอยากได้ stock รวมคงเหลือ
    public function getTotalRemainAttribute()
    {
        return $this->cardStocks()->sum('remain');
    }
}
