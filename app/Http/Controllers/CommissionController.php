<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\MassageCommission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    // แสดงรายการคอมมิชชั่นทั้งหมด
    public function index()
    {
        $userBranchId = auth()->user()->ref_branch_id ?? null;
        $commissions = MassageCommission::with(['user.branch'])
            ->whereHas('user', function ($q) use ($userBranchId) {
                $q->where('ref_branch_id', $userBranchId);
            })
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.commission.index', compact('commissions'));
    }
    // แสดงค่าคอมมิชชั่นพนักงานนวด
    public function view_massage(Request $request)
    {
        $userBranchId = auth()->user()->ref_branch_id ?? null;
        $usersQuery = \App\Models\User::with(['branch', 'position'])
            ->where('ref_position_id', 2)
            ->where('ref_branch_id', $userBranchId); // เฉพาะพนักงานนวดในสาขาที่ login
        $users = $usersQuery->get();

        $staffData = [];
        $range = $request->input('range', '1');
        $start = $request->input('start');
        $end = $request->input('end');
        $today = now();

        if ($range === 'custom' && $start && $end) {
            $startDate = date('Y-m-d', strtotime($start));
            $endDate = date('Y-m-d', strtotime($end));
        } else {
            $days = in_array($range, ['1', '7', '14', '30']) ? (int)$range : 1;
            $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
        }

        foreach ($users as $user) {
            $commission = \App\Models\CommissionsHistory::where('user_message_id', $user->id)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->sum('commission_massage_amount');
            $staffData[] = [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'branch' => $user->branch ? $user->branch->name : null,
                'position' => $user->position ? $user->position->position_name : null,
                'commission' => $commission,
            ];
        }
        if ($request->ajax() || $request->input('ajax') == '1') {
            return view('admin.commission._table_body', compact('staffData'));
        }
        return view('admin.commission.view_massage', compact('staffData'));
    }

    // แสดงค่าคอมมิชชั่นพนักงานขาย
    public function view_sales(Request $request)
    {
        $usersQuery = \App\Models\User::with(['branch', 'position'])
            ->where('ref_position_id', 1); // เฉพาะพนักงานขาย
        $users = $usersQuery->get();

        $staffData = [];
        $range = $request->input('range', '1');
        $start = $request->input('start');
        $end = $request->input('end');
        $today = now();

        if ($range === 'custom' && $start && $end) {
            $startDate = date('Y-m-d', strtotime($start));
            $endDate = date('Y-m-d', strtotime($end));
        } else {
            $days = in_array($range, ['1', '7', '14', '30']) ? (int)$range : 1;
            $startDate = $today->copy()->subDays($days - 1)->format('Y-m-d');
            $endDate = $today->format('Y-m-d');
        }

        foreach ($users as $user) {
            // Get all orders for this seller in the date range
            $orders = \App\Models\Order::where('ref_seller_id', $user->id)
                ->whereDate('booking_date', '>=', $startDate)
                ->whereDate('booking_date', '<=', $endDate)
                ->get();

            $totalSales = $orders->sum('total_price');
            $commission = 0;

            // Find tier for this branch and sales amount
            $tier = \App\Models\SalesCommissionTier::where('ref_branch_id', $user->ref_branch_id)
                ->where('min_sales_amount', '<=', $totalSales)
                ->where('max_sales_amount', '>=', $totalSales)
                ->first();

            if ($tier) {
                $commission = $totalSales * ($tier->commission_rate / 100);
            }

            $staffData[] = [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'branch' => $user->branch ? $user->branch->name : null,
                'position' => $user->position ? $user->position->position_name : null,
                'commission' => $commission,
            ];
        }
        if ($request->ajax() || $request->input('ajax') == '1') {
            return view('admin.commission._table_body', compact('staffData'));
        }
        return view('admin.commission.view_sales', compact('staffData'));
    }
    // แสดงฟอร์มสร้างใหม่
    public function create()
    {
        // เฉพาะพนักงานนวด (ref_position_id = 2)
        $userBranchId = auth()->user()->ref_branch_id ?? null;
        $users = User::with('branch')
            ->where('ref_position_id', 2)
            ->where('ref_branch_id', $userBranchId)
            ->leftJoin('branchs', 'users.ref_branch_id', '=', 'branchs.id')
            ->orderBy('branchs.name')
            ->orderBy('users.name')
            ->select('users.*')
            ->get();
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

            // เพิ่ม ref_branch_id จากผู้ใช้ที่ login
            $validated['ref_branch_id'] = auth()->user()->ref_branch_id ?? null;

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
        } else {
            $days = in_array($range, ['1', '7', '14', '30']) ? (int)$range : 1;
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
        } else {
            $days = in_array($range, ['1', '7', '14', '30']) ? (int)$range : 1;
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
