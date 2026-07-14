<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryCommission extends Model
{
    protected $table = 'history_commissions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'round', 'type', 'ref_staff_id', 'commission', 'sales_received',
        'commission_rate', 'min_sales_amount', 'max_sales_amount',
        'rank_no', 'accumulated_rounds', 'mode', 'payout_type',
        'from_date', 'to_date',
    ];
    
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'ref_staff_id')->withTrashed();
    }
}
