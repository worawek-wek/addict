<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesCommissionTier;
use App\Models\Branch;

class SalesCommissionTierController extends Controller
{
    public function index()
    {
        $tiers = SalesCommissionTier::orderBy('min_sales_amount')->get();
        $branches = Branch::orderBy('name')->get();
        $branchMap = $branches->keyBy('id');
        $addonOptions = \App\Models\AddonOption::orderBy('name')->get();
        $cheerCharges = \App\Models\CheerCharge::where('ref_branch_id', auth()->user()->ref_branch_id)->get();
        return view('admin.commission.sales_tier', compact('tiers', 'branches', 'branchMap', 'addonOptions', 'cheerCharges'));
    }
    public function storeCheer(Request $request)
    {
        $request->validate([
            'addon_options_id' => 'required|exists:addon_options,id',
            'type' => 'required|in:percent,baht',
            'amount' => 'required|numeric',
        ]);
        \App\Models\CheerCharge::create([
            'ref_branch_id' => auth()->user()->ref_branch_id,
            'addon_options_id' => $request->addon_options_id,
            'type' => $request->type,
            'amount' => $request->amount,
        ]);
        return redirect()->route('sales_commission_tier.index')->with('success', 'บันทึก Cheer สำเร็จ');
    }

    public function destroyCheer($id)
    {
        \App\Models\CheerCharge::destroy($id);
        return redirect()->route('sales_commission_tier.index')->with('success', 'ลบ Cheer สำเร็จ');
    }

    public function store(Request $request)
    {
        $request->validate([
            'min_sales_amount' => 'required|numeric',
            'max_sales_amount' => 'required|numeric',
            'commission_rate' => 'required|numeric',
        ]);
        SalesCommissionTier::create([
            'ref_branch_id' => $request->ref_branch_id,
            'min_sales_amount' => $request->min_sales_amount,
            'max_sales_amount' => $request->max_sales_amount,
            'commission_rate' => $request->commission_rate,
            'created_at' => now(),
        ]);
        return redirect()->route('sales_commission_tier.index')->with('success', 'บันทึกข้อมูลสำเร็จ');
    }

    public function destroy($id)
    {
        SalesCommissionTier::destroy($id);
        return redirect()->route('sales_commission_tier.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
