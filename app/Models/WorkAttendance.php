<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorkAttendance extends Model
{
    /** ชั่วโมงที่ถือเป็นขอบวันของการเข้างาน = ตี 3 (ตรงกับ cron attendance:end-day) */
    public const DAY_CUTOFF_HOUR = 3;

    protected $table = 'work_attendances';

    protected $fillable = [
        'ref_staff_id',
        'ref_branch_id',
        'work_date',
        'check_in_at',
        'check_out_at',
        'status',
    ];

    protected $casts = [
        'work_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'ref_staff_id')->withTrashed();
    }

    /**
     * วันเข้างานตามขอบตี 3 — เวลา 00:00-02:59 ถือเป็นของเมื่อวาน
     * ใช้ให้ตรงกันทั้งตอนแตะบัตร (FrontClockInController) และหน้ารายชื่อ/รายงาน (AttendanceController)
     */
    public static function businessDate($now = null): string
    {
        $now = $now ? Carbon::parse($now) : Carbon::now();

        return $now->copy()->subHours(self::DAY_CUTOFF_HOUR)->toDateString();
    }

    /**
     * ลงเวลาแบบ toggle ให้พนักงาน 1 คน — ใช้ร่วมกันทั้งแตะบัตร (FrontClockInController)
     * และปุ่มกดเปิดเองจากหน้ารายชื่อ (AttendanceController)
     *  - ครั้งแรกของวัน = เข้างาน (working) + work_status=1 + ref_status_id=1 (ให้ขึ้นหน้า booking)
     *  - ครั้งถัดไป = ออกงาน (left) + work_status=0
     *  - หลังออกแล้ว = ครบแล้ว (ไม่ทำอะไร)
     * ยึดขอบวันตี 3 — ต้องเรียกภายใน DB transaction (ใช้ lockForUpdate กันแตะซ้ำ)
     *
     * @return array{action:string,message:string}  action = in | out | done
     */
    public static function punch(User $user, $now = null): array
    {
        $now = $now ? Carbon::parse($now) : Carbon::now();
        $workDate = self::businessDate($now);

        $attendance = self::where('ref_staff_id', $user->id)
            ->where('work_date', $workDate)
            ->lockForUpdate()
            ->first();

        // แตะครั้งแรกของวัน = เข้างาน
        if (!$attendance) {
            self::create([
                'ref_staff_id' => $user->id,
                'ref_branch_id' => $user->ref_branch_id,
                'work_date' => $workDate,
                'check_in_at' => $now,
                'status' => 'working',
            ]);
            $user->work_status = 1;
            $user->ref_status_id = 1;
            $user->save();

            return ['action' => 'in', 'message' => "คุณ {$user->nickname} เข้างานสำเร็จ เวลา " . $now->format('H:i') . " น."];
        }

        // แตะครั้งที่สอง = ออก/ลา
        if ($attendance->status === 'working') {
            $attendance->check_out_at = $now;
            $attendance->status = 'left';
            $attendance->save();
            $user->work_status = 0;
            $user->save();

            return ['action' => 'out', 'message' => "คุณ {$user->nickname} ออกงาน เวลา " . $now->format('H:i') . " น."];
        }

        // แตะเพิ่มหลังออกแล้ว = ครบแล้ว
        return ['action' => 'done', 'message' => "วันนี้ลงเวลาครบแล้ว"];
    }
}
