<?php

namespace App\Http\Controllers;

use App\Models\AddonOption;
use Illuminate\Http\Request;
use App\Models\User;

class MassageDefaultSettingController extends Controller
{
    // แสดงหน้าตั้งค่าเริ่มต้นของพนักงานนวด
    public function index()
    {
        // ดึงรายชื่อพนักงานนวดทั้งหมด
        $users = User::where('ref_position_id', 2)->get();
        $isSuperAdmin = auth()->user()->ref_position_id == 0;
        if ($isSuperAdmin) {
            $branches = \App\Models\Branch::all();
            $selectedBranchId = request('branch_id');
            if ($selectedBranchId) {
                $addonOptions = AddonOption::where('branch', $selectedBranchId)->get();
                $defaultSettings = \App\Models\MassageCommission::whereNull('ref_user_id')->where('ref_branch_id', $selectedBranchId)->get();
            } else {
                $addonOptions = collect();
                $defaultSettings = collect();
            }
        } else {
            $branchId = auth()->user()->ref_branch_id ?? null;
            $branches = null;
            $addonOptions = AddonOption::where('branch', $branchId)->get();
            $defaultSettings = \App\Models\MassageCommission::whereNull('ref_user_id')
                ->where('ref_branch_id', $branchId)
                ->get();
        }
        return view('admin.commission.massage_default_setting', compact('users', 'addonOptions', 'defaultSettings', 'branches'));
    }

    // บันทึกค่าตั้งค่าเริ่มต้น
    public function store(Request $request)
    {
        if (auth()->user()->ref_position_id == 0) {
            $branchId = $request->input('branch_id');
        } else {
            $branchId = auth()->user()->ref_branch_id ?? null;
        }
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'service_duration' => 'nullable|string',
            'commission_amount' => 'nullable|numeric',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        // ต้องกรอกอย่างน้อย 1 ช่อง
        if (empty($validated['commission_amount']) && empty($validated['commission_percent'])) {
            return redirect()->back()->withErrors(['commission_amount' => 'กรุณากรอกจำนวนเงินหรือเปอร์เซ็นต์อย่างน้อย 1 ช่อง'])->withInput();
        }
        // ถ้ามีทั้งสองช่องให้แจ้งเตือน
        if (!empty($validated['commission_amount']) && !empty($validated['commission_percent'])) {
            return redirect()->back()->withErrors(['commission_amount' => 'กรุณากรอกจำนวนเงินหรือเปอร์เซ็นต์อย่างใดอย่างหนึ่งเท่านั้น'])->withInput();
        }

        // ถ้าเลือก addon option
        $addon_options_id = null;
        if (strpos($validated['service_name'], 'addon_') === 0) {
            $addon_options_id = intval(str_replace('addon_', '', $validated['service_name']));
            // ดึงชื่อ addon จริงมาเก็บใน service_name
            $addon = AddonOption::find($addon_options_id);
            $validated['service_name'] = $addon ? $addon->name : '';
        }

        \App\Models\MassageCommission::create([
            'ref_user_id' => null,
            'ref_branch_id' => $branchId,
            'addon_options_id' => $addon_options_id,
            'service_name' => $validated['service_name'],
            'service_duration' => $validated['service_duration'] ?? null,
            'commission_amount' => $validated['commission_amount'] ?? null,
            'commission_percent' => $validated['commission_percent'] ?? null,
        ]);
        return redirect()->route('massage_default_setting.index')->with('success', 'บันทึกค่าตั้งค่าเริ่มต้นสำเร็จ');
    }

    // อัปเดตค่าตั้งค่าเริ่มต้น
    public function update(Request $request, $id)
    {
        // รับข้อมูลและอัปเดตค่าตั้งค่าเริ่มต้น
        // ...
        return redirect()->route('massage_default_setting.index')->with('success', 'อัปเดตค่าตั้งค่าเริ่มต้นสำเร็จ');
    }
    public function destroy($id)
    {
        \App\Models\MassageCommission::where('id', $id)->whereNull('ref_user_id')->delete();
        return redirect()->route('massage_default_setting.index')->with('success', 'ลบค่าตั้งค่าเริ่มต้นสำเร็จ');
    }
}
