<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CardStocks;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/product';
        $data['page'] = 'สินค้า';
        
        return view('admin/product/index', $data);
    }

    public function datatable(Request $request)
    {
        $results = Product::orderBy('id','DESC');
        // if(@$request->brand_name){
        //     $results = $results->Where('brand_name','LIKE','%'.$request->brand_name.'%');
        // }
        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/product';
        $data['list_data'] = $results;

        return view('admin/product/table', $data);
    }
    public function card_stock_report()
    {
        $data['product'] = Product::get();
        $data['page_url'] = 'admin/card_stock_report';
        $data['page'] = 'สินค้า';
        
        return view('admin/product/card_stock_report', $data);
    }

    public function card_stock_report_datatable(Request $request)
    {
        $results = CardStocks::select('card_stocks.*', 'products.name as product_name', 'branchs.name as branch_name')
                                ->orderBy('id','DESC')
                                ->leftjoin('products', 'card_stocks.ref_product_id', '=', 'products.id')
                                ->leftjoin('branchs', 'products.ref_branch_id', '=', 'branchs.id');
        // if(@$request->brand_name){
        //     $results = $results->Where('brand_name','LIKE','%'.$request->brand_name.'%');
        // }
        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/card_stock_report';
        $data['list_data'] = $results;

        return view('admin/product/card_stock_report_table', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New Product';
        $data['product'] = Product::get();
        $data['action'] = route('product.store');
        return view('admin/product/form', $data);
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
            $product = new Product;
            $product->ref_branch_id = $request->ref_branch_id;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->cost = $request->cost;
            $product->remark = $request->remark;
            $product->save();
            
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function card_stock_report_store(Request $request)
    {
        $card_stocks = CardStocks::where('ref_product_id', $request->ref_product_id)->latest()->first();
        if(!$card_stocks){
            $remain = 0;
        }else{
            $remain = $card_stocks->remain;
        }
        try{
            $card_stocks = new CardStocks;
            $card_stocks->ref_product_id = $request->ref_product_id;
            $card_stocks->type = 1;
            $card_stocks->label = $request->label;
            $card_stocks->quantity = $request->quantity;
            $card_stocks->remain = $remain+$request->quantity;
            $card_stocks->remark = $request->remark;
            $card_stocks->save();
            
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
        
        $data['page_url'] = 'admin/product';
        $data['product'] = Product::find($id);
        return view('admin/product/view', $data);
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
            $product = Product::find($id);
            $product->ref_branch_id = $request->ref_branch_id;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->cost = $request->cost;
            $product->stock = $request->stock;
            $product->remark = $request->remark;
            $product->save();

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
            Product::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    
}
