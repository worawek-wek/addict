<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Drink;
use App\Models\DrinkStockReadyForSale;
use App\Models\DrinkCardStocks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

DB::beginTransaction();

class DrinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/drink';
        $data['page'] = 'สินค้า';
        $user = Auth::user();
        $data['drink'] = Drink::orderBy('name')->get();

        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }
        return view('admin/drink/index', $data);
    }

    public function datatable(Request $request)
    {
        $results = Drink::orderBy('name');
        if (!empty($request->search)) {
            $results->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('remark', 'LIKE', "%{$request->search}%")
                    ->orWhere('price', 'LIKE', "%{$request->search}%")
                    ->orWhere('cost', 'LIKE', "%{$request->search}%");
            });
        }

        // 🔎 ถ้ามีเลือกสาขา และไม่ใช่ all → filter
        if ($request->filled('ref_branch_id') && $request->ref_branch_id !== 'all') {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }
        // ถ้าไม่ส่ง ref_branch_id หรือเป็น all → ข้าม ไม่ filter

        $limit = $request->limit ?? 15;

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends($request->query());
        $data['query'] = $request->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/drink';

        return view('admin/drink/table', $data);
    }

    public function change_status(Request $request, $id)
    {
        try {

            $user = Drink::find($id);
            $user->ref_status_id = $request->ref_status_id;
            $user->save();

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
                Drink::where('ref_branch_id', Auth::user()->ref_branch_id)->where('sort', '>', $old_sort)->where('sort', '<=', $new_sort)->decrement('sort'); // ลดลง -1
            }else{
                Drink::where('ref_branch_id', Auth::user()->ref_branch_id)->where('sort', '<', $old_sort)->where('sort', '>=', $new_sort)->increment('sort'); // เพิ่มขึ้น +1
            }
            Drink::where('id', $id)->update(['sort' => $new_sort]); // ลดลง
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
        if ($user->ref_position_id == 0) {
            // super admin เห็นทุก branch
            $data['drink'] = Drink::get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['drink'] = Drink::where('ref_branch_id', $user->ref_branch_id)->get();
        }
        $data['page_url'] = 'admin/drink_card_stock_report';
        $data['page'] = 'สินค้า';

        return view('admin/drink/card_stock_report', $data);
    }

    public function card_stock_report_datatable(Request $request)
    {
        $user = Auth::user();
        $results = DrinkCardStocks::select('drink_card_stocks.*', 'drinks.name as drink_name', 'branchs.name as branch_name', 'drink_card_stocks.cost_price')
                                ->orderBy('drink_card_stocks.id', 'DESC')
                                ->leftjoin('drinks', 'drink_card_stocks.ref_drink_id', '=', 'drinks.id')
                                ->leftjoin('branchs', 'drinks.ref_branch_id', '=', 'branchs.id');

        if ($user->ref_position_id != 0) {
            // filter เฉพาะสาขาของตัวเอง
            $results = $results->where('drinks.ref_branch_id', $user->ref_branch_id);
        }
        
        if (@$request->created_at) {
            $created_at = Carbon::createFromFormat('d/m/Y', $request->created_at)->format('Y-m-d');
            $results = $results->WhereDate('drink_card_stocks.created_at', $created_at);
        }

        if (request()->filled('search')) {
            $search = request()->search;
            $results->Where(function ($query) use ($request) {

                                    $query->where('drink_card_stocks.label','LIKE','%'.$request->search.'%')

                                        ->orWhere('drinks.name','LIKE','%'.$request->search.'%')

                                        ->orWhere('drink_card_stocks.remark','LIKE','%'.$request->search.'%');

                                });
        }
        // if(@$request->brand_name){
        //     $results = $results->Where('brand_name','LIKE','%'.$request->brand_name.'%');
        // }
        $limit = 15;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['page_url'] = 'admin/card_stock_report';
        $data['list_data'] = $results;

        return view('admin/drink/card_stock_report_table', $data);
    }

    public function get_stock(Request $request, $drink_id)
    {
        $stock = DrinkCardStocks::where('ref_drink_id', $drink_id)->get();
        return $stock;
    }
    public function get_stock_by_id(Request $request, $stock_id)
    {
        $stock = DrinkCardStocks::find($stock_id);
        return $stock;
    }
    public function card_stock_report_pdf(Request $request)
    {
        $user = Auth::user();
        $results = DrinkCardStocks::select('card_stocks.*', 'drinks.name as drink_name', 'branchs.name as branch_name', 'card_stocks.cost_price')
                                ->orderBy('card_stocks.id', 'DESC')
                                ->leftjoin('drinks', 'card_stocks.ref_drink_id', '=', 'drinks.id')
                                ->leftjoin('branchs', 'drinks.ref_branch_id', '=', 'branchs.id');

        if ($user->ref_position_id != 0) {
            // filter เฉพาะสาขาของตัวเอง
            $results = $results->where('drinks.ref_branch_id', $user->ref_branch_id);
        }
        if (request()->filled('search')) {
            $search = request()->search;
            $results->Where(function ($query) use ($request) {

                                    $query->where('card_stocks.label','LIKE','%'.$request->search.'%')

                                        ->orWhere('drinks.name','LIKE','%'.$request->search.'%')

                                        ->orWhere('card_stocks.remark','LIKE','%'.$request->search.'%');

                                });
        }
        
        $data['list_data'] = $results->get();

        $html = view('admin/drink/card_stock_report_pdf', $data)->render();

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
        $data['title'] = 'Add New Drink';
        $data['drink'] = Drink::get();
        $data['action'] = route('drink.store');
        return view('admin/drink/form', $data);
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
            $lastSort = Drink::lockForUpdate()->max('sort') ?? 0;

            $drink = new Drink;
            $drink->ref_branch_id = $request->ref_branch_id;
            $drink->name = $request->name;
            $drink->price = $request->price;
            $drink->commission = $request->commission;
            $drink->cost = @$request->cost ?? 0.00;
            $drink->remark = $request->remark;
            $drink->sort  =  $lastSort + 1;
            $drink->save();

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

            $card_stocks = DrinkCardStocks::find($request->ref_lot_id);
            $card_stocks->remain = $card_stocks->remain-$request->qty;
            $card_stocks->save();

            $drink = new DrinkStockReadyForSale;
            $drink->ref_drink_id = $request->ref_drink_id;
            $drink->ref_lot_id = $request->ref_lot_id;
            $drink->qty = $request->qty;
            $drink->remain = $request->qty;
            $drink->save();

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
    public function card_stock_report_store(Request $request)
    {
        $card_stocks = DrinkCardStocks::where('ref_drink_id', $request->ref_drink_id)->latest()->first();
        if (!$card_stocks) {
            $remain = 0;
        } else {
            $remain = $card_stocks->remain;
        }
        try {
            $card_stocks = new DrinkCardStocks;
            $card_stocks->ref_drink_id = $request->ref_drink_id;
            $card_stocks->type = 1;
            $card_stocks->label = $request->label;
            $card_stocks->quantity = $request->quantity;
            $card_stocks->remain = $remain + $request->quantity;
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
    public function card_stock_report_update(Request $request, $id)
    {
        try {
            
            $card_stocks = DrinkCardStocks::find($id);
            // if ($card_stocks->quantity > $request->quantity) {
            //     $card_stocks->remain = $card_stocks->remain - abs($card_stocks->quantity - $request->quantity);
            // } else {
            //     $card_stocks->remain = $card_stocks->remain + abs($card_stocks->quantity - $request->quantity);
            // }
            
            $card_stocks->ref_drink_id = $request->ref_drink_id;
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

        $data['page_url'] = 'admin/drink';
        $data['drink'] = Drink::find($id);
        $user = Auth::user();

        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }        // $data['title'] = 'Profile';
        return view('admin/drink/view', $data);
    }
    
    public function card_stock_report_edit($id)
    {

        $data['page_url'] = 'admin/card_stock_report';
        $data['stock'] = DrinkCardStocks::find($id);
        $user = Auth::user();
        if ($user->ref_position_id == 0) {
            // super admin เห็นทุก branch
            $data['drink'] = Drink::get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['drink'] = Drink::where('ref_branch_id', $user->ref_branch_id)->get();
        }
        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }        // $data['title'] = 'Profile';
        return view('admin/drink/card_stock_report_view', $data);
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
            $drink = Drink::find($id);
            $drink->ref_branch_id = $request->ref_branch_id;
            $drink->name = $request->name;
            $drink->price = $request->price;
            $drink->commission = $request->commission;
            $drink->cost = @$request->cost ?? 0.00;
            // $drink->stock = $request->stock;
            $drink->remark = $request->remark;
            $drink->save();

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
            Drink::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}
