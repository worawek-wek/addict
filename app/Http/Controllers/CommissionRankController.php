<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CommissionRank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CommissionRankController extends Controller
{
    /** โหมดบันได Rank */
    private array $modes = ['sales', 'rounds'];

    /** หมวด: service = นวด+สินค้า, drink = ดื่ม */
    private array $categories = ['service', 'drink'];

    private function normalizeCategory($value): string
    {
        return in_array($value, $this->categories, true) ? $value : 'service';
    }

    /** เฉพาะ user id = 1 (เจ้าของร้าน) เท่านั้นที่จัดการบันได Rank ได้ทุกสาขา */
    private function isSuper(): bool
    {
        return (int) auth()->id() === 1;
    }

    private function authorizeBoss(): void
    {
        abort_unless((int) auth()->id() === 1, 403, 'เฉพาะเจ้าของร้านเท่านั้น');
    }

    public function index(Request $request)
    {
        $this->authorizeBoss();
        $page_url = 'admin/commission-ranks';
        $branches = Branch::orderBy('name')->get();
        $tableReady = Schema::hasTable('commission_ranks');

        // '' = ค่ากลาง (ทุกสาขา, ref_branch_id null)
        if ($this->isSuper()) {
            $branchParam = (string) $request->input('branch', '');
        } else {
            $branchParam = (string) auth()->user()->ref_branch_id;
        }
        $branchId = $branchParam === '' ? null : (int) $branchParam;

        $category = $this->normalizeCategory($request->input('category'));

        $ladders = [];
        foreach ($this->modes as $mode) {
            $ladders[$mode] = $tableReady
                ? CommissionRank::where('category', $category)->where('mode', $mode)
                    ->when($branchId === null,
                        fn ($q) => $q->whereNull('ref_branch_id'),
                        fn ($q) => $q->where('ref_branch_id', $branchId))
                    ->get()->keyBy('rank_no')
                : collect();
        }

        return view('admin.commission.commission_ranks',
            compact('page_url', 'branches', 'ladders', 'branchParam', 'tableReady', 'category'));
    }

    public function save(Request $request)
    {
        $this->authorizeBoss();
        $branchParam = $this->isSuper()
            ? (string) $request->input('ref_branch_id', '')
            : (string) auth()->user()->ref_branch_id;
        $branchId = $branchParam === '' ? null : (int) $branchParam;
        $category = $this->normalizeCategory($request->input('category'));

        foreach ($this->modes as $mode) {
            $rows = $request->input($mode, []);

            for ($rank = 1; $rank <= 5; $rank++) {
                $r = $rows[$rank] ?? null;
                $min = $r['min_threshold'] ?? null;

                $match = CommissionRank::where('category', $category)->where('mode', $mode)->where('rank_no', $rank)
                    ->when($branchId === null,
                        fn ($q) => $q->whereNull('ref_branch_id'),
                        fn ($q) => $q->where('ref_branch_id', $branchId));

                // เว้นว่าง = ลบ rank ขั้นนั้น
                if ($r === null || $min === null || $min === '') {
                    (clone $match)->delete();
                    continue;
                }

                $payout = in_array($r['payout_type'] ?? 'percent', ['percent', 'fixed_per_round', 'fixed'], true)
                    ? $r['payout_type'] : 'percent';

                CommissionRank::updateOrCreate(
                    ['ref_branch_id' => $branchId, 'category' => $category, 'mode' => $mode, 'rank_no' => $rank],
                    [
                        'min_threshold' => (float) $min,
                        'rate' => (float) ($r['rate'] ?? 0),
                        'fixed_amount' => ($r['fixed_amount'] ?? '') === '' ? null : (float) $r['fixed_amount'],
                        'payout_type' => $payout,
                    ]
                );
            }
        }

        $redirectParams = array_filter(['branch' => $branchParam, 'category' => $category], fn ($v) => $v !== '');

        return redirect()
            ->route('commission_ranks.index', $redirectParams)
            ->with('success', 'บันทึกบันได Rank เรียบร้อย');
    }
}
