<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ระบบคอมมิชชั่นทีมมาม่าแบบ Rank
     *  - commission_ranks: บันได Rank 1-5 (แก้ได้จากหลังบ้าน) แยกโหมด sales/rounds
     *  - users.commission_mode: เลือกโหมดของพนักงานแต่ละคน (1 คน 1 โหมด)
     *  - commission_monthly_progress: สถานะสะสมรายเดือน + snapshot ค่า rank ที่ใช้จริง
     *    (denormalized เพื่อให้ย้อนดูได้ แม้เดือนถัดไปจะแก้เกณฑ์/เรตของ rank)
     */
    public function up(): void
    {
        // 1) บันได Rank (config ที่แก้ได้) --------------------------------------
        if (!Schema::hasTable('commission_ranks')) {
            Schema::create('commission_ranks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ref_branch_id')->nullable()
                    ->comment('null = ค่า default ใช้ทุกสาขา (fallback)');
                $table->enum('category', ['service', 'drink'])->default('service')
                    ->comment('service = นวด+สินค้า, drink = ดื่ม');
                $table->enum('mode', ['sales', 'rounds'])
                    ->comment('บันไดยอดขาย หรือ บันไดจำนวนรอบ');
                $table->unsignedTinyInteger('rank_no')->comment('1-5');
                $table->decimal('min_threshold', 12, 2)->default(0)
                    ->comment('ยอดสะสมขั้นต่ำ(บาท) หรือ จำนวนรอบขั้นต่ำ ที่ทำให้ถึง rank นี้');
                $table->decimal('rate', 5, 2)->default(0)
                    ->comment('% คอมมิชชั่นของ rank นี้');
                $table->decimal('fixed_amount', 12, 2)->nullable()
                    ->comment('จำนวนเงินคงที่ (ใช้กับ payout_type fixed/fixed_per_round)');
                $table->enum('payout_type', ['percent', 'fixed_per_round', 'fixed'])
                    ->default('percent');
                $table->timestamps();

                $table->unique(['ref_branch_id', 'category', 'mode', 'rank_no'], 'commission_ranks_branch_cat_mode_rank_unique');
                $table->index(['category', 'mode', 'min_threshold']);
            });
        }

        // 2) โหมดคอมมิชชั่นของพนักงานแต่ละคน -----------------------------------
        if (!Schema::hasColumn('users', 'commission_mode')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('commission_mode', ['sales', 'rounds'])
                    ->nullable()->default('sales')->after('ref_position_id')
                    ->comment('โหมดคิดคอมมิชชั่น นวด+สินค้า (เลือกได้ 1 โหมด)');
            });
        }
        if (!Schema::hasColumn('users', 'drink_commission_mode')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('drink_commission_mode', ['sales', 'rounds'])
                    ->nullable()->default('sales')->after('commission_mode')
                    ->comment('โหมดคิดคอมมิชชั่นดื่ม (เลือกได้ 1 โหมด)');
            });
        }

        // 3) สถานะสะสมรายเดือน + snapshot ที่ย้อนตรวจได้ -----------------------
        if (!Schema::hasTable('commission_monthly_progress')) {
            Schema::create('commission_monthly_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ref_staff_id');
                $table->unsignedBigInteger('ref_branch_id')->nullable();
                $table->char('period_ym', 7)->comment("รอบเดือน เช่น '2026-07'");
                $table->enum('category', ['service', 'drink'])->default('service')
                    ->comment('service = นวด+สินค้า, drink = ดื่ม');
                $table->enum('mode', ['sales', 'rounds'])->comment('โหมดของคนนี้ ณ เดือนนั้น');

                // ค่าสะสมของเดือน
                $table->decimal('accumulated_sales', 14, 2)->default(0);
                $table->unsignedInteger('accumulated_rounds')->default(0);

                // Rank + snapshot ค่าที่ "ใช้จริง" (ไม่ผูก FK กับ config ที่แก้ได้)
                $table->unsignedTinyInteger('current_rank')->default(0)
                    ->comment('rank ที่ได้ (ค้างไว้ = ค่าสูงสุดของเดือน)');
                $table->decimal('applied_rate', 5, 2)->default(0)
                    ->comment('เรต % ที่ใช้คำนวณจริง');
                $table->decimal('applied_min_threshold', 12, 2)->default(0)
                    ->comment('เกณฑ์ขั้นต่ำของ rank ที่ตัดได้ ณ ตอนนั้น');
                $table->enum('applied_payout_type', ['percent', 'fixed_per_round', 'fixed'])
                    ->default('percent');
                $table->decimal('applied_fixed_amount', 12, 2)->nullable();

                $table->decimal('commission_amount', 14, 2)->default(0);

                // ภาพรวมบันได rank ทั้งชุด ณ ตอนคำนวณ/ปิดเดือน -> ตรวจ "ตัดยังไง" ได้ครบ
                $table->json('rank_table_snapshot')->nullable();

                $table->dateTime('period_start')->nullable();
                $table->dateTime('period_end')->nullable();
                $table->boolean('is_finalized')->default(false)
                    ->comment('true = ปิดเดือนแล้ว ค่าคงที่ ไม่คำนวณใหม่');
                $table->timestamp('finalized_at')->nullable();
                $table->timestamps();

                $table->unique(['ref_staff_id', 'period_ym', 'category'], 'commission_progress_staff_period_cat_unique');
                $table->index(['period_ym', 'category', 'mode']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_monthly_progress');

        if (Schema::hasColumn('users', 'commission_mode')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('commission_mode');
            });
        }

        Schema::dropIfExists('commission_ranks');
    }
};
