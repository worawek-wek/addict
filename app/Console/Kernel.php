<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ปิดรอบเดือนก่อนหน้า (finalize) วันที่ 1 ของเดือน เวลา 00:05
        $schedule->command('commission:close-month')->monthlyOn(1, '00:05');

        // อัปเดต snapshot สะสมของเดือนปัจจุบันทุกวัน เวลา 00:15 (ให้ rank ค้างไว้ล่าสุด)
        $schedule->command('commission:close-month --current')->dailyAt('00:15');

        // ตี 3: ปิดสถานะเข้างานที่ค้าง + รีเซ็ต work_status เริ่มวันใหม่
        $schedule->command('attendance:end-day')->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
