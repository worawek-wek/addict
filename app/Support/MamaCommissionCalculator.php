<?php

namespace App\Support;

use App\Models\CommissionMonthlyProgress;
use App\Models\CommissionRank;
use App\Models\Order;
use App\Models\OrderHasDrink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * คำนวณคอมมิชชั่นทีมมาม่าแบบ Rank (รองรับ 2 หมวด service = นวด+สินค้า, drink = ดื่ม)
 *
 *  - โหมด 'sales'  : rank ตัดจากยอดขายสะสม, จ่าย % ของยอดขาย
 *  - โหมด 'rounds' : rank ตัดจากจำนวนรอบสะสม (1 order = 1 รอบ)
 *  - payout_type ของ rank กำหนดวิธีจ่าย: percent | fixed_per_round | fixed
 *
 * แหล่งข้อมูล (นับเฉพาะ order ที่ ref_seller_id = พนักงาน ในช่วงเวลาที่กำหนด):
 *  - service (order type IN 1,2): ยอดขาย = ยอดรวมทั้งบิล orders.total_price
 *      (คอร์ส + สินค้า + option หลังหักส่วนลด ตามที่บันทึกในบิล)
 *  - drink   (order type = 3)   : ยอดขาย = SUM(order_has_drinks.price * quantity)
 *  - จำนวนรอบ ทั้งสองหมวด = จำนวน order ไม่ซ้ำ (1 order = 1 รอบ)
 */
class MamaCommissionCalculator
{
    /**
     * ตั้งค่าของแต่ละหมวด
     *  - order_types : ชนิดออเดอร์ที่นับเข้าหมวดนี้
     *  - mode_field  : คอลัมน์บน users ที่เก็บโหมดคอมฯของหมวดนี้
     *  - sales_source: order_total = ยอดรวมทั้งบิล (orders.total_price)
     *                  drink_line  = SUM(price*quantity) จาก order_has_drinks
     */
    public const CATEGORIES = [
        'service' => ['order_types' => [1, 2], 'mode_field' => 'commission_mode',       'sales_source' => 'order_total'],
        'drink'   => ['order_types' => [3],    'mode_field' => 'drink_commission_mode', 'sales_source' => 'drink_line'],
    ];

    private static function config(string $category): array
    {
        return self::CATEGORIES[$category] ?? self::CATEGORIES['service'];
    }

    /**
     * คำนวณผลของพนักงาน 1 คนในช่วง [$start, $end] ของหมวดที่ระบุ
     */
    public static function computeForStaff(User $staff, $start, $end, string $category = 'service'): array
    {
        $cfg = self::config($category);
        $modeField = $cfg['mode_field'];
        $orderTypes = $cfg['order_types'];
        $salesSource = $cfg['sales_source'] ?? 'order_total';

        $mode = in_array($staff->{$modeField} ?? null, ['sales', 'rounds'], true)
            ? $staff->{$modeField}
            : 'sales';

        // ออเดอร์ของผู้ขายคนนี้ ในหมวด+ช่วงเวลา (1 order = 1 รอบ)
        $orderQuery = function () use ($staff, $orderTypes, $start, $end) {
            return Order::where('ref_seller_id', $staff->id)
                ->whereIn('type', $orderTypes)
                ->whereBetween('created_at', [$start, $end]);
        };

        $orderIds = $orderQuery()->pluck('id');
        $accumulatedRounds = $orderIds->count();

        if ($salesSource === 'drink_line') {
            // ยอดดื่ม = ราคาต่อหน่วย x จำนวน ของทุกแถวในออเดอร์เหล่านั้น
            $accumulatedSales = $orderIds->isEmpty()
                ? 0.0
                : (float) OrderHasDrink::whereIn('ref_order_id', $orderIds)
                    ->sum(DB::raw('price * quantity'));
        } else {
            // ยอดรวมทั้งบิล (คอร์ส + สินค้า + option หลังหักส่วนลด ตามที่บันทึกในบิล)
            $accumulatedSales = (float) ($orderQuery()->sum('total_price') ?? 0);
        }

        $ladder = self::ladder($staff->ref_branch_id, $mode, $category);
        $metric = $mode === 'rounds' ? $accumulatedRounds : $accumulatedSales;

        $rank = self::resolveRank($ladder, $metric);

        $rankNo = $rank['rank_no'] ?? 0;
        $rate = (float) ($rank['rate'] ?? 0);
        $minThreshold = (float) ($rank['min_threshold'] ?? 0);
        $payoutType = $rank['payout_type'] ?? 'percent';
        $fixedAmount = isset($rank['fixed_amount']) ? (float) $rank['fixed_amount'] : null;

        $commission = self::payout($payoutType, $rate, $fixedAmount, $accumulatedSales, $accumulatedRounds);

        return [
            'category' => $category,
            'mode' => $mode,
            'accumulated_sales' => $accumulatedSales,
            'accumulated_rounds' => $accumulatedRounds,
            'rank_no' => $rankNo,
            'applied_rate' => $rate,
            'applied_min_threshold' => $minThreshold,
            'applied_payout_type' => $payoutType,
            'applied_fixed_amount' => $fixedAmount,
            'commission_amount' => round($commission, 2),
            'rank_table_snapshot' => $ladder,
        ];
    }

    /**
     * บันได rank (snapshot เป็น array ง่ายๆ) — คืน [] ถ้ายังไม่มีตาราง/ข้อมูล
     */
    public static function ladder($branchId, string $mode, string $category = 'service'): array
    {
        if (!Schema::hasTable('commission_ranks')) {
            return [];
        }

        return CommissionRank::ladderFor($branchId, $mode, $category)
            ->map(function ($r) {
                return [
                    'rank_no' => (int) $r->rank_no,
                    'min_threshold' => (float) $r->min_threshold,
                    'rate' => (float) $r->rate,
                    'payout_type' => $r->payout_type,
                    'fixed_amount' => $r->fixed_amount !== null ? (float) $r->fixed_amount : null,
                ];
            })
            ->all();
    }

    /**
     * เลือก rank สูงสุดที่ min_threshold <= metric (ยอด/รอบสะสม)
     * ไม่ถึงขั้นต่ำสุด -> null (rank 0, ไม่มีคอมฯ)
     */
    public static function resolveRank(array $ladder, float $metric): ?array
    {
        $matched = null;
        foreach ($ladder as $rank) {
            if ($metric >= (float) $rank['min_threshold']) {
                if ($matched === null || $rank['rank_no'] > $matched['rank_no']) {
                    $matched = $rank;
                }
            }
        }

        return $matched;
    }

    /**
     * บันทึก/อัปเดตสถานะสะสมรายเดือนของพนักงาน 1 คน ลง commission_monthly_progress
     *  - latch: current_rank ไม่ลดลงภายในเดือน (ถ้า config ถูกลดขั้น ยังคงขั้นเดิมไว้)
     *  - เดือนที่ปิดแล้ว (is_finalized) จะไม่คำนวณทับ
     *  - แยกตามหมวด (service / drink)
     *
     * @param  string  $ym  รูปแบบ 'YYYY-MM'
     */
    public static function persistProgress(User $staff, string $ym, string $category = 'service', bool $finalize = false): ?CommissionMonthlyProgress
    {
        if (!Schema::hasTable('commission_monthly_progress')) {
            return null;
        }

        $key = ['ref_staff_id' => $staff->id, 'period_ym' => $ym];
        if (Schema::hasColumn('commission_monthly_progress', 'category')) {
            $key['category'] = $category;
        }
        $existing = CommissionMonthlyProgress::firstOrNew($key);

        // ปิดรอบแล้ว = ค่าคงที่ ไม่แตะ
        if ($existing->exists && $existing->is_finalized) {
            return $existing;
        }

        [$start, $end] = \App\Support\AdminBusinessDay::monthRange($ym);
        $c = self::computeForStaff($staff, $start, $end, $category);

        // latch rank: ถ้าเคยได้ขั้นสูงกว่า ให้คงขั้นเดิม แล้วคิดเงินตามขั้นนั้นจาก ladder
        $prevRank = (int) ($existing->current_rank ?? 0);
        if ($prevRank > (int) $c['rank_no']) {
            foreach ($c['rank_table_snapshot'] as $rankRow) {
                if ((int) $rankRow['rank_no'] === $prevRank) {
                    $c['rank_no'] = $prevRank;
                    $c['applied_rate'] = (float) $rankRow['rate'];
                    $c['applied_min_threshold'] = (float) $rankRow['min_threshold'];
                    $c['applied_payout_type'] = $rankRow['payout_type'];
                    $c['applied_fixed_amount'] = $rankRow['fixed_amount'];
                    $c['commission_amount'] = round(self::payout(
                        $rankRow['payout_type'],
                        (float) $rankRow['rate'],
                        $rankRow['fixed_amount'],
                        $c['accumulated_sales'],
                        $c['accumulated_rounds']
                    ), 2);
                    break;
                }
            }
        }

        $fill = [
            'ref_branch_id' => $staff->ref_branch_id,
            'mode' => $c['mode'],
            'accumulated_sales' => $c['accumulated_sales'],
            'accumulated_rounds' => $c['accumulated_rounds'],
            'current_rank' => $c['rank_no'],
            'applied_rate' => $c['applied_rate'],
            'applied_min_threshold' => $c['applied_min_threshold'],
            'applied_payout_type' => $c['applied_payout_type'],
            'applied_fixed_amount' => $c['applied_fixed_amount'],
            'commission_amount' => $c['commission_amount'],
            'rank_table_snapshot' => $c['rank_table_snapshot'],
            'period_start' => $start,
            'period_end' => $end,
        ];
        if (Schema::hasColumn('commission_monthly_progress', 'category')) {
            $fill['category'] = $category;
        }
        $existing->fill($fill);

        if ($finalize) {
            $existing->is_finalized = true;
            $existing->finalized_at = now();
        }

        $existing->save();

        return $existing;
    }

    public static function payout(string $payoutType, float $rate, ?float $fixed, float $sales, int $rounds): float
    {
        switch ($payoutType) {
            case 'fixed_per_round':
                return (float) ($fixed ?? 0) * $rounds;
            case 'fixed':
                return (float) ($fixed ?? 0);
            case 'percent':
            default:
                return $rate * $sales / 100;
        }
    }
}
