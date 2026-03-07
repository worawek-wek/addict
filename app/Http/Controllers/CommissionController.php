<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionsHistory;
use App\Models\MassageCommission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            $users = User::with(['branch', 'position'])
                ->where('ref_position_id', 2)
                ->get();
        } else {
            $userBranchId = auth()->user()->ref_branch_id ?? null;
            $users = User::with(['branch', 'position'])
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
            $startDate = date('Y-m-d', strtotime($start));
            $endDate = date('Y-m-d', strtotime($end));
        } elseif ($range === 'today' || !$request->has('range')) {
            $startDate = $today->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
        } elseif ($range === '1') {
            $yesterday = $today->copy()->subDay();
            $startDate = $yesterday->format('Y-m-d');
            $endDate = $yesterday->format('Y-m-d');
        } else {
            $days = in_array($range, ['7', '14', '30']) ? (int)$range : 1;
            $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
        }

        foreach ($users as $user) {
            $commission = \App\Models\CommissionsHistory::where('user_message_id', $user->id)
                ->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereDate('booking_date', '>=', $startDate)
                        ->whereDate('booking_date', '<=', $endDate);
                })
                ->sum('commission_massage_amount');
            $cheer_charge = \App\Models\CommissionsHistory::where('user_message_id', $user->id)
                ->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereDate('booking_date', '>=', $startDate)
                        ->whereDate('booking_date', '<=', $endDate);
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

    // แสดงค่าคอมมิชชั่นพนักงานขาย view_sales_datatable
    public function view_sales(Request $request)
    {
        $page_url = "admin/commission/view-sales";
        // $usersQuery = \App\Models\User::with(['branch', 'position'])
        //     ->where('ref_position_id', 1); // เฉพาะพนักงานขาย
        // $users = $usersQuery->get();

        // $staffData = [];
        // $range = $request->input('range', '1');
        // $start = $request->input('start');
        // $end = $request->input('end');
        // $today = now();

        // if ($range === 'custom' && $start && $end) {
        //     $startDate = date('Y-m-d', strtotime($start));
        //     $endDate = date('Y-m-d', strtotime($end));
        // } else {
        //     $days = in_array($range, ['1', '7', '14', '30']) ? (int)$range : 1;
        //     $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
        //     $endDate = $today->format('Y-m-d');
        // }

        // foreach ($users as $user) {
        //     // Get all orders for this seller in the date range
        //     $orders = \App\Models\Order::where('ref_seller_id', $user->id)
        //         ->whereDate('booking_date', '>=', $startDate)
        //         ->whereDate('booking_date', '<=', $endDate)
        //         ->get();

        //     $totalSales = $orders->sum('total_price');
        //     $commission = 0;

        //     // Find tier for this branch and sales amount
        //     $tier = \App\Models\SalesCommissionTier::where('ref_branch_id', $user->ref_branch_id)
        //         ->where('min_sales_amount', '<=', $totalSales)
        //         ->where('max_sales_amount', '>=', $totalSales)
        //         ->first();

        //     if ($tier) {
        //         $commission = $totalSales * ($tier->commission_rate / 100);
        //     }

        //     // ดึง cheer_charge จาก commissions_history โดย filter ตาม booking_date ของ order
        //     $cheer_charge = CommissionsHistory::where('user_sales_id', $user->id)
        //         ->whereHas('order', function ($q) use ($startDate, $endDate) {
        //             $q->whereDate('booking_date', '>=', $startDate)
        //                 ->whereDate('booking_date', '<=', $endDate);
        //         })
        //         ->sum('price_options_sales');

        //     $staffData[] = [
        //         'id' => $user->id,
        //         'name' => $user->name,
        //         'nickname' => $user->nickname,
        //         'branch' => $user->branch ? $user->branch->name : null,
        //         'position' => $user->position ? $user->position->position_name : null,
        //         'commission' => $commission,
        //         'cheer_charge' => $cheer_charge,
        //     ];
        // }
        // if ($request->ajax() || $request->input('ajax') == '1') {
        //     return view('admin.commission._table_body', compact('staffData'));
        // }
        return view('admin.commission.view_sales', compact('page_url'));
    }
    public function view_sales_datatable(Request $request)
    {
        // $user = Auth::user();
        $results = User::where('ref_position_id', 1)->orderBy('id');

        if (request('name')) {
            $results->Where(function ($query) use ($request) {
                                    $query->where('name','LIKE','%'.request('name').'%')
                                            ->orWhere('nickname','LIKE','%'.request('name').'%');
                                });
        }
        
        // if (@$request->created_at) {
        //     $created_at = Carbon::createFromFormat('d/m/Y', $request->created_at)->format('Y-m-d');
        //     $results = $results->WhereDate('drink_card_stocks.created_at', $created_at);
        // }

        // if (request()->filled('search')) {
        //     $search = request()->search;
        //     $results->Where(function ($query) use ($request) {

        //                             $query->where('drink_card_stocks.label','LIKE','%'.$request->search.'%')

        //                                 ->orWhere('drinks.name','LIKE','%'.$request->search.'%')

        //                                 ->orWhere('drink_card_stocks.remark','LIKE','%'.$request->search.'%');

        //                         });
        // }
        // if(@$request->brand_name){
        //     $results = $results->Where('brand_name','LIKE','%'.$request->brand_name.'%');
        // }

        if (request('start_date')) {
            $data['start_date'] = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();

            $data['end_date'] = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
        }

        $limit = 15;
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
            $startDate = date('Y-m-d', strtotime($start));
            $endDate = date('Y-m-d', strtotime($end));
        } elseif ($range === '1') {
            $yesterday = $today->copy()->subDay();
            $startDate = $yesterday->format('Y-m-d');
            $endDate = $yesterday->format('Y-m-d');
        } else {
            $days = in_array($range, ['7', '14', '30']) ? (int)$range : 1;
            $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
        }

        $orders = \App\Models\Order::with(['customer', 'branch'])
            ->where('ref_seller_id', $userId)
            ->whereDate('booking_date', '>=', $startDate)
            ->whereDate('booking_date', '<=', $endDate)
            ->orderBy('booking_date', 'desc')
            ->get();

        $user = \App\Models\User::find($userId);

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
            $startDate = date('Y-m-d', strtotime($start));
            $endDate = date('Y-m-d', strtotime($end));
        } elseif ($range === '1') {
            $yesterday = $today->copy()->subDay();
            $startDate = $yesterday->format('Y-m-d');
            $endDate = $yesterday->format('Y-m-d');
        } else {
            $days = in_array($range, ['7', '14', '30']) ? (int)$range : 1;
            $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
        }

        $orders = \App\Models\Order::with(['customer', 'branch'])
            ->where('ref_user_id', $userId)
            ->whereDate('booking_date', '>=', $startDate)
            ->whereDate('booking_date', '<=', $endDate)
            ->orderBy('booking_date', 'desc')
            ->get();

        $user = \App\Models\User::find($userId);

        return view('admin.commission.sales_orders', compact('orders', 'user', 'startDate', 'endDate'));
    }
}
