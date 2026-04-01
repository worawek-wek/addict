<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Position;
use App\Models\Branch;
use App\Models\DailySalesClosure;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Mpdf\Mpdf;

DB::beginTransaction();

class ReportController extends Controller
{

    public function view_overview(Request $request)
    {
        return view('report/report-viewOverview');
    }
    public function rent_bill(Request $request)
    {
        return view('report/report-rentBill');
    }
    public function move_in(Request $request)
    {
        return view('report/report-moveIn');
    }
    public function move_out(Request $request)
    {
        return view('report/report-moveOut');
    }
    public function badDebt(Request $request)
    {
        return view('report/report-badDebt');
    }
    public function monthly_booking(Request $request)
    {
        return view('report/report-monthlyBooking');
    }

    public function coupon_report(Request $request)
    {
        $data['page_url'] = "admin/report/coupon-report";
        $data['employees'] = \App\Models\User::where('ref_branch_id', Auth::user()->ref_branch_id)
            ->where('ref_position_id', 2)
            ->orderBy('name')
            ->get();
        return view('admin.report.report-couponReport', $data);
    }

    public function coupon_report_datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $orderRooms = $this->CRgetOrderRooms($limit);

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        $userCommissionMap = \App\Models\UserHasRoomTypeCommission::select('ref_user_id', 'ref_room_type_id', 'ref_course_id', 'price', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_user_id}_{$r->ref_room_type_id}_{$r->ref_course_id}");

        $roomTypeCourseMap = \App\Models\RoomTypeHasCourse::select('ref_room_type_id', 'ref_course_id', 'price', 'commission', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_room_type_id}_{$r->ref_course_id}");

        return view('admin.report.report-couponReport-datatable', compact('orderRooms', 'branches', 'userCommissionMap', 'roomTypeCourseMap'));
    }

    private function CRgetOrderRooms($limit)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'seller', 'course'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3])
            // ->select('orders.*')
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('booking_date')
            ->orderBy('start_time');

        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request()->filled('user_id')) {
            $get_user_id = User::where('user_id', request('user_id'))->first();
            if ($get_user_id) {
                $query->where('ref_user_id', $get_user_id->id);
            }
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
            if (request('start_time_filter')) {
                [$sh, $sm] = explode(':', request('start_time_filter'));
                $startDate->setTime((int)$sh, (int)$sm, 0);
            }
            if (request('end_time_filter')) {
                [$eh, $em] = explode(':', request('end_time_filter'));
                $endDate->setTime((int)$eh, (int)$em, 59);
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orderRooms = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        foreach ($orderRooms as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);

            if (!empty($order->payment_method)) {
                $order->badge_class = 'bg-info';
                $order->status_label = $order->payment_method;
            } elseif ($order->ref_status_id == 2) {
                $order->badge_class = 'bg-success';
                $order->status_label = 'อยู่ระหว่างใช้บริการ';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->between($startDateTime, $endDateTime)) {
                $order->badge_class = 'bg-primary';
                $order->status_label = 'จอง (ถึงเวลาแล้ว)';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->lessThan($startDateTime)) {
                $order->badge_class = 'bg-warning';
                $order->status_label = 'จอง';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->greaterThan($endDateTime)) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'จอง (เกินเวลา)';
            } elseif ($order->ref_status_id == 3) {
                $order->badge_class = 'bg-secondary';
                $order->status_label = 'ใช้บริการเสร็จสิ้น';
            } elseif ($order->ref_status_id == 4) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'ยกเลิก';
            } else {
                $order->badge_class = 'bg-dark';
                $order->status_label = 'ไม่ระบุ';
            }
        }

        return $orderRooms;
    }

    public function coupon_report_pdf(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $orderRooms = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'seller', 'course'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3])
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('booking_date')
            ->orderBy('start_time');


        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $orderRooms->where('ref_branch_id', $userBranchId);
        }

        if (request()->filled('branch_id')) {
            $orderRooms->where('ref_branch_id', request()->branch_id);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $orderRooms->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request()->filled('user_id')) {
            $get_user_id = User::where('user_id', request('user_id'))->first();
            if ($get_user_id) {
                $orderRooms->where('ref_user_id', $get_user_id->id);
            }
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
            if (request('start_time_filter')) {
                [$sh, $sm] = explode(':', request('start_time_filter'));
                $startDate->setTime((int)$sh, (int)$sm, 0);
            }
            if (request('end_time_filter')) {
                [$eh, $em] = explode(':', request('end_time_filter'));
                $endDate->setTime((int)$eh, (int)$em, 59);
            }
            $orderRooms->whereBetween('created_at', [$startDate, $endDate]);
        }

        $data['orderRooms'] = collect($orderRooms->get());
        $data['summary_total_price'] = $data['orderRooms']->sum('total_price');

        $data['userCommissionMap'] = \App\Models\UserHasRoomTypeCommission::select('ref_user_id', 'ref_room_type_id', 'ref_course_id', 'price', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_user_id}_{$r->ref_room_type_id}_{$r->ref_course_id}");

        $data['roomTypeCourseMap'] = \App\Models\RoomTypeHasCourse::select('ref_room_type_id', 'ref_course_id', 'price', 'commission', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_room_type_id}_{$r->ref_course_id}");

        $data['report_start_date'] = request('start_date') ?? date('d/m/Y');
        $data['report_end_date']   = request('end_date')   ?? date('d/m/Y');

        $html = view('admin.report.report-couponReport-pdf', $data)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    public function drink_com(Request $request)
    {
        $data['page_url'] = "admin/report/drink-com";
        $data['employees'] = \App\Models\User::where('ref_branch_id', Auth::user()->ref_branch_id)
            ->where('ref_position_id', 2)
            ->orderBy('name')
            ->get();
        return view('admin.report.report-drink-com', $data);
    }

    public function drink_com_datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $orderRooms = $this->DCgetOrderRooms($limit);

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        $userCommissionMap = \App\Models\UserHasRoomTypeCommission::select('ref_user_id', 'ref_room_type_id', 'ref_course_id', 'price', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_user_id}_{$r->ref_room_type_id}_{$r->ref_course_id}");

        $roomTypeCourseMap = \App\Models\RoomTypeHasCourse::select('ref_room_type_id', 'ref_course_id', 'price', 'commission', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_room_type_id}_{$r->ref_course_id}");

        return view('admin.report.report-drink-com-datatable', compact('orderRooms', 'branches', 'userCommissionMap', 'roomTypeCourseMap'));
    }
    private function DCgetOrderRooms($limit)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::withSum('drinks', 'price')
                        ->with(['branch', 'customer', 'user', 'room', 'status', 'seller'])
                        ->where('type', 3)
                        ->whereIn('ref_status_id', [2, 3])
                        // ->select('orders.*')
                        ->orderByRaw("
                                    CASE
                                        WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                                        WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                                        WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                                        WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                                        WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                                        WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                                        WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                                        ELSE 8 -- ไม่ระบุ
                                    END
                                ")
                        ->orderBy('booking_date')
                        ->orderBy('start_time');

        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request()->filled('user_id')) {
            $get_user_id = User::where('user_id', request('user_id'))->first();
            if ($get_user_id) {
                $query->where('ref_user_id', $get_user_id->id);
            }
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))
                ->startOfDay();

            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))
                ->endOfDay();

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orderRooms = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        foreach ($orderRooms as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);

            if (!empty($order->payment_method)) {
                $order->badge_class = 'bg-info';
                $order->status_label = $order->payment_method;
            } elseif ($order->ref_status_id == 2) {
                $order->badge_class = 'bg-success';
                $order->status_label = 'อยู่ระหว่างใช้บริการ';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->between($startDateTime, $endDateTime)) {
                $order->badge_class = 'bg-primary';
                $order->status_label = 'จอง (ถึงเวลาแล้ว)';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->lessThan($startDateTime)) {
                $order->badge_class = 'bg-warning';
                $order->status_label = 'จอง';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->greaterThan($endDateTime)) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'จอง (เกินเวลา)';
            } elseif ($order->ref_status_id == 3) {
                $order->badge_class = 'bg-secondary';
                $order->status_label = 'ใช้บริการเสร็จสิ้น';
            } elseif ($order->ref_status_id == 4) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'ยกเลิก';
            } else {
                $order->badge_class = 'bg-dark';
                $order->status_label = 'ไม่ระบุ';
            }
        }

        return $orderRooms;
    }
    public function drink_com_pdf(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $orderRooms = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'seller', 'course'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3])
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('booking_date')
            ->orderBy('start_time');


        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $orderRooms->where('ref_branch_id', $userBranchId);
        }

        if (request()->filled('branch_id')) {
            $orderRooms->where('ref_branch_id', request()->branch_id);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $orderRooms->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request()->filled('user_id')) {
            $get_user_id = User::where('user_id', request('user_id'))->first();
            if ($get_user_id) {
                $orderRooms->where('ref_user_id', $get_user_id->id);
            }
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))
                ->startOfDay();

            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))
                ->endOfDay();

            $orderRooms->whereBetween('created_at', [$startDate, $endDate]);
        }


        $data['orderRooms'] = collect($orderRooms->get());
        $data['summary_total_price'] = $data['orderRooms']->sum('total_price');

        $data['userCommissionMap'] = \App\Models\UserHasRoomTypeCommission::select('ref_user_id', 'ref_room_type_id', 'ref_course_id', 'price', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_user_id}_{$r->ref_room_type_id}_{$r->ref_course_id}");

        $data['roomTypeCourseMap'] = \App\Models\RoomTypeHasCourse::select('ref_room_type_id', 'ref_course_id', 'price', 'commission', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_room_type_id}_{$r->ref_course_id}");

        $data['report_start_date'] = request('start_date') ?? date('d/m/Y');
        $data['report_end_date']   = request('end_date')   ?? date('d/m/Y');

        $html = view('admin.report.report-drink-com-pdf', $data)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    public function oversee_employee_datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $orderRooms = $this->OEgetOrderRooms($limit);


        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.report.report-overseeEmp-datatable', compact('orderRooms', 'branches'));
    }

    private function OEgetOrderRooms($limit)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'seller', 'course'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3])
            // ->select('orders.*')
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('booking_date')
            ->orderBy('start_time');

        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        // $DailySalesClosure = DailySalesClosure::orderBy("id", "DESC")->first();

        // if (@$DailySalesClosure) {
        //     $query->where('created_at', ">", $DailySalesClosure->date_time);
        // }

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
            if (request('start_time_filter')) {
                [$sh, $sm] = explode(':', request('start_time_filter'));
                $startDate->setTime((int)$sh, (int)$sm, 0);
            }
            if (request('end_time_filter')) {
                [$eh, $em] = explode(':', request('end_time_filter'));
                $endDate->setTime((int)$eh, (int)$em, 59);
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $orderRooms = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderRooms as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);

            if (!empty($order->payment_method)) {
                $order->badge_class = 'bg-info';
                $order->status_label = $order->payment_method;
            } elseif ($order->ref_status_id == 2) {
                $order->badge_class = 'bg-success';
                $order->status_label = 'อยู่ระหว่างใช้บริการ';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->between($startDateTime, $endDateTime)) {
                $order->badge_class = 'bg-primary';
                $order->status_label = 'จอง (ถึงเวลาแล้ว)';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->lessThan($startDateTime)) {
                $order->badge_class = 'bg-warning';
                $order->status_label = 'จอง';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->greaterThan($endDateTime)) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'จอง (เกินเวลา)';
            } elseif ($order->ref_status_id == 3) {
                $order->badge_class = 'bg-secondary';
                $order->status_label = 'ใช้บริการเสร็จสิ้น';
            } elseif ($order->ref_status_id == 4) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'ยกเลิก';
            } else {
                $order->badge_class = 'bg-dark';
                $order->status_label = 'ไม่ระบุ';
            }
        }

        return $orderRooms;
    }
    public function oversee_employee_pdf(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'seller', 'course'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3]) // ยกเลิก
            // ->select('orders.*')
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('booking_date')
            ->orderBy('start_time');

        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
            if (request('start_time_filter')) {
                [$sh, $sm] = explode(':', request('start_time_filter'));
                $startDate->setTime((int)$sh, (int)$sm, 0);
            }
            if (request('end_time_filter')) {
                [$eh, $em] = explode(':', request('end_time_filter'));
                $endDate->setTime((int)$eh, (int)$em, 59);
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $data['orderRooms'] = $query->get();

        $data['summary_data'] = $data['orderRooms']
            ->groupBy('ref_seller_id')
            ->map(function ($orders) {
                return [
                    'user_id'             => $orders->first()->seller->user_code ?? 'ไม่ระบุ',
                    'name'                => optional($orders->first()->seller)->name ?? 'ไม่ระบุ',
                    'total_price'         => $orders->where('ref_status_id', '!=', 4)->sum(function($o) {
                        return $o->total_price - ($o->addons_sum_price ?? 0) - ($o->products_sum_price ?? 0);
                    }),
                    'count'               => $orders->where('ref_status_id', '!=', 4)->count(),
                ];
            })
            ->sortByDesc('total_price')
            ->values();

        $data['report_start_date'] = request('start_date') ?? date('d/m/Y');
        $data['report_end_date']   = request('end_date')   ?? date('d/m/Y');


        $html = view('admin.report.report-overseeEmp-pdf', $data,)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    public function monthly_sale_datatable(Request $request)
    {
        $limit = $request->limit ?? 10;
        $orderRooms = $this->getOrderRooms($limit);

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }

        $userCommissionMap = \App\Models\UserHasRoomTypeCommission::select('ref_user_id', 'ref_room_type_id', 'ref_course_id', 'price', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_user_id}_{$r->ref_room_type_id}_{$r->ref_course_id}");

        $roomTypeCourseMap = \App\Models\RoomTypeHasCourse::select('ref_room_type_id', 'ref_course_id', 'price', 'commission', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_room_type_id}_{$r->ref_course_id}");

        // Compute summary totals over ALL filtered records (not just current page)
        $allOrders = $this->buildOrderRoomsQuery()->get();
        $totalNetSum = $totalNetCash = $totalNetTransfer = $totalNetCredit = $totalNetAl = 0.0;
        foreach ($allOrders as $order) {
            if ($order->ref_status_id == 4) continue;
            $coursePrice    = $order->total_price ?? 0;
            $usedCoupon     = 0;
            $usedCommission = 0;
            $ucKey = "{$order->ref_user_id}_{$order->ref_room_type_id}_{$order->service_laundry_cost}";
            $uc    = $userCommissionMap->get($ucKey);
            if ($uc && ($uc->price > 0 || $uc->coupon > 0)) {
                $usedCommission = $uc->price;
                $usedCoupon     = $uc->coupon;
            } else {
                $rtcKey       = "{$order->ref_room_type_id}_{$order->service_laundry_cost}";
                $roomTypeCourse = $roomTypeCourseMap->get($rtcKey);
                if ($roomTypeCourse) {
                    $usedCommission = $roomTypeCourse->commission;
                    $usedCoupon     = $roomTypeCourse->coupon;
                }
            }
            $rev = $coursePrice - ($usedCoupon + $usedCommission);
            $totalNetSum += $rev;
            if ($order->payment_method === 'cash')        $totalNetCash     += $rev;
            if ($order->payment_method === 'qr_code')     $totalNetTransfer += $rev;
            if ($order->payment_method === 'credit_card') $totalNetCredit   += $rev;
            if ($order->payment_method === 'alipay')      $totalNetAl       += $rev;
        }

        return view('admin.report.report-saleMonthly-datatable', compact(
            'orderRooms', 'branches',
            'userCommissionMap', 'roomTypeCourseMap',
            'totalNetSum', 'totalNetCash', 'totalNetTransfer', 'totalNetCredit', 'totalNetAl'
        ));
    }

    private function buildOrderRoomsQuery()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'room_type'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3])
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('created_at', 'ASC');

        // filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
            if (request('start_time_filter')) {
                [$sh, $sm] = explode(':', request('start_time_filter'));
                $startDate->setTime((int)$sh, (int)$sm, 0);
            }
            if (request('end_time_filter')) {
                [$eh, $em] = explode(':', request('end_time_filter'));
                $endDate->setTime((int)$eh, (int)$em, 59);
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query;
    }

    private function getOrderRooms($limit)
    {
        $query = $this->buildOrderRoomsQuery();

        $orderRooms = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderRooms as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);

            if (!empty($order->payment_method)) {
                $order->badge_class = 'bg-info';
                $order->status_label = $order->payment_method;
            } elseif ($order->ref_status_id == 2) {
                $order->badge_class = 'bg-success';
                $order->status_label = 'อยู่ระหว่างใช้บริการ';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->between($startDateTime, $endDateTime)) {
                $order->badge_class = 'bg-primary';
                $order->status_label = 'จอง (ถึงเวลาแล้ว)';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->lessThan($startDateTime)) {
                $order->badge_class = 'bg-warning';
                $order->status_label = 'จอง';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->greaterThan($endDateTime)) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'จอง (เกินเวลา)';
            } elseif ($order->ref_status_id == 3) {
                $order->badge_class = 'bg-secondary';
                $order->status_label = 'ใช้บริการเสร็จสิ้น';
            } elseif ($order->ref_status_id == 4) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'ยกเลิก';
            } else {
                $order->badge_class = 'bg-dark';
                $order->status_label = 'ไม่ระบุ';
            }
        }

        return $orderRooms;
    }
    public function monthly_sale(Request $request)
    {
        // $order = Order::withSum('addons', 'price')->get();
        // return $this->getOrderRooms(1)[0]->addons_sum_price;
        $data['page_url'] = "admin/report/monthly-sale";

        return view('admin.report.report-saleMonthly', $data);
    }
    public function monthly_sale_pdf(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::withSum('addons', 'price')
            ->withSum('addons', 'coupon')
            ->withSum('products', 'price')
            ->with(['branch', 'customer', 'user', 'room', 'status', 'room_type'])
            ->where('type', 1)
            ->whereIn('ref_status_id', [2, 3])
            // ->select('orders.*')
            ->orderByRaw("
                        CASE
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                            ELSE 8 -- ไม่ระบุ
                        END
                    ")
            ->orderBy('ref_user_id')
            ->orderBy('booking_date')
            ->orderBy('start_time');
        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        // $DailySalesClosure = DailySalesClosure::orderBy("id","DESC")->first();

        // if (@$DailySalesClosure) {
        //     $query->where('created_at', ">" ,$DailySalesClosure->date_time);
        // }

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (request('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
            if (request('start_time_filter')) {
                [$sh, $sm] = explode(':', request('start_time_filter'));
                $startDate->setTime((int)$sh, (int)$sm, 0);
            }
            if (request('end_time_filter')) {
                [$eh, $em] = explode(':', request('end_time_filter'));
                $endDate->setTime((int)$eh, (int)$em, 59);
            }
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $data['orderRooms'] = $query->get();

        // Pre-fetch all UserHasRoomTypeCommission rows as a lookup map keyed by userId_roomTypeId_courseId
        $data['userCommissionMap'] = \App\Models\UserHasRoomTypeCommission::select('ref_user_id', 'ref_room_type_id', 'ref_course_id', 'price', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_user_id}_{$r->ref_room_type_id}_{$r->ref_course_id}");

        // Pre-fetch all RoomTypeHasCourse rows as a lookup map keyed by roomTypeId_courseId
        $data['roomTypeCourseMap'] = \App\Models\RoomTypeHasCourse::select('ref_room_type_id', 'ref_course_id', 'price', 'commission', 'coupon')
            ->get()
            ->keyBy(fn($r) => "{$r->ref_room_type_id}_{$r->ref_course_id}");

        $data['discounts_summary'] = $data['orderRooms']->sum('discount');
        $data['addons_sum_price'] = $data['orderRooms']->sum('addons_sum_price');
        $data['summary_receive_price'] = $data['orderRooms']->sum('price');
        $data['summary_receive_price_after_discount'] = $data['orderRooms']->sum('total_price');
        $data['report_start_date'] = request('start_date') ?? date('d/m/Y');
        $data['report_end_date']   = request('end_date')   ?? date('d/m/Y');
        $nonCancelledOrders = $data['orderRooms']->where('ref_status_id', '!=', 4);
        $data['summary_type_payment_cash'] = $nonCancelledOrders->where('payment_method', 'cash')->sum('total_price');
        $data['summary_type_payment_credit'] = $nonCancelledOrders->where('payment_method', 'credit_card')->sum('total_price');
        $data['summary_type_payment_transfer'] = $nonCancelledOrders->where('payment_method', 'qr_code')->sum('total_price');
        $data['summary_type_payment_al'] = $nonCancelledOrders->where('payment_method', 'alipay')->sum('total_price');


        $html = view('admin.report.report-saleMonthly-pdf', $data)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    public function oversee_employee(Request $request)
    {
        $data['page_url'] = "admin/report/oversee-employee";

        return view('admin.report.report-overseeEmp', $data);
    }
}
