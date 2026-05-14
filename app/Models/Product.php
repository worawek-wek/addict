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
    public function order_has_product()
    {
        return $this->hasOne('App\Models\OrderHasProduct', 'ref_product_id', 'id');
    }
    public function order_has_products()
    {
        return $this->hasMany(OrderHasProduct::class, 'ref_product_id', 'id');
    }
    public function history_stocks()
    {
        return $this->hasMany(HistoryStock::class, 'ref_product_id', 'id');
    }
    public function historyStocksOldest()
    {
        return $this->hasOne(HistoryStock::class, 'ref_product_id', 'id')
            ->oldest('id');
    }
    public function historyStocksMaxReady()
    {
        return $this->hasOne(HistoryStock::class, 'ref_product_id', 'id')
            ->ofMany('stock_ready_for_sale_before_quantity', 'max');
    }

    public function historyStocksLatest()
    {
        return $this->hasOne(HistoryStock::class, 'ref_product_id', 'id')
            ->latest('id');
    }
    public function historyStocksDecrease() // ดึงแค่ จำนวนที่ลดสต็อกขาย เช่น ขายสินค้า
    {
        return $this->hasMany(HistoryStock::class, 'ref_product_id', 'id')->where('quantity_type', 0);
    }
    public function historyStocksIncrease() // ดึงแค่ จำนวนที่เพิ่มสต็อกขาย เช่น คืนสต็อก
    {
        return $this->hasMany(HistoryStock::class, 'ref_product_id', 'id')->where('quantity_type', 1);
    }
    public function historyStocksExport() // ดึงแค่ จำนวนที่นำออกสต็อกหลัก
    {
        return $this->hasMany(HistoryStock::class, 'ref_product_id', 'id')->where('quantity_type', 2);
    }
    public function getQuantitySumAttribute() // เวลา เรียกใช้ ->quantity_sum
    {
        return $this->order_has_products()->sum('quantity');
    }
    public function firstOrderOfDay()
    {
        return $this->hasOne(HistoryStock::class, 'ref_product_id', 'id')->ofMany('created_at', 'min');
    }
    public function lastOrderOfDay()
    {
        return $this->hasOne(HistoryStock::class, 'ref_product_id', 'id')->ofMany('created_at', 'max');
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

    // ✅ ความสัมพันธ์กับ stock
    public function stockReadyForSales()
    {
        return $this->hasMany(StockReadyForSale::class, 'ref_product_id', 'id');
    }

    // ✅ ถ้าอยากได้ stock ล่าสุด (เช่นดูจาก updated_at ล่าสุด)
    public function latestStockReadyForSale()
    {
        return $this->hasOne(StockReadyForSale::class, 'ref_product_id', 'id')->latestOfMany();
    }

    // ✅ หรือถ้าอยากได้ stock รวมคงเหลือ
    public function getReadyForSaleTotalRemainAttribute()
    {
        return $this->stockReadyForSales()->sum('remain');
    }

    public function producttype()
    {
        return $this->hasOne(ProductType::class, 'id', 'type_id');
    }

}
