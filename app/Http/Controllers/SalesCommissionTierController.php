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
        return view('admin.commission.sales_tier', compact('tiers', 'branches', 'branchMap'));
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
