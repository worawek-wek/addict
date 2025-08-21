<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/customer';
        $data['page'] = 'ลูกค้า';
        $user = Auth::user();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branches'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branches'] = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin/customer/index', $data);
    }

  public function datatable(Request $request)
{
    $results = Customer::withCount('orders')->orderBy('id', 'DESC');

    // ✅ filter ด้วยสาขา
    if ($request->filled('ref_branch_id') && $request->ref_branch_id !== '') {
        $results->where('ref_branch_id', $request->ref_branch_id);
    }

    // ✅ filter ด้วย search
    if ($request->filled('search')) {
        $search = $request->search;
        $results->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    // ✅ Limit
    $limit = $request->limit ?? 15;

    $results = $results->paginate($limit);

    $data['list_data'] = $results->appends($request->query());
    $data['query'] = $request->query();
    $data['query']['limit'] = $limit;
    $data['page_url'] = 'admin/customer';

    return view('admin/customer/table', $data);
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New Customer';
        $data['customer'] = Customer::get();
        $data['action'] = route('customer.store');
        return view('admin/customer/form', $data);
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $customer = new Customer;
    //         $customer->name = $request->name;
    //         $customer->price = $request->price;
    //         $customer->cost = $request->cost;
    //         $customer->remark = $request->remark;
    //         $customer->save();

    //         DB::commit();
    //         return true;
    //     } catch (QueryException $err) {
    //         DB::rollBack();
    //     }
    //     //
    // }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ref_branch_id' => 'required',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'remark' => 'nullable|string'
        ]);

        Customer::create($validated);

        return response()->json(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data['page_url'] = 'admin/customer';
        $data['customer'] = Customer::find($id);
        return view('admin/customer/view', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        try {
            $customer = Customer::find($id);
            $customer->name = $request->name;
            $customer->price = $request->price;
            $customer->cost = $request->cost;
            $customer->stock = $request->stock;
            $customer->remark = $request->remark;
            $customer->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Customer::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function lock(Customer $customer)
    {
        try {
            $customer->status = 0; // 0 = locked
            $customer->save();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function unlock(Customer $customer)
    {
        try {
            $customer->status = 1; // 1 = unlocked
            $customer->save();
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateCus(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $result = $customer->update($request->only([
            'name',
            'first_name',
            'last_name',
            'phone',
            'id_card',
            'ref_branch_id'
        ]));

        DB::commit();

        return response()->json(true);
    }
}
