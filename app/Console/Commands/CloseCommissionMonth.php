<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\AdminBusinessDay;
use App\Support\MamaCommissionCalculator;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * ปิดรอบเดือน / อัปเดต snapshot สะสมรายเดือนของทีมมาม่า ลง commission_monthly_progress
 *
 *  php artisan commission:close-month              ปิดรอบ "เดือนก่อนหน้า" (finalize)
 *  php artisan commission:close-month --current    อัปเดต snapshot "เดือนปัจจุบัน" (ยังไม่ปิด)
 *  php artisan commission:close-month --period=2026-07   ปิดรอบเดือนที่ระบุ
 */
class CloseCommissionMonth extends Command
{
    protected $signature = 'commission:close-month {--period=} {--current}';
    protected $description = 'ปิดรอบเดือน / อัปเดตสถานะสะสมคอมมิชชั่นทีมมาม่า';

    public function handle(): int
    {
        $isCurrent = (bool) $this->option('current');
        $period = $this->option('period');

        if (!$period) {
            $period = $isCurrent
                ? AdminBusinessDay::currentPeriodYm()
                : Carbon::now()->subMonthNoOverflow()->format('Y-m');
        }

        // ระบุ --period แต่ไม่ได้ --current ถือว่าเป็นการปิดรอบ
        $finalize = !$isCurrent;

        $mamas = User::withTrashed()->mama()->get();
        $categories = array_keys(MamaCommissionCalculator::CATEGORIES);
        $count = 0;

        foreach ($mamas as $mama) {
            foreach ($categories as $category) {
                MamaCommissionCalculator::persistProgress($mama, $period, $category, $finalize);
                $count++;
            }
        }

        $this->info(sprintf(
            'commission:close-month period=%s finalize=%s rows=%d (staff x categories)',
            $period,
            $finalize ? 'yes' : 'no',
            $count
        ));

        return self::SUCCESS;
    }
}
