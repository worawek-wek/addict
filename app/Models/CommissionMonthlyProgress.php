<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionMonthlyProgress extends Model
{
    protected $table = 'commission_monthly_progress';

    protected $fillable = [
        'ref_staff_id',
        'ref_branch_id',
        'period_ym',
        'category',
        'mode',
        'accumulated_sales',
        'accumulated_rounds',
        'current_rank',
        'applied_rate',
        'applied_min_threshold',
        'applied_payout_type',
        'applied_fixed_amount',
        'commission_amount',
        'rank_table_snapshot',
        'period_start',
        'period_end',
        'is_finalized',
        'finalized_at',
    ];

    protected $casts = [
        'rank_table_snapshot' => 'array',
        'is_finalized' => 'boolean',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'ref_staff_id')->withTrashed();
    }
}
