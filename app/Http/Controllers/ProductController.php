<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\StockReadyForSale;
use App\Models\ExportStock;
use App\Models\CardStocks;
use App\Models\HistoryStock;
use App\Support\AdminBusinessDay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Exception;

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
        $user = Auth::user();
        $data['product'] = Product::with('producttype')->get();
        $data['producttype'] = ProductType::all();

        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }
        return view('admin/product/index', $data);
    }

    public function datatable(Request $request)
    {
        $results = Product::orderBy('name');
        if (!empty($request->search)) {
            $results->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('remark', 'LIKE', "%{$request->search}%")
                    ->orWhere('price', 'LIKE', "%{$request->search}%");
                    // ->orWhere('cost', 'LIKE', "%{$request->search}%");
            });
        }

        // 🔎 ถ้ามีเลือกสาขา และไม่ใช่ all → filter
        if ($request->filled('ref_branch_id') && $request->ref_branch_id !== 'all') {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }
        // ถ้าไม่ส่ง ref_branch_id หรือเป็น all → ข้าม ไม่ filter

        $limit = $request->limit ?? AdminBusinessDay::DEFAULT_PER_PAGE;

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends($request->query());
        $data['query'] = $request->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/product';

        return view('admin/product/table', $data);
    }

    public function change_status(Request $request, $id)
    {
        try {

            $user = Product::find($id);
            $user->ref_status_id = $request->ref_status_id;
            $user->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }

    public function change_sell_with_course(Request $request, $id)
    {
        try {
            $product = Product::find($id);
            $product->sell_with_course = $request->sell_with_course ? 1 : 0;
            $product->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }

    public function update_sort(Request $request, $id)
    {
        try {
            // return $request;
            $old_sort = $request->old_sort;
            $new_sort = $request->new_sort;

            if($old_sort < $new_sort){
                Product::where('ref_branch_id', Auth::user()->ref_branch_id)->where('sort', '>', $old_sort)->where('sort', '<=', $new_sort)->decrement('sort'); // ลดลง -1
            }else{
                Product::where('ref_branch_id', Auth::user()->ref_branch_id)->where('sort', '<', $old_sort)->where('sort', '>=', $new_sort)->increment('sort'); // เพิ่มขึ้น +1
            }
            Product::where('id', $id)->update(['sort' => $new_sort]); // ลดลง
            // return 123;
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }

    public function card_stock_report()
    {
        $user = Auth::user();
        $data['branch'] = Branch::orderBy('name')->get();
        // if ($user->ref_position_id == 0) {
            // super admin เห็นทุก branch
            $data['product'] = Product::get();
        // } else {
            // เห็นเฉพาะสาขาของตัวเอง
            // $data['product'] = Product::where('ref_branch_id', $user->ref_branch_id)->get();
        // }
        $data['page_url'] = 'admin/card_stock_report';
        $data['page'] = 'สินค้า';

        return view('admin/product/card_stock_report', $data);
    }

    public function card_stock_report_datatable(Request $request)
    {
        $user = Auth::user();
        $results = CardStocks::select('card_stocks.*', 'products.name as product_name', 'branchs.name as branch_name', 'card_stocks.cost_price')
                                ->orderBy('card_stocks.id', 'DESC')
                                ->leftjoin('products', 'card_stocks.ref_product_id', '=', 'products.id')
                                ->leftjoin('branchs', 'products.ref_branch_id', '=', 'branchs.id')
                                ->where('card_stocks.remain', '>', 0);

        // if ($user->ref_position_id != 0) {
        //     // filter เฉพาะสาขาของตัวเอง
        //     $results = $results->where('products.ref_branch_id', $user->ref_branch_id);
        // }
        
        if (@$request->ref_product_id) {
            // filter เฉพาะสาขาของตัวเอง
            $results = $results->where('products.id', $request->ref_product_id);
        }

        if (@$request->ref_branch_id) {
            // filter เฉพาะสาขาของตัวเอง
            $results = $results->where('branchs.id', $request->ref_branch_id);
        }

        if (@$request->created_at) {
            [$startDate, $endDate] = AdminBusinessDay::singleDateRange($request->created_at);
            $results = $results->whereBetween('card_stocks.created_at', [$startDate, $endDate]);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $results->Where(function ($query) use ($request) {

                                    $query->where('card_stocks.label','LIKE','%'.$request->search.'%')

                                        ->orWhere('products.name','LIKE','%'.$request->search.'%')

                                        ->orWhere('card_stocks.remark','LIKE','%'.$request->search.'%');

                                });
        }
        // if(@$request->brand_name){
        //     $results = $results->Where('brand_name','LIKE','%'.$request->brand_name.'%');
        // }
        $limit = 100;
        if (@$request['limit']) {
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
    public function get_stock(Request $request, $product_id)
    {
        $stock = CardStocks::where('ref_product_id', $product_id)
            ->where('remain', '>', 0)
            ->get();
        return $stock;
    }
    public function get_stock_by_id(Request $request, $stock_id)
    {
        $stock = CardStocks::find($stock_id);
        return $stock;
    }

    public function card_stock_report_pdf(Request $request)
    {
        $user = Auth::user();
        $results = CardStocks::select('card_stocks.*', 'products.name as product_name', 'branchs.name as branch_name', 'card_stocks.cost_price')
                                ->orderBy('card_stocks.id', 'DESC')
                                ->leftjoin('products', 'card_stocks.ref_product_id', '=', 'products.id')
                                ->leftjoin('branchs', 'products.ref_branch_id', '=', 'branchs.id')
                                ->where('card_stocks.remain', '>', 0);

        if ($user->ref_position_id != 0) {
            // filter เฉพาะสาขาของตัวเอง
            $results = $results->where('products.ref_branch_id', $user->ref_branch_id);
        }
        
        if (@$request->ref_product_id) {
            // filter เฉพาะสาขาของตัวเอง
            $results = $results->where('products.id', $request->ref_product_id);
        }

        if (@$request->created_at) {
            [$startDate, $endDate] = AdminBusinessDay::singleDateRange($request->created_at);
            $results = $results->whereBetween('card_stocks.created_at', [$startDate, $endDate]);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $results->Where(function ($query) use ($request) {

                                    $query->where('card_stocks.label','LIKE','%'.$request->search.'%')

                                        ->orWhere('products.name','LIKE','%'.$request->search.'%')

                                        ->orWhere('card_stocks.remark','LIKE','%'.$request->search.'%');

                                });
        }

        $data['list_data'] = $results->get();

        $html = view('admin/product/card_stock_report_pdf', $data)->render();

        $pdf = new \Mpdf\Mpdf([
            'default_font_size' => 10,
            'default_font' => 'sarabun'
        ]);
        $pdf->autoScriptToLang = true;
        $pdf->autoLangToFont = true;
        $pdf->WriteHTML($html);
        $pdf->Output();
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
        try {
            $lastSort = Product::lockForUpdate()->max('sort') ?? 0;

            $product = new Product;
            $product->ref_branch_id = $request->ref_branch_id ?: Auth::user()->ref_branch_id;
            $product->type_id = $request->producttype;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->price_staff = $request->price_staff;
            $product->sell_with_course = $request->boolean('sell_with_course');
            $product->cost = @$request->cost ?? 0.00;
            $product->remark = $request->remark;
            $product->minimum = $request->minimum;
            $product->sort  =  $lastSort + 1;
            $product->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'บันทึกไม่สำเร็จ',
                'error'   => $err->getMessage()
            ], 500);
        }
        //
    }
    public function withdraw(Request $request)
    {
        try {
            $product = Product::find($request->ref_product_id);
            $main_stock_remain = $product->total_remain ?? 0;
            $ready_for_sale_remain = $product->ready_for_sale_total_remain ?? 0;
            $withdrawQty = (int) $request->qty;

//////////////////////////////////////////////////

            $card_stocks = CardStocks::lockForUpdate()->find($request->ref_lot_id);
            if (!$card_stocks || $card_stocks->remain < $withdrawQty) {
                throw new Exception('สต็อกหลักไม่พอสำหรับเบิกสินค้า');
            }

            $card_stocks->remain = $card_stocks->remain - $withdrawQty;
            $card_stocks->save();

            $readyStock = new StockReadyForSale;
            $readyStock->ref_product_id = $request->ref_product_id;
            $readyStock->ref_lot_id = $request->ref_lot_id;
            $readyStock->qty = $withdrawQty;
            $readyStock->remain = $withdrawQty;
            $readyStock->save();
            
//////////////////////////////////////////////////

                $new_product = Product::find($request->ref_product_id);
                $new_main_stock_remain = $new_product->total_remain ?? 0;
                $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
                
// เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> ตัดสต็อก {
                $history_stock = new HistoryStock;
                $history_stock->ref_product_id = $request->ref_product_id; // id สินค้า
                $history_stock->quantity = 0; // จำนวนที่เคลื่อนไหว
                $history_stock->stock_before_quantity = $main_stock_remain; // จำนวน ก่อน ตัดสต็อก
                $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->withdraw_quantity = $withdrawQty; // จำนวนที่เบิกเข้าพร้อมขาย
                $history_stock->stock_ready_for_sale_before_quantity = $ready_for_sale_remain; // จำนวนพร้อมขายก่อนเบิก
                $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวนพร้อมขายหลังเบิก
                $history_stock->quantity_type = 0; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
                $history_stock->save();


            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'บันทึกไม่สำเร็จ',
                'error'   => $err->getMessage()
            ], 500);
        } catch (Exception $err) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $err->getMessage(),
            ], 422);
        }
        //
    }
    public function card_stock_report_store(Request $request)
    {
        // $card_stocks = CardStocks::where('ref_product_id', $request->ref_product_id)->latest()->first();
        // if (!$card_stocks) {
        //     $remain = 0;
        // } else {
        //     $remain = $card_stocks->remain;
        // }
        try {
        // ดึง สินค้า ก่อน เพิ่มสต็อก {
            $product = Product::find($request->ref_product_id); // ดึง สินค้า ก่อน เพิ่มสต็อก
            $main_stock_remain = $product->total_remain ?? 0;
            $ready_for_sale_remain = $product->ready_for_sale_total_remain ?? 0;
        // ดึง สินค้า ก่อน เพิ่มสต็อก }

        // เพิ่มสต็อก {
            $card_stocks = new CardStocks;
            $card_stocks->ref_product_id = $request->ref_product_id;
            $card_stocks->type = 1;
            $card_stocks->label = $request->label;
            $card_stocks->stock_before_quantity = $main_stock_remain + $ready_for_sale_remain;
            $card_stocks->quantity = $request->quantity;
            $card_stocks->stock_after_quantity = $main_stock_remain + $ready_for_sale_remain + $request->quantity;
            $card_stocks->remain = $request->quantity;
            $card_stocks->remark = $request->remark;
            $card_stocks->cost_price = $request->cost_price;
            $card_stocks->save();
        // เพิ่มสต็อก }
            // return 1234;

        // ดึง สินค้า หลัง เพิ่มสต็อก {
            $new_product = Product::find($request->ref_product_id);
            $new_main_stock_remain = $new_product->total_remain ?? 0;
            $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
        // ดึง สินค้า หลัง เพิ่มสต็อก }
        // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> เบิกไปสต็อกขาย {
            $history_stock = new HistoryStock;
            $history_stock->ref_product_id = $request->ref_product_id; // id สินค้า
            $history_stock->quantity = $request->quantity; // จำนวนที่เคลื่อนไหว
            $history_stock->stock_before_quantity = $main_stock_remain; // จำนวน ก่อน ตัดสต็อก
            $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
            $history_stock->stock_ready_for_sale_before_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
            $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
            $history_stock->quantity_type = 1; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
            $history_stock->withdraw_quantity = 0;
            $history_stock->save();
        // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> เบิกไปสต็อกขาย }

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function export_stock_store(Request $request)
    {
        try {
        // ดึง สินค้า ก่อน ลดสต็อก {
            $product = Product::find($request->ref_product_id); // ดึง สินค้า ก่อน ลดสต็อก
            $main_stock_remain = $product->total_remain ?? 0;
            $ready_for_sale_remain = $product->ready_for_sale_total_remain ?? 0;
        // ดึง สินค้า ก่อน ลดสต็อก }

        // เพิ่มนำออกสินค้า {
            $export_stocks = new ExportStock;
            $export_stocks->ref_product_id = $request->ref_product_id;
            $export_stocks->ref_lot_id = $request->ref_lot_id;
            $export_stocks->quantity = $request->qty;
            $export_stocks->remark = $request->remark;
            $export_stocks->save();
        // เพิ่มนำออกสินค้า }
// return 123;

        // เพิ่มสต็อก {
            $card_stocks = CardStocks::find($request->ref_lot_id);
            $card_stocks->remain = $card_stocks->remain - $request->qty;
            $card_stocks->save();
        // เพิ่มสต็อก }
        // ดึง สินค้า หลัง ลดสต็อก {
            $new_product = Product::find($request->ref_product_id);
            $new_main_stock_remain = $new_product->total_remain ?? 0;
            $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
        // ดึง สินค้า หลัง ลดสต็อก }
            
        // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> ตัดสต็อก {
            $history_stock = new HistoryStock;
            $history_stock->ref_product_id = $request->ref_product_id; // id สินค้า
            $history_stock->quantity = $request->qty; // จำนวนที่เคลื่อนไหว
            $history_stock->stock_before_quantity = $main_stock_remain; // จำนวน ก่อน ตัดสต็อก
            $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
            $history_stock->stock_ready_for_sale_before_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
            $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
            $history_stock->quantity_type = 2; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
            $history_stock->withdraw_quantity = 0;
            $history_stock->save();
        // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> ตัดสต็อก }

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function card_stock_report_update(Request $request, $id)
    {
        try {
            
            $card_stocks = CardStocks::find($id);
            // if ($card_stocks->quantity > $request->quantity) {
            //     $card_stocks->remain = $card_stocks->remain - abs($card_stocks->quantity - $request->quantity);
            // } else {
            //     $card_stocks->remain = $card_stocks->remain + abs($card_stocks->quantity - $request->quantity);
            // }

            // $card_stocks->ref_product_id = $request->ref_product_id;
            $card_stocks->type = 1;
            $card_stocks->label = $request->label;
            // $card_stocks->quantity = $request->quantity;
            $card_stocks->remark = $request->remark;
            $card_stocks->cost_price = $request->cost_price;
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
        $user = Auth::user();

        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }        // $data['title'] = 'Profile';
        return view('admin/product/view', $data);
    }

    public function card_stock_report_edit($id)
    {

        $data['page_url'] = 'admin/card_stock_report';
        $data['stock'] = CardStocks::find($id);
        $user = Auth::user();
        if ($user->ref_position_id == 0) {
            // super admin เห็นทุก branch
            $data['product'] = Product::get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['product'] = Product::where('ref_branch_id', $user->ref_branch_id)->get();
        }
        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }        // $data['title'] = 'Profile';
        return view('admin/product/card_stock_report_view', $data);
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
            $product = Product::find($id);
            $product->ref_branch_id = $request->ref_branch_id;
            $product->name = $request->name;
            // $product->type_id = $request->producttype;
            $product->price = $request->price;
            $product->price_staff = $request->price_staff;
            $product->sell_with_course = $request->boolean('sell_with_course');
            $product->cost = @$request->cost ?? 0.00;
            // $product->stock = $request->stock;
            $product->remark = $request->remark;
            $product->minimum = $request->minimum;
            $product->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return false;
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
            Product::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }

    // ==========================================
    // ระบบจัดการประเภทสินค้า (Product Type CRUD)
    // ==========================================
    public function getAllProductTypes()
    {
        $productTypes = ProductType::orderBy('id', 'desc')->with('branch')->get();
        return response()->json($productTypes);
    }

    public function storeProductType(Request $request)
    {
        // 1. เช็คก่อนว่ามีค่า name ส่งมาหรือไม่
        if (empty($request->name)) {
            return response()->json([
                'status' => false,
                'message' => 'ไม่มีชื่อประเภทสินค้าส่งมา'
            ], 400);
        }

        try {
            $save = [
                'name'       => $request->name,
                'ref_branch_id'       => $request->ref_branch_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $inserted = DB::table('product_type')->insert($save);
            DB::commit();

            if (!$inserted) {
                return response()->json([
                    'status' => false,
                    'message' => 'คำสั่ง Insert คืนค่า False (บันทึกไม่ลงโดยไม่ทราบสาเหตุ)'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'เพิ่มประเภทสินค้าสำเร็จ'
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'System Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateProductType(Request $request, $id)
    {
        try {
            $productType = ProductType::findOrFail($id);
            $productType->update([
                'name' => $request->name
            ]);

            return response()->json(['status' => true, 'message' => 'แก้ไขประเภทสินค้าสำเร็จ']);
        } catch (Exception $e) {
            Log::error('Update Product Type Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProductType($id)
    {
        try {
            $productType = ProductType::findOrFail($id);
            $productCount = Product::where('type_id', $id)->count();

            if ($productCount > 0) {
                return response()->json([
                    'status' => false,
                    'message' => "ไม่สามารถลบได้ เนื่องจากมีสินค้าใช้หมวดหมู่นี้อยู่จำนวน {$productCount} รายการ"
                ], 400);
            }

            $productType->delete();
            DB::commit();
            return response()->json(['status' => true, 'message' => 'ลบหมวดหมู่สำเร็จ']);
        } catch (Exception $e) {
            Log::error('Delete Product Type Error: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

}
