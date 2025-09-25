<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheerCharge;
use App\Models\Branch;
use App\Models\AddonOption;

class CheerChargeController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $addonOptions = AddonOption::all();
        $cheerCharges = CheerCharge::with(['branch', 'addonOption'])->get();
        return view('admin.commission.cheer_charge', compact('branches', 'addonOptions', 'cheerCharges'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ref_branch_id' => 'required|integer',
            'addon_options_id' => 'nullable|integer',
            'type' => 'required|in:percent,baht',
            'amount' => 'required|numeric|min:0',
        ]);
        $cheerCharge = CheerCharge::create($validated);
        return redirect()->route('cheer_charge.index')->with('success', 'บันทึกค่าเชียร์สำเร็จ');
    }

    public function destroy($id)
    {
        CheerCharge::destroy($id);
        return redirect()->route('cheer_charge.index')->with('success', 'ลบค่าเชียร์สำเร็จ');
    }
}
