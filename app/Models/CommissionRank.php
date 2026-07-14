<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRank extends Model
{
    protected $table = 'commission_ranks';

    protected $fillable = [
        'ref_branch_id',
        'category',      // 'service' (นวด+สินค้า) | 'drink' (ดื่ม)
        'mode',          // 'sales' | 'rounds'
        'rank_no',       // 1-5
        'min_threshold', // ยอดสะสม(บาท) หรือ จำนวนรอบขั้นต่ำ
        'rate',          // %
        'fixed_amount',
        'payout_type',   // 'percent' | 'fixed_per_round' | 'fixed'
    ];

    protected $casts = [
        'min_threshold' => 'decimal:2',
        'rate' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'rank_no' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'ref_branch_id');
    }

    /**
     * บันได Rank ของสาขา+โหมด โดยเลือกค่าเฉพาะสาขาก่อน ถ้าไม่มีใช้ค่า default (ref_branch_id = null)
     * คืน collection เรียงตาม rank_no
     */
    public static function ladderFor($branchId, string $mode, string $category = 'service')
    {
        $ranks = static::where('mode', $mode)
            ->when(\Illuminate\Support\Facades\Schema::hasColumn('commission_ranks', 'category'),
                fn ($q) => $q->where('category', $category))
            ->where(function ($q) use ($branchId) {
                $q->where('ref_branch_id', $branchId)->orWhereNull('ref_branch_id');
            })
            ->orderBy('rank_no')
            ->get();

        return $ranks->groupBy('rank_no')
            ->map(function ($group) use ($branchId) {
                // มีค่าเฉพาะสาขา -> ใช้ก่อน, ไม่งั้น fallback global
                return $group->firstWhere('ref_branch_id', $branchId) ?? $group->first();
            })
            ->sortBy('rank_no')
            ->values();
    }
}
