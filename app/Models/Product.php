<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'products';

    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
      // ✅ ความสัมพันธ์กับ stock
    public function cardStocks()
    {
        return $this->hasMany(CardStocks::class, 'ref_product_id', 'id');
    }

    // ✅ ถ้าอยากได้ stock ล่าสุด (เช่นดูจาก updated_at ล่าสุด)
    public function latestStock()
    {
        return $this->hasOne(CardStocks::class, 'ref_product_id', 'id')->latestOfMany();
    }

    // ✅ หรือถ้าอยากได้ stock รวมคงเหลือ
    public function getTotalRemainAttribute()
    {
        return $this->cardStocks()->sum('remain');
    }
}
