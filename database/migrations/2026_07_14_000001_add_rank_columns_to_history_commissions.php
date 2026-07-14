<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * เพิ่มฟิลด์ snapshot ของระบบ Rank ลงตารางประวัติรอบคอมมิชชั่น
 * เพื่อให้ปุ่ม "พิมพ์ + บันทึกรอบ" เก็บ Rank/จำนวนรอบ/โหมด ที่ใช้จริงไว้ย้อนดูได้
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('history_commissions', 'rank_no')) {
                $table->unsignedTinyInteger('rank_no')->default(0)->after('type');
            }
            if (!Schema::hasColumn('history_commissions', 'accumulated_rounds')) {
                $table->unsignedInteger('accumulated_rounds')->default(0)->after('sales_received');
            }
            if (!Schema::hasColumn('history_commissions', 'mode')) {
                $table->string('mode', 20)->nullable()->after('rank_no');
            }
            if (!Schema::hasColumn('history_commissions', 'payout_type')) {
                $table->string('payout_type', 20)->nullable()->after('mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_commissions', function (Blueprint $table) {
            foreach (['rank_no', 'accumulated_rounds', 'mode', 'payout_type'] as $col) {
                if (Schema::hasColumn('history_commissions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
