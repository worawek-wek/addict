<?php

namespace App\Http\Controllers;

use App\Models\AddonOption;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddonOptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->ref_position_id == 0) {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        if ($user->ref_position_id == 0) {
            $options = AddonOption::all();
        } else {
            $options = AddonOption::where('branch', $user->ref_branch_id)->get();
        }
        return view('admin.addon_option.index', compact('options', 'branches'));
    }

    public function create()
    {
        return view('admin.addon_option.create');
    }

    public function store(Request $request)
    {
        $request->commission = $request->commission ?? 0;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'branch' => 'nullable|integer',
            'commission' => 'nullable|numeric|min:0',
            'coupon'     => 'nullable|numeric|min:0',
        ]);
        $validated['commission'] = $validated['commission'] ?? 0;
        $validated['coupon'] = $validated['coupon'] ?? 0;
        AddonOption::create($validated);
    return redirect()->route('addon_options.index')->with('success', 'Option created successfully.');
    }

    public function edit($id)
    {
        $option = AddonOption::findOrFail($id);
        $user = Auth::user();
        if ($user->ref_position_id == 0) {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.addon_option.edit', compact('option', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'branch' => 'nullable|integer',
            'commission' => 'nullable|numeric|min:0',
            'coupon'     => 'nullable|numeric|min:0',
        ]);

        $option = AddonOption::findOrFail($id);
        $option->update($validated);
    return redirect()->route('addon_options.index')->with('success', 'Option updated successfully.');
    }

    public function destroy($id)
    {
        $option = AddonOption::findOrFail($id);
        $option->delete();
    return redirect()->route('addon_options.index')->with('success', 'Option deleted successfully.');
    }
}
