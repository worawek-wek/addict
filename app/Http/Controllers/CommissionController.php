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
        $commissions = Commission::with(['user.branch', 'position'])->orderBy('id', 'desc')->get();
        return view('admin.commission.index', compact('commissions'));
    }

    // แสดงฟอร์มสร้างใหม่
    public function create()
    {
        $users = User::with('branch')->where('ref_position_id', '!=', 0)->orderBy('name')->get();
        // ถ้าไม่มี user ที่ ref_position_id != 0 ให้ fallback เป็น user ทุกคน (หรือจะแสดง dropdown ว่างก็ได้)
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
        return view('admin.commission.create', compact('users', 'positions'));
    }

    // บันทึกข้อมูลใหม่
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ref_user_id' => 'required|integer',
                'ref_position_id' => 'required|integer',
                'service_name' => 'required|string|max:100',
                'service_duration' => 'nullable|string',
                'commission_amount' => 'required|numeric',
            ]);

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
        return view('admin.commission.edit', compact('commission', 'users', 'positions'));
    }

    // อัปเดตข้อมูล
    public function update(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'service_duration' => 'nullable|string',
            'commission_amount' => 'required|numeric',
        ]);
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
