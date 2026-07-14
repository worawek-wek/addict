<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionsHistory;
use App\Models\HistoryCommission;
use App\Models\MassageCommission;
use App\Models\OrderHasProduct;
use App\Models\SalesCommissionTier;
use App\Models\User;
use App\Models\Branch;
use App\Support\AdminBusinessDay;
use App\Support\MamaCommissionCalculator;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

class CommissionController extends Controller
{
    // แสดงรายการคอมมิชชั่นทั้งหมด
    public function index()
    {
        $user = auth()->user();
        if ($user->ref_position_id == 0) {
            // แสดงทุกสาขา เฉพาะที่มี ref_user_id
            $commissions = MassageCommission::with(['user.branch'])
                ->whereNotNull('ref_user_id')
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $userBranchId = $user->ref_branch_id ?? null;
            $commissions = MassageCommission::with(['user.branch'])
                ->whereNotNull('ref_user_id')
                ->whereHas('user', function ($q) use ($userBranchId) {
                    $q->where('ref_branch_id', $userBranchId);
                })
                ->orderBy('id', 'desc')
                ->get();
        }
        return view('admin.commission.index', compact('commissions'));
    }
    // แสดงค่าคอมมิชชั่นพนักงานนวด
    public function view_massage(Request $request)
    {
        if (auth()->user()->ref_position_id == 0) {
            // superadmin: แสดงพนักงานนวดทุกสาขา
            $users = User::withTrashed()
                ->with(['branch', 'position'])
                ->where('ref_position_id', 2)
                ->get();
        } else {
            $userBranchId = auth()->user()->ref_branch_id ?? null;
            $users = User::withTrashed()
                ->with(['branch', 'position'])
                ->where('ref_position_id', 2)
                ->where('ref_branch_id', $userBranchId)
                ->get();
        }

        $staffData = [];
        $range = $request->has('range') ? $request->input('range') : 'today';
        $start = $request->input('start');
        $end = $request->input('end');
        $today = now();
        if ($range === 'custom' && $start && $end) {
            [$startRange, $endRange] = AdminBusinessDay::rangeFromRequest(new Request([
                'start_date' => $start,
                'end_date' => $end,
            ]));
        } elseif ($range === 'today' || !$request->has('range')) {
            [$startRange, $endRange] = AdminBusinessDay::currentRange();
        } elseif ($range === '1') {
            [$startRange, $endRange] = AdminBusinessDay::singleDateRange($today->copy()->subDay()->format('Y-m-d'));
        } else {
            $days = in_array($range, ['7', '14', '30']) ? (int)$range : 1;
            [$startRange, $endRange] = AdminBusinessDay::rangeForPresetDays($days);
        }
        $sqlRange = AdminBusinessDay::sqlRange([$startRange, $endRange]);
        $startDate = $startRange->toDateString();
        $endDate = $endRange->toDateString();

        foreach ($users as $user) {
            $commission = \App\Models\CommissionsHistory::where('user_message_id', $user->id)
                ->whereHas('order', function ($q) use ($sqlRange) {
                    $q->whereRaw("CONCAT(booking_date, ' ', start_time) BETWEEN ? AND ?", $sqlRange);
                })
                ->sum('commission_massage_amount');
            $cheer_charge = \App\Models\CommissionsHistory::where('user_message_id', $user->id)
                ->whereHas('order', function ($q) use ($sqlRange) {
                    $q->whereRaw("CONCAT(booking_date, ' ', start_time) BETWEEN ? AND ?", $sqlRange);
                })
                ->sum('price_options_massage');
            $staffData[] = [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'branch' => $user->branch ? $user->branch->name : null,
                'position' => $user->position ? $user->position->position_name : null,
                'commission' => $commission,
                'cheer_charge' => $cheer_charge,
            ];
        }
        if ($request->ajax() || $request->input('ajax') == '1') {
            return view('admin.commission._table_body', compact('staffData'));
        }
        return view('admin.commission.view_massage', compact('staffData'));
    }

    // แสดงค่าคอมมิชชั่นพนักงานขาย นวด+สินค้า
    public function view_sales(Request $request)
    {
        $rounds = HistoryCommission::whereIn('id', function($query) {
                                                $query->selectRaw('MAX(id)')->from('history_commissions')->groupBy('round');
                                            })->get();

        $branch = Branch::orderBy('name')->get();

        $page_url = "admin/commission/view-sales";
        return view('admin.commission.view_sales', compact('page_url', 'rounds', 'branch'));
    }

    // Dashboard สรุปคอมมิชชั่นรวม (นวด+สินค้า + ดื่ม) รายวัน/รายเดือน
    public function dashboard(Request $request)
    {
        $branch = Branch::orderBy('name')->get();
        $page_url = 'admin/commission/dashboard';
        return view('admin.commission.dashboard', compact('page_url', 'branch'));
    }

    public function dashboard_datatable(Request $request)
    {
        $period = $request->input('period', 'month');
        if ($period === 'day') {
            [$start, $end] = $request->filled('date')
                ? AdminBusinessDay::singleDateRange($request->input('date'))
                : AdminBusinessDay::currentRange();
        } else {
            [$start, $end] = AdminBusinessDay::monthRange($request->input('ym') ?: null);
        }

        $results = User::withTrashed()->mama()->orderBy('id');
        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . request('name') . '%')
                    ->orWhere('nickname', 'LIKE', '%' . request('name') . '%');
            });
        }
        if (@$request->ref_branch_id) {
            $results->where('ref_branch_id', $request->ref_branch_id);
        }

        $rows = $results->get()->map(function ($staff) use ($start, $end) {
            $service = MamaCommissionCalculator::computeForStaff($staff, $start, $end, 'service');
            $drink = MamaCommissionCalculator::computeForStaff($staff, $start, $end, 'drink');
            return [
                'staff' => $staff,
                'service' => $service,
                'drink' => $drink,
                'total' => $service['commission_amount'] + $drink['commission_amount'],
            ];
        });

        $summary = [
            'count' => $rows->count(),
            'service' => $rows->sum(fn ($r) => $r['service']['commission_amount']),
            'drink' => $rows->sum(fn ($r) => $r['drink']['commission_amount']),
            'total' => $rows->sum('total'),
        ];

        return view('admin.commission.dashboard_table', compact('rows', 'summary', 'start', 'end', 'period'));
    }

    public function get_history_by_round($round)
    {
        // $user = Auth::user();
        $results = HistoryCommission::where('round', $round)->get();

        $data['list_data'] = $results;
        return view('admin.commission.view_sales_history', $data);
    }
    public function view_sales_datatable(Request $request)
    {
        // มาม่า/ทีมเชียร์ = ทุกตำแหน่งยกเว้นพนักงานนวด (position id = 2)
        $results = User::withTrashed()->mama()->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }

        if (@$request->ref_branch_id) {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }

        // ช่วงเวลาสะสม (ปกติ = ช่วงเดือนจากตัวกรอง) ถ้าไม่ส่งมาใช้ค่าวันทำการปัจจุบัน
        [$start, $end] = AdminBusinessDay::rangeFromRequest($request);
        $data['start_date'] = $start;
        $data['end_date'] = $end;

        $limit = AdminBusinessDay::DEFAULT_PER_PAGE;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        // คำนวณ rank/ยอด/รอบ/คอมฯ ต่อคน (เฉพาะหน้าที่แสดง)
        $data['rows'] = $results->getCollection()->map(function ($staff) use ($start, $end) {
            return [
                'staff' => $staff,
                'c' => MamaCommissionCalculator::computeForStaff($staff, $start, $end),
            ];
        });

        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['page_url'] = 'admin/commission/view-sales';
        $data['list_data'] = $results;
        return view('admin.commission.view_sales_table', $data);
    }
    
    public function view_sales_pdf(Request $request)
    {
        $results = User::withTrashed()->mama()->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }

/////////////////////////////////////////////////////////////////////////////////////////
        if (@request('ref_branch_id')) {
            $results->where('ref_branch_id', request('ref_branch_id'));
        }
/////////////////////////////////////////////////////////////////////////////////////////

        [$start, $end] = AdminBusinessDay::rangeFromRequest($request);
        $data['start_date'] = $start;
        $data['end_date'] = $end;

        // คำนวณด้วยระบบ Rank เดียวกับหน้าจอ
        $data['rows'] = $results->get()->map(function ($staff) use ($start, $end) {
            return [
                'staff' => $staff,
                'c' => MamaCommissionCalculator::computeForStaff($staff, $start, $end),
            ];
        });

        $html = view('admin.commission.view_sales_pdf', $data,)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    // แสดงค่าคอมมิชชั่นพนักงานขาย ดื่ม
    public function drink_view_sales(Request $request)
    {
        $page_url = "admin/commission/drink-view-sales";
        $branch = Branch::orderBy('name')->get();
        return view('admin.commission.drink_view_sales', compact('page_url','branch'));
    }
    public function drink_view_sales_datatable(Request $request)
    {
        // มาม่า/ทีมเชียร์ = ทุกตำแหน่งยกเว้นพนักงานนวด
        $results = User::withTrashed()->mama()->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }

        if (@$request->ref_branch_id) {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }

        [$start, $end] = AdminBusinessDay::rangeFromRequest($request);
        $data['start_date'] = $start;
        $data['end_date'] = $end;

        $limit = AdminBusinessDay::DEFAULT_PER_PAGE;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        // คำนวณด้วยระบบ Rank หมวด drink
        $data['rows'] = $results->getCollection()->map(function ($staff) use ($start, $end) {
            return [
                'staff' => $staff,
                'c' => MamaCommissionCalculator::computeForStaff($staff, $start, $end, 'drink'),
            ];
        });

        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['page_url'] = 'admin/commission/drink-view-sales';
        $data['list_data'] = $results;
        return view('admin.commission.drink_view_sales_table', $data);
    }

    public function drink_view_sales_pdf(Request $request)
    {
        $results = User::withTrashed()->mama()->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }

/////////////////////////////////////////////////////////////////////////////////////////
        if (@request('ref_branch_id')) {
            $results->where('ref_branch_id', request('ref_branch_id'));
        }
/////////////////////////////////////////////////////////////////////////////////////////

        [$start, $end] = AdminBusinessDay::rangeFromRequest($request);
        $data['start_date'] = $start;
        $data['end_date'] = $end;

        $data['rows'] = $results->get()->map(function ($staff) use ($start, $end) {
            return [
                'staff' => $staff,
                'c' => MamaCommissionCalculator::computeForStaff($staff, $start, $end, 'drink'),
            ];
        });

        $html = view('admin.commission.drink_view_sales_pdf', $data,)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
    }
    // public function view_sales(Request $request)
    // {
    //     $usersQuery = \App\Models\User::with(['branch', 'position'])
    //         ->where('ref_position_id', 1); // เฉพาะพนักงานขาย
    //     $users = $usersQuery->get();

    //     $staffData = [];
    //     $range = $request->input('range', '1');
    //     $start = $request->input('start');
    //     $end = $request->input('end');
    //     $today = now();

    //     if ($range === 'custom' && $start && $end) {
    //         $startDate = date('Y-m-d', strtotime($start));
    //         $endDate = date('Y-m-d', strtotime($end));
    //     } else {
    //         $days = in_array($range, ['1', '7', '14', '30']) ? (int)$range : 1;
    //         $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
    //         $endDate = $today->format('Y-m-d');
    //     }

    //     foreach ($users as $user) {
    //         // Get all orders for this seller in the date range
    //         $orders = \App\Models\Order::where('ref_seller_id', $user->id)
    //             ->whereDate('booking_date', '>=', $startDate)
    //             ->whereDate('booking_date', '<=', $endDate)
    //             ->get();

    //         $totalSales = $orders->sum('total_price');
    //         $commission = 0;

    //         // Find tier for this branch and sales amount
    //         $tier = \App\Models\SalesCommissionTier::where('ref_branch_id', $user->ref_branch_id)
    //             ->where('min_sales_amount', '<=', $totalSales)
    //             ->where('max_sales_amount', '>=', $totalSales)
    //             ->first();

    //         if ($tier) {
    //             $commission = $totalSales * ($tier->commission_rate / 100);
    //         }

    //         // ดึง cheer_charge จาก commissions_history โดย filter ตาม booking_date ของ order
    //         $cheer_charge = CommissionsHistory::where('user_sales_id', $user->id)
    //             ->whereHas('order', function ($q) use ($startDate, $endDate) {
    //                 $q->whereDate('booking_date', '>=', $startDate)
    //                     ->whereDate('booking_date', '<=', $endDate);
    //             })
    //             ->sum('price_options_sales');

    //         $staffData[] = [
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'nickname' => $user->nickname,
    //             'branch' => $user->branch ? $user->branch->name : null,
    //             'position' => $user->position ? $user->position->position_name : null,
    //             'commission' => $commission,
    //             'cheer_charge' => $cheer_charge,
    //         ];
    //     }
    //     if ($request->ajax() || $request->input('ajax') == '1') {
    //         return view('admin.commission._table_body', compact('staffData'));
    //     }
    //     return view('admin.commission.view_sales', compact('staffData'));
    // }
    // แสดงฟอร์มสร้างใหม่
    public function create()
    {
        $user = auth()->user();
        if ($user->ref_position_id == 0) {
            // แสดงพนักงานทุกคน (ref_position_id = 2)
            $users = User::with('branch')
                ->where('ref_position_id', 2)
                ->leftJoin('branchs', 'users.ref_branch_id', '=', 'branchs.id')
                ->orderBy('branchs.name')
                ->orderBy('users.name')
                ->select('users.*')
                ->get();
        } else {
            $userBranchId = $user->ref_branch_id ?? null;
            $users = User::with('branch')
                ->where('ref_position_id', 2)
                ->where('ref_branch_id', $userBranchId)
                ->leftJoin('branchs', 'users.ref_branch_id', '=', 'branchs.id')
                ->orderBy('branchs.name')
                ->orderBy('users.name')
                ->select('users.*')
                ->get();
        }
        $positions = \App\Models\Position::where('id', '!=', 0)->orderBy('position_name')->get();
        $addonOptions = \App\Models\AddonOption::all();
        // Pass all options, filter in JS by branch
        return view('admin.commission.create', compact('users', 'positions', 'addonOptions'));
    }

    // บันทึกข้อมูลใหม่
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ref_user_id' => 'required|integer',
                'ref_position_id' => 'required|integer',
                'ref_addon_options_id' => 'nullable|integer',
                'service_name' => 'required|string|max:100',
                'service_duration' => 'nullable|string',
                'commission_amount' => 'nullable|numeric',
                'commission_percent' => 'nullable|numeric|min:0|max:100',
            ]);
            // Map ref_addon_options_id to addon_options_id
            if (array_key_exists('ref_addon_options_id', $validated)) {
                $validated['addon_options_id'] = $validated['ref_addon_options_id'];
                unset($validated['ref_addon_options_id']);
            }

            // ถ้าเลือก AddonOption ให้เคลียร์ service_duration
            if (!empty($validated['ref_addon_options_id'])) {
                $validated['service_duration'] = null;
            }
            // ถ้า service_name มีคำว่า "นวด" ต้องกรอก service_duration (ถ้าไม่ได้เลือก AddonOption)
            if (empty($validated['ref_addon_options_id']) && strpos($validated['service_name'], 'นวด') !== false && empty($validated['service_duration'])) {
                return redirect()->route('commission.create')->withErrors(['service_duration' => 'กรุณากรอกระยะเวลาบริการนวด'])->withInput();
            }

            // Custom validation: at least one of commission_amount or commission_percent must be present
            if (empty($validated['commission_amount']) && empty($validated['commission_percent'])) {
                return redirect()->route('commission.create')->withErrors(['commission_amount' => 'กรุณากรอกจำนวนเงินหรือเปอร์เซ็นต์คอมมิชชั่นอย่างน้อย 1 ช่อง'])->withInput();
            }

            // เพิ่ม ref_branch_id จากผู้ใช้ที่เลือก
            if (!empty($validated['ref_user_id'])) {
                $userObj = User::find($validated['ref_user_id']);
                $validated['ref_branch_id'] = $userObj ? $userObj->ref_branch_id : null;
            } else {
                $validated['ref_branch_id'] = null;
            }

            // Use MassageCommission model instead of Commission
            $commission = MassageCommission::create($validated);
            return redirect()->route('commission.index')->with('success', 'เพิ่มคอมมิชชั่นนวดสำเร็จ');
        } catch (Exception $e) {
            return redirect()->route('commission.create')->withErrors(['db' => $e->getMessage()])->withInput();
        }
    }

    public function save_commission_history(Request $request)
    {
        try {

            // ให้ตรงกับหน้ารายงาน (มาม่า = ทุกตำแหน่งยกเว้นนวด) ไม่ใช่เฉพาะ position 1
            $results = User::withTrashed()->mama()->orderBy('id');

            if (request('name')) {
                $results->Where(function ($query) use ($request) {
                                        $query->where('name','LIKE','%'.request('name').'%')
                                                ->orWhere('nickname','LIKE','%'.request('name').'%');
                                    });
            }

            [$start_date, $end_date] = AdminBusinessDay::rangeFromRequest($request);

            $results = $results->get();

            $history = HistoryCommission::orderBy('round', 'desc')->first();
            $round = ($history ? $history->round : 0) + 1;

            // คอลัมน์ snapshot rank (มีเมื่อรัน SQL เพิ่มแล้ว)
            $hasRankCols = \Illuminate\Support\Facades\Schema::hasColumn('history_commissions', 'rank_no');

            foreach($results as $row){

                $c = MamaCommissionCalculator::computeForStaff($row, $start_date, $end_date);

                $product = new HistoryCommission;
                $product->round = $round;
                $product->type = 1;
                $product->ref_staff_id = $row->id;
                $product->commission = $c['commission_amount'];
                $product->sales_received = $c['accumulated_sales'];
                $product->commission_rate = $this->rankRateLabel($c);
                $product->min_sales_amount = $c['applied_min_threshold'];
                $product->max_sales_amount = 0;
                if ($hasRankCols) {
                    $product->rank_no = $c['rank_no'];
                    $product->accumulated_rounds = $c['accumulated_rounds'];
                    $product->mode = $c['mode'];
                    $product->payout_type = $c['applied_payout_type'];
                }
                $product->from_date = $start_date;
                $product->to_date  =  $end_date;
                $product->save();
            }

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'บันทึกไม่สำเร็จ',
                'error'   => $err->getMessage()
            ], 500);
        }
        //
    }
    /**
     * ป้ายเรต/เกณฑ์ ของผลลัพธ์จาก MamaCommissionCalculator (ใช้ในประวัติ/PDF)
     */
    private function rankRateLabel(array $c): string
    {
        if (($c['rank_no'] ?? 0) <= 0) {
            return '-';
        }
        switch ($c['applied_payout_type']) {
            case 'fixed_per_round':
                return number_format($c['applied_fixed_amount'] ?? 0, 2) . '/รอบ';
            case 'fixed':
                return number_format($c['applied_fixed_amount'] ?? 0, 2) . ' คงที่';
            case 'percent':
            default:
                return rtrim(rtrim(number_format($c['applied_rate'], 2), '0'), '.') . '%';
        }
    }

    // แสดงฟอร์มแก้ไข
    public function edit($id)
    {
        $commission = MassageCommission::where('id', $id)->firstOrFail();
        $users = \App\Models\User::with('branch')
            ->where('ref_position_id', 2)
            ->leftJoin('branchs', 'users.ref_branch_id', '=', 'branchs.id')
            ->orderBy('branchs.name')
            ->orderBy('users.name')
            ->select('users.*')
            ->get();
        $positions = \App\Models\Position::where('id', '!=', 0)->orderBy('position_name')->get();
        $addonOptions = \App\Models\AddonOption::all();
        return view('admin.commission.edit', compact('commission', 'users', 'positions', 'addonOptions'));
    }

    // อัปเดตข้อมูล
    public function update(Request $request, $id)
    {
        $commission = MassageCommission::findOrFail($id);
        $validated = $request->validate([
            'ref_position_id' => 'required|integer',
            'ref_addon_options_id' => 'nullable|integer',
            'service_name' => 'required|string|max:100',
            'service_duration' => 'nullable|string',
            'commission_amount' => 'nullable|numeric',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
        ]);
        // Map ref_addon_options_id to addon_options_id
        if (array_key_exists('ref_addon_options_id', $validated)) {
            $validated['addon_options_id'] = $validated['ref_addon_options_id'];
            unset($validated['ref_addon_options_id']);
        }
        // ถ้าเลือก AddonOption ให้เคลียร์ service_duration
        if (!empty($validated['ref_addon_options_id'])) {
            $validated['service_duration'] = null;
        }
        // ถ้า service_name มีคำว่า "นวด" ต้องกรอก service_duration (ถ้าไม่ได้เลือก AddonOption)
        if (empty($validated['ref_addon_options_id']) && strpos($validated['service_name'], 'นวด') !== false && empty($validated['service_duration'])) {
            return redirect()->route('commission.edit', $commission->id)->withErrors(['service_duration' => 'กรุณากรอกระยะเวลาบริการนวด'])->withInput();
        }

        // Custom validation: at least one of commission_amount or commission_percent must be present
        if (empty($validated['commission_amount']) && empty($validated['commission_percent'])) {
            return redirect()->route('commission.edit', $commission->id)->withErrors(['commission_amount' => 'กรุณากรอกจำนวนเงินหรือเปอร์เซ็นต์คอมมิชชั่นอย่างน้อย 1 ช่อง'])->withInput();
        }

        $commission->update($validated);
        return redirect()->route('commission.index')->with('success', 'แก้ไขคอมมิชชั่นนวดสำเร็จ');
    }

    // ลบข้อมูล
    public function destroy($id)
    {
        MassageCommission::destroy($id);
        return redirect()->route('commission.index')->with('success', 'ลบคอมมิชชั่นสำเร็จ');
    }

    public function salesOrders(Request $request)
    {
        $userId = $request->input('user_id');
        $range = $request->input('range', '1');
        $start = $request->input('start');
        $end = $request->input('end');
        $today = now();

        if ($range === 'custom' && $start && $end) {
            [$startRange, $endRange] = AdminBusinessDay::rangeFromRequest(new Request([
                'start_date' => $start,
                'end_date' => $end,
            ]));
        } elseif ($range === '1') {
            [$startRange, $endRange] = AdminBusinessDay::singleDateRange($today->copy()->subDay()->format('Y-m-d'));
        } else {
            $days = in_array($range, ['7', '14', '30']) ? (int)$range : 1;
            [$startRange, $endRange] = AdminBusinessDay::rangeForPresetDays($days);
        }
        $sqlRange = AdminBusinessDay::sqlRange([$startRange, $endRange]);
        $startDate = $startRange->toDateString();
        $endDate = $endRange->toDateString();

        $orders = \App\Models\Order::with(['customer', 'branch'])
            ->where('ref_seller_id', $userId)
            ->whereRaw("CONCAT(booking_date, ' ', start_time) BETWEEN ? AND ?", $sqlRange)
            ->orderBy('booking_date', 'desc')
            ->get();

        $user = \App\Models\User::withTrashed()->find($userId);

        return view('admin.commission.sales_orders', compact('orders', 'user', 'startDate', 'endDate'));
    }
    public function orderDetailAjax($orderId)
    {
        $order = \App\Models\Order::with(['customer', 'branch', 'user', 'addons', 'products'])->findOrFail($orderId);
        return view('admin.commission._order_detail_modal', compact('order'));
    }
    public function massageOrders(Request $request)
    {
        $userId = $request->input('user_id');
        $range = $request->input('range', '1');
        $start = $request->input('start');
        $end = $request->input('end');
        $today = now();

        if ($range === 'custom' && $start && $end) {
            [$startRange, $endRange] = AdminBusinessDay::rangeFromRequest(new Request([
                'start_date' => $start,
                'end_date' => $end,
            ]));
        } elseif ($range === '1') {
            [$startRange, $endRange] = AdminBusinessDay::singleDateRange($today->copy()->subDay()->format('Y-m-d'));
        } else {
            $days = in_array($range, ['7', '14', '30']) ? (int)$range : 1;
            [$startRange, $endRange] = AdminBusinessDay::rangeForPresetDays($days);
        }
        $sqlRange = AdminBusinessDay::sqlRange([$startRange, $endRange]);
        $startDate = $startRange->toDateString();
        $endDate = $endRange->toDateString();

        $orders = \App\Models\Order::with(['customer', 'branch'])
            ->where('ref_user_id', $userId)
            ->whereRaw("CONCAT(booking_date, ' ', start_time) BETWEEN ? AND ?", $sqlRange)
            ->orderBy('booking_date', 'desc')
            ->get();

        $user = \App\Models\User::withTrashed()->find($userId);

        return view('admin.commission.sales_orders', compact('orders', 'user', 'startDate', 'endDate'));
    }
}
