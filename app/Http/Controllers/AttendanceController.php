<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /** id = 1 (เจ้าของร้าน) ดูได้ทุกสาขา นอกนั้นเฉพาะสาขาตัวเอง */
    private function canSeeAllBranches(): bool
    {
        return (int) auth()->id() === 1;
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
        $date = $this->parseDate($request->input('date'), Carbon::today()->toDateString());

        $users = User::with(['position', 'branch'])
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->orderBy('ref_position_id')
            ->orderBy('name')
            ->get();

        $attendance = WorkAttendance::where('work_date', $date)
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->get()
            ->keyBy('ref_staff_id');

        $byPosition = $users->groupBy(fn ($u) => optional($u->position)->position_name ?: 'อื่นๆ');

        return view('admin.attendance._data', compact('byPosition', 'attendance', 'date'));
    }

    /** หน้ารายงานเข้างาน ต่อวัน + ช่วงวันที่ */
    public function report(Request $request)
    {
        $page_url = 'admin/attendance/report';
        $branches = Branch::orderBy('name')->get();

        $today = Carbon::today()->toDateString();
        $start = $this->parseDate($request->input('start_date'), $today);
        $end = $this->parseDate($request->input('end_date'), $start);
        $branchId = $this->scopedBranchId($request);

        $records = WorkAttendance::with(['staff.position', 'staff.branch'])
            ->whereBetween('work_date', [$start, $end])
            ->when($branchId, fn ($q) => $q->where('ref_branch_id', $branchId))
            ->orderBy('work_date', 'desc')
            ->orderBy('ref_staff_id')
            ->get();

        return view('admin.attendance.report', compact('page_url', 'branches', 'records', 'start', 'end'));
    }

    /** PDF ของรายงานเข้างาน (ใช้ตัวกรองเดียวกับหน้า report) */
    public function reportPdf(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $start = $this->parseDate($request->input('start_date'), $today);
        $end = $this->parseDate($request->input('end_date'), $start);
        $branchId = $this->scopedBranchId($request);

        $records = WorkAttendance::with(['staff.position', 'staff.branch'])
            ->whereBetween('work_date', [$start, $end])
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
