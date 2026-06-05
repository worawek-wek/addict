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
    public function get_history_by_round($round)
    {
        // $user = Auth::user();
        $results = HistoryCommission::where('round', $round)->get();

        $data['list_data'] = $results;
        return view('admin.commission.view_sales_history', $data);
    }
    public function view_sales_datatable(Request $request)
    {
        // $user = Auth::user();
        $results = User::withTrashed()->where('ref_position_id', 1)->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }

        if (@$request->ref_branch_id) {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }
        if (request('start_date')) {
            [$data['start_date'], $data['end_date']] = AdminBusinessDay::rangeFromRequest($request);
        }

        $limit = AdminBusinessDay::DEFAULT_PER_PAGE;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/card_stock_report';
        $data['list_data'] = $results;
        return view('admin.commission.view_sales_table', $data);
    }
    
    public function view_sales_pdf(Request $request)
    {
        $results = User::withTrashed()->where('ref_position_id', 1)->orderBy('id');

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

        if (request('start_date')) {
            [$data['start_date'], $data['end_date']] = AdminBusinessDay::rangeFromRequest($request);
        }

        $results = $results->get();
        
        $data['list_data'] = $results;

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
        // $user = Auth::user();
        $results = User::withTrashed()->where('ref_position_id', 1)->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }

        if (@$request->ref_branch_id) {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }
        if (request('start_date')) {
            [$data['start_date'], $data['end_date']] = AdminBusinessDay::rangeFromRequest($request);
        }

        $limit = AdminBusinessDay::DEFAULT_PER_PAGE;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/card_stock_report';
        $data['list_data'] = $results;
        return view('admin.commission.drink_view_sales_table', $data);
    }
    
    public function drink_view_sales_pdf(Request $request)
    {
        $results = User::withTrashed()->where('ref_position_id', 1)->orderBy('id');

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

        if (request('start_date')) {
            [$data['start_date'], $data['end_date']] = AdminBusinessDay::rangeFromRequest($request);
        }

        $results = $results->get();
        
        $data['list_data'] = $results;

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

            $results = User::withTrashed()->where('ref_position_id', 1)->orderBy('id');

            if (request('name')) {
                $results->Where(function ($query) use ($request) {
                                        $query->where('name','LIKE','%'.request('name').'%')
                                                ->orWhere('nickname','LIKE','%'.request('name').'%');
                                    });
            }

            if (request('start_date')) {
                [$start_date, $end_date] = AdminBusinessDay::rangeFromRequest($request);
            }

            $results = $results->get();

            $history = HistoryCommission::orderBy('round', 'desc')->first();
            
            $round = 0;

            foreach($results as $row){

                $total_price = OrderHasProduct::whereHas('order', function ($query) use ($row) {
                                                            $query->where('ref_seller_id', $row->id)
                                                                    ->whereIn('type', [1, 2]);
                                                        })
                                                        ->whereBetween('created_at', [$start_date, $end_date])
                                                        ->sum('price') ?? 0;
                $sale_commission = SalesCommissionTier::where('type', 1)->where('min_sales_amount', '<=', $total_price)->where('max_sales_amount', '>=', $total_price)->first(); // ดึงการตั้งค่า คอมมิชชั่น ที่ตรงกับยอดขายรวม
                $commission_price = $sale_commission->commission_price ?? 0; // เปอร์เซ็นต์ * ยอดขายรวม / 100
                if($sale_commission && $sale_commission->commission_by == 1){ // ถ้าเป็น เปอร์เซ็นต์
                    $commission_price = $sale_commission->commission_rate*$total_price/100; // เปอร์เซ็นต์ * ยอดขายรวม / 100
                    $sale_commission->commission_price = $sale_commission->commission_rate."%"; // เปอร์เซ็นต์ * ยอดขายรวม / 100
                }
                // return $commission_price;
                
                if ($history) {
                    $round = $history->round;
                }

                $product = new HistoryCommission;
                $product->round = $round+1;
                $product->type = 1;
                $product->ref_staff_id = $row->id;
                $product->commission = @$commission_price ?? 0;
                $product->sales_received = $total_price;
                $product->commission_rate = @$sale_commission->commission_price ?? 0.00;
                $product->min_sales_amount = @$sale_commission->min_sales_amount ?? 0.00;
                $product->max_sales_amount = @$sale_commission->max_sales_amount ?? 0.00;
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
