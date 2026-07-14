<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WorkAttendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

/**
 * ตี 3 ของทุกวัน:
 *  - พนักงานที่ยังไม่แตะออก (status = working) ให้ปิดเป็น auto_ended (ถือว่าเลิกงาน)
 *  - รีเซ็ต work_status ของพนักงานทุกคนเป็น 0 (เลิกงาน) เพื่อเริ่มวันใหม่
 *    เช้ามาแตะบัตรครั้งแรก = เข้างาน + ขึ้น online อีกครั้ง
 */
class EndWorkDay extends Command
{
    protected $signature = 'attendance:end-day';
    protected $description = 'ตี 3: ปิดสถานะเข้างานที่ค้าง และรีเซ็ต work_status ของพนักงานทั้งหมด';

    public function handle(): int
    {
        $ended = 0;
        if (Schema::hasTable('work_attendances')) {
            $ended = WorkAttendance::where('status', 'working')->update(['status' => 'auto_ended']);
        }

        $reset = User::query()->update(['work_status' => 0]);

        $this->info("attendance:end-day auto_ended={$ended} reset_work_status={$reset}");

        return self::SUCCESS;
    }
}
