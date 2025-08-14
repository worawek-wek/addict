<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
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

        return view('admin/customer/index', $data);
    }

    public function datatable(Request $request)
    {
        $results = Customer::withCount('orders')->orderBy('id', 'DESC');
        // if(@$request->brand_name){
        //     $results = $results->Where('brand_name','LIKE','%'.$request->brand_name.'%');
        // }

        if (@$request->search) {
            $results = $results->orWhere(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        $limit = 15;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/customer';
        $data['list_data'] = $results;

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
    public function store(Request $request)
    {
        try {
            $customer = new Customer;
            $customer->name = $request->name;
            $customer->price = $request->price;
            $customer->cost = $request->cost;
            $customer->remark = $request->remark;
            $customer->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
