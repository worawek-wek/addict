<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    // แสดงรายการคอมมิชชั่นทั้งหมด
    public function index()
    {
        // $commissions = Commission::with(['user.branch', 'position'])->orderBy('id', 'desc')->get();
         $commissions =[];
        return view('admin.commission.index', compact('commissions'));
    }
    public function view(Request $request)
    {
        // ดึงพนักงานทุกคนที่มี branch และ position
        $usersQuery = \App\Models\User::with(['branch', 'position'])
            ->where('ref_position_id', '!=', 0);
        $branchId = $request->input('branch_id');
        if (!empty($branchId)) {
            $usersQuery->where('ref_branch_id', $branchId);
        }
        $users = $usersQuery->get();

        $staffData = [];
            $staffData = [];

            // Filter date range
            $range = $request->input('range', '1');
            $start = $request->input('start');
            $end = $request->input('end');
            $today = now();

            if ($range === 'custom' && $start && $end) {
                $startDate = date('Y-m-d', strtotime($start));
                $endDate = date('Y-m-d', strtotime($end));
            } else {
                // Default ranges: 1, 7, 14, 30 days
                $days = in_array($range, ['1','7','14','30']) ? (int)$range : 1;
                $startDate = $today->copy()->subDays($days-1)->format('Y-m-d');
                $endDate = $today->format('Y-m-d');
            }

            foreach ($users as $user) {
                $commission = 0;
                // เลือก user id ที่ใช้กรอง orders ตามตำแหน่ง
                if ($user->position && $user->position->id == 1) {
                    // ตำแหน่งขาย ใช้ ref_seller_id
                    $orders = \App\Models\Order::where('ref_seller_id', $user->id)
                        ->whereDate('booking_date', '>=', $startDate)
                        ->whereDate('booking_date', '<=', $endDate)
                        ->get();
                    foreach ($orders as $order) {
                        $commission += $order->sales_commission;
                    }
                } elseif ($user->position && $user->position->id == 2) {
                    // ตำแหน่งนวด ใช้ ref_user_id
                    $orders = \App\Models\Order::where('ref_user_id', $user->id)
                        ->whereDate('booking_date', '>=', $startDate)
                        ->whereDate('booking_date', '<=', $endDate)
                        ->get();
                    foreach ($orders as $order) {
                        $commission += $order->massage_commission;
                    }
                }
                $staffData[] = [
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
            return view('admin.commission.view', compact('staffData'));
    }
    // แสดงฟอร์มสร้างใหม่
    public function create()
    {
        $users = User::with('branch')->where('ref_position_id', '!=', 0)->orderBy('name')->get();
        if ($users->count() == 0) {
            $users = User::with('branch')
                ->leftJoin('branchs', 'users.ref_branch_id', '=', 'branchs.id')
                ->orderBy('branchs.name')
                ->orderBy('users.name')
                ->select('users.*')
                ->get();
        }
        $users = User::with('branch')
            ->where('ref_position_id', '!=', 0)
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

            // Custom validation: at least one of commission_amount or commission_percent must be present
            if (empty($validated['commission_amount']) && empty($validated['commission_percent'])) {
                return redirect()->route('commission.create')->withErrors(['commission_amount' => 'กรุณากรอกจำนวนเงินหรือเปอร์เซ็นต์คอมมิชชั่นอย่างน้อย 1 ช่อง'])->withInput();
            }

            // ตรวจสอบซ้ำ user ถ้ามี service_duration ให้เช็ค service_duration แทน service_name
            if (!empty($validated['service_duration'])) {
                $exists = \App\Models\Commission::where('ref_user_id', $validated['ref_user_id'])
                    ->where('service_duration', $validated['service_duration'])
                    ->first();
                if ($exists) {
                    $user = \App\Models\User::find($validated['ref_user_id']);
                    $msg = ($user ? $user->name : 'ผู้ใช้') . ' เคยมีค่าคอมมิชชั่นของบริการระยะเวลา ' . $validated['service_duration'] . ' อยู่แล้ว';
                    return redirect()->route('commission.create')->withErrors(['service_duration' => $msg])->withInput();
                }
            } else {
                $exists = \App\Models\Commission::where('ref_user_id', $validated['ref_user_id'])
                    ->where('service_name', $validated['service_name'])
                    ->first();
                if ($exists) {
                    $user = \App\Models\User::find($validated['ref_user_id']);
                    $msg = ($user ? $user->name : 'ผู้ใช้') . ' เคยมีค่าคอมมิชชั่นของบริการ ' . $validated['service_name'] . ' อยู่แล้ว';
                    return redirect()->route('commission.create')->withErrors(['service_name' => $msg])->withInput();
                }
            }

            $commission = Commission::create($validated);
            return redirect()->route('commission.index')->with('success', 'เพิ่มคอมมิชชั่นสำเร็จ');
        } catch (Exception $e) {
            return redirect()->route('commission.create')->withErrors(['db' => $e->getMessage()])->withInput();
        }
    }

    // แสดงฟอร์มแก้ไข
    public function edit($id)
    {
        $commission = Commission::findOrFail($id);
        $users = \App\Models\User::with('branch')
            ->where('ref_position_id', '!=', 0)
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
        $commission = Commission::findOrFail($id);
        $validated = $request->validate([
            'ref_addon_options_id' => 'nullable|integer',
            'service_name' => 'required|string|max:100',
            'service_duration' => 'nullable|string',
            'commission_amount' => 'nullable|numeric',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
        ]);
        // ถ้าเลือก addon option ให้บันทึกชื่อ addon จริง ๆ
        if ($request->filled('ref_addon_options_id')) {
            $addon = \App\Models\AddonOption::find($request->ref_addon_options_id);
            if ($addon) {
                $validated['service_name'] = $addon->name;
            }
        }
        // ตรวจสอบซ้ำ user ถ้ามี service_duration ให้เช็ค service_duration แทน service_name (ยกเว้นตัวที่กำลังแก้ไข)
        if (!empty($validated['service_duration'])) {
            $exists = \App\Models\Commission::where('ref_user_id', $commission->ref_user_id)
                ->where('service_duration', $validated['service_duration'])
                ->where('id', '!=', $commission->id)
                ->first();
            if ($exists) {
                $user = \App\Models\User::find($commission->ref_user_id);
                $msg = ($user ? $user->name : 'ผู้ใช้') . ' เคยมีค่าคอมมิชชั่นของบริการระยะเวลา ' . $validated['service_duration'] . ' อยู่แล้ว';
                return redirect()->route('commission.edit', $commission->id)->withErrors(['service_duration' => $msg])->withInput();
            }
        } else {
            $exists = \App\Models\Commission::where('ref_user_id', $commission->ref_user_id)
                ->where('service_name', $validated['service_name'])
                ->where('id', '!=', $commission->id)
                ->first();
            if ($exists) {
                $user = \App\Models\User::find($commission->ref_user_id);
                $msg = ($user ? $user->name : 'ผู้ใช้') . ' เคยมีค่าคอมมิชชั่นของบริการ ' . $validated['service_name'] . ' อยู่แล้ว';
                return redirect()->route('commission.edit', $commission->id)->withErrors(['service_name' => $msg])->withInput();
            }
        }
        $commission->update($validated);
        return redirect()->route('commission.index')->with('success', 'แก้ไขคอมมิชชั่นสำเร็จ');
    }

    // ลบข้อมูล
    public function destroy($id)
    {
        Commission::destroy($id);
        return redirect()->route('commission.index')->with('success', 'ลบคอมมิชชั่นสำเร็จ');
    }
}
