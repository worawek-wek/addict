<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
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
        $data['page_url'] = 'product';
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

        $data['page_url'] = 'product';
        $data['list_data'] = $results;

        return view('admin/product/table', $data);
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
            $product->product_name = $request->product_name;
            $product->save();
            
            DB::commit();
            return redirect('product-page')->with('message', 'Insert Product "'.$request->product_name.'" success');
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
        $data['asset'] = asset('/');
        $data['title'] = 'Edit Product';
        $data['page_before'] = 'Product';
        $data['page'] = 'Edit Product';
        $data['product'] = Product::get();
        $data['action'] = url('product/'.$id);
        $data['product'] = Product::find($id);

        return view('admin/product/form', $data);
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
            $product->product_name = $request->product_name;
            $product->save();

            DB::commit();
            return redirect('product-page')->with('message', 'Update Product "'.$request->product_name.'" success');
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
