<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ระบบลงเวลาเข้างานแบบแตะบัตร (real-time)
 *  - แตะครั้งแรกของวัน = เข้างาน (check_in_at)
 *  - แตะครั้งที่สอง    = ออก/ลา (check_out_at, status = left)
 *  - ไม่แตะออก -> cron ตี 3 ปิดให้ (status = auto_ended)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('work_attendances')) {
            Schema::create('work_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ref_staff_id');
                $table->unsignedBigInteger('ref_branch_id')->nullable();
                $table->date('work_date');
                $table->dateTime('check_in_at')->nullable();
                $table->dateTime('check_out_at')->nullable();
                $table->enum('status', ['working', 'left', 'auto_ended'])->default('working')
                    ->comment('working=กำลังทำงาน, left=แตะออก/ลา, auto_ended=ตี3ปิดอัตโนมัติ');
                $table->timestamps();

                $table->unique(['ref_staff_id', 'work_date'], 'work_attendances_staff_date_unique');
                $table->index(['work_date', 'ref_branch_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_attendances');
    }
};
