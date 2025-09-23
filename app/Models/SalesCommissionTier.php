<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCommissionTier extends Model
{
    protected $table = 'sales_commission_tiers';
    public $timestamps = false;
    protected $fillable = [
        'ref_branch_id',
        'min_sales_amount',
        'max_sales_amount',
        'commission_rate',
        'created_at',
    ];
}
