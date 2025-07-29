<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderHasProduct;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/order';
        $data['page'] = 'รายงานการขายสินค้า';
        
        return view('admin/order/index', $data);
    }

    public function sales_report()
    {
        $data['page_url'] = 'admin/sales_report';
        $data['page'] = 'รายงานยอดขาย';
        
        return view('admin/order/sales_report', $data);
    }

    public function datatable(Request $request)
    {
        $results = OrderHasProduct::select('order_has_products.*', 'products.name as product_name', 'customers.name as customer_name', 'orders.order_number', 'branchs.name as branch_name')
                                    ->join('orders', 'order_has_products.ref_order_id', '=', 'orders.id')
                                    ->join('branchs', 'orders.ref_branch_id', '=', 'branchs.id')
                                    ->join('customers', 'orders.ref_customer_id', '=', 'customers.id')
                                    ->join('products', 'order_has_products.ref_product_id', '=', 'products.id')
                                    ->orderBy('order_has_products.id', 'DESC');

        // ถ้ามี search
        if (@$request->search) {
            $results = $results->where(function ($query) use ($request) {
                $query->where('orders.order_number', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('branchs.name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('customers.name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('order_has_products.quantity', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('order_has_products.price', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('products.name', 'LIKE', '%' . $request->search . '%');
            });
        }

        // limit
        $limit = $request->limit ?? 15;

        $results = $results->paginate($limit);


        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/order';
        $data['list_data'] = $results;

        return view('admin/order/table', $data);
    }
    public function sales_report_datatable(Request $request)
    {
        $results = Branch::select('branchs.id', 'branchs.name', 
                            DB::raw('SUM(order_has_products.price * order_has_products.quantity) as total_sales'),
                            DB::raw('SUM(order_has_products.quantity) as total_quantity')) // ใช้ SUM() เพื่อคำนวณยอดขายรวม
                        ->leftjoin('orders', 'branchs.id', '=', 'orders.ref_branch_id')
                        ->leftjoin('order_has_products', 'orders.id', '=', 'order_has_products.ref_order_id')
                        ->groupBy('branchs.id', 'branchs.name') // GROUP BY สาขา
                        ->orderBy('branchs.id', 'DESC');

        // ถ้ามี search
        if (@$request->search) {
            $results = $results->where(function ($query) use ($request) {
                $query->where('branchs.name', 'LIKE', '%' . $request->search . '%');
            });
        }

        // limit
        $limit = $request->limit ?? 15;

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/order';
        $data['list_data'] = $results;

        return view('admin/order/sales_report_table', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New Order';
        $data['order'] = Order::get();
        $data['action'] = route('order.store');
        return view('admin/order/form', $data);
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
        try{
            $order = new Order;
            $order->name = $request->name;
            $order->price = $request->price;
            $order->cost = $request->cost;
            $order->remark = $request->remark;
            $order->save();
            
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
        
        $data['page_url'] = 'admin/order';
        $data['order'] = Order::find($id);
        return view('admin/order/view', $data);
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
        try{
            $order = Order::find($id);
            $order->name = $request->name;
            $order->price = $request->price;
            $order->cost = $request->cost;
            $order->stock = $request->stock;
            $order->remark = $request->remark;
            $order->save();

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
        try{
            Order::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    
}
