<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /** เจ้าของร้าน (Boss กัส) ยกเว้นจากการลงเวลา ถือว่าเข้างานตลอด ไม่นับในรายชื่อ */
    private const EXEMPT_STAFF_ID = 1;

    /** id = 1 (เจ้าของร้าน) ดูได้ทุกสาขา นอกนั้นเฉพาะสาขาตัวเอง */
    private function canSeeAllBranches(): bool
    {
        return \App\Models\User::isAllBranchAdmin(auth()->id());
    }

    private function scopedBranchId(Request $request): ?int
    {
        if ($this->canSeeAllBranches()) {
            return $request->filled('ref_branch_id') ? (int) $request->ref_branch_id : null;
        }

        return (int) auth()->user()->ref_branch_id;
    }

    private function parseDate($value, string $default): string
    {
        if (!$value) {
            return $default;
        }
        try {
            $fmt = str_contains((string) $value, '/') ? 'd/m/Y' : 'Y-m-d';
            return Carbon::createFromFormat($fmt, $value)->toDateString();
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /** หน้ารายชื่อการเข้างาน (แท็บแต่ละตำแหน่ง) + polling */
    public function index(Request $request)
    {
        $page_url = 'admin/attendance';
        $positions = Position::where('id', '!=', 0)->orderBy('position_name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.attendance.index', compact('page_url', 'positions', 'branches'));
    }

    /** ข้อมูล real-time สำหรับ polling — คืน partial ตารางแยกตามตำแหน่ง */
    public function data(Request $request)
    {
        $branchId = $this->scopedBranchId($request);
        $date = $this->parseDate($request->input('date'), WorkAttendance::businessDate());

        $users = User::with(['position', 'branch'])
            ->where('id', '!=', self::EXEMPT_STAFF_ID)
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->orderBy('ref_position_id')
            ->orderBy('name')
            ->get();

        $attendance = WorkAttendance::where('work_date', $date)
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->get()
            ->keyBy('ref_staff_id');

        $byPosition = $users->groupBy(fn ($u) => optional($u->position)->position_name ?: 'อื่นๆ');

        // ปุ่มกดเปิด/ปิดมาทำงานเอง โชว์เฉพาะเมื่อกำลังดูวันปัจจุบัน (ยึดขอบตี 3)
        $canToggle = ($date === WorkAttendance::businessDate());

        return view('admin.attendance._data', compact('byPosition', 'attendance', 'date', 'canToggle'));
    }

    /** กดเปิด/ปิด มาทำงานเอง (ไม่ได้แตะบัตร) — ทำงานเหมือนแตะบัตร: บันทึกเข้างาน + ให้ขึ้นหน้า booking */
    public function toggle(Request $request, $staff)
    {
        try {
            $user = User::find($staff);
            if (!$user) {
                return response()->json(['ok' => false, 'message' => 'ไม่พบพนักงาน'], 404);
            }

            // เจ้าของร้าน (Boss กัส) ยกเว้นจากการลงเวลา
            if ((int) $user->id === self::EXEMPT_STAFF_ID) {
                return response()->json(['ok' => false, 'message' => 'บัญชีนี้ยกเว้นการลงเวลา'], 422);
            }

            // สิทธิ์: คนที่ไม่ใช่เจ้าของร้าน (id=1) เปิดได้เฉพาะพนักงานสาขาตัวเอง
            if (!$this->canSeeAllBranches()
                && (int) $user->ref_branch_id !== (int) auth()->user()->ref_branch_id) {
                return response()->json(['ok' => false, 'message' => 'ไม่มีสิทธิ์จัดการพนักงานสาขาอื่น'], 403);
            }

            DB::beginTransaction();
            $result = WorkAttendance::punch($user);
            DB::commit();

            return response()->json(['ok' => true, 'action' => $result['action'], 'message' => $result['message']]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['ok' => false, 'message' => 'ผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /** หน้ารายงานเข้างาน ต่อวัน + ช่วงวันที่ */
    public function report(Request $request)
    {
        $page_url = 'admin/attendance/report';
        $branches = Branch::orderBy('name')->get();

        $today = WorkAttendance::businessDate();
        $start = $this->parseDate($request->input('start_date'), $today);
        $end = $this->parseDate($request->input('end_date'), $start);
        $branchId = $this->scopedBranchId($request);

        $records = WorkAttendance::with(['staff.position', 'staff.branch'])
            ->whereBetween('work_date', [$start, $end])
            ->where('ref_staff_id', '!=', self::EXEMPT_STAFF_ID)
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->orderBy('work_date', 'desc')
            ->orderBy('ref_staff_id')
            ->get();

        return view('admin.attendance.report', compact('page_url', 'branches', 'records', 'start', 'end'));
    }

    /** PDF ของรายงานเข้างาน (ใช้ตัวกรองเดียวกับหน้า report) */
    public function reportPdf(Request $request)
    {
        $today = WorkAttendance::businessDate();
        $start = $this->parseDate($request->input('start_date'), $today);
        $end = $this->parseDate($request->input('end_date'), $start);
        $branchId = $this->scopedBranchId($request);

        $records = WorkAttendance::with(['staff.position', 'staff.branch'])
            ->whereBetween('work_date', [$start, $end])
            ->where('ref_staff_id', '!=', self::EXEMPT_STAFF_ID)
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->orderBy('work_date', 'desc')
            ->orderBy('ref_staff_id')
            ->get();

        $html = view('admin.attendance.report_pdf', compact('records', 'start', 'end'))->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun',
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output('attendance.pdf', 'I');
    }
}
