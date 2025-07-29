<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\IncomeExpenses;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Work_shift;
use App\Models\Category;
use App\Models\Leave;
use App\Models\UserLeave;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

DB::beginTransaction();

class IncomeExpensesController extends Controller
{
    public function index(Request $request)
    {
        $data['page_url'] = 'income-expenses';
        $data['position'] = Position::get();
        $data['category'] = Category::get();
        $income = IncomeExpenses::where('type', 1)->sum('amount');
        $expenses = IncomeExpenses::where('type', 2)->sum('amount');
        $data['income'] = $income;
        $data['expenses'] = $expenses;
        $data['total'] = $income-$expenses;
        // $data['title'] = 'Profile';
        
        return view('income-expenses/index', $data);
    }
    public function datatable(Request $request)
    {
        $results = IncomeExpenses::orderBy('id','DESC');
        
        if(@$request->search){
            $results = $results->Where(function ($query) use ($request) {
                                    $query->where('label','LIKE','%'.$request->search.'%');
                                        // ->orWhere('email','LIKE','%'.$request->search.'%');
                                });
        }

        if(@$request->ref_category_id != "all"){
            $results = $results->Where('ref_category_id', $request->ref_category_id);
        }

        if(@$request->type != "all"){
            $results = $results->Where('type', $request->type);
        }

        if(@$request->from_month){
            $to_month = 2000-01;
            if(@$request->to_month){
                $to_month = $request->to_month;
            }
            $results = $results->whereRaw("DATE_FORMAT(date, '%Y-%m') BETWEEN ? AND ?", [$request->from_month, $to_month]);
        }
        // if(@$request->to_month){
        //     $results = $results->WhereDate('date', '<=', $request->to_month);
        // }

        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }

        $results = $results->paginate($limit);
        // return $results->items();
        // dd($results);
        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['list_data'] = $results;

        return view('income-expenses/table', $data);
    }
    public function store(Request $request)
    {
        $date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
        try{
            $expenses = new IncomeExpenses;
            $expenses->type  =  $request->type;
            $expenses->label  =  $request->label;
            $expenses->amount  =  $request->amount;
            $expenses->date  =  $date;
            $expenses->ref_category_id  =  $request->ref_category_id;
            $expenses->room_name  =  $request->room_name;
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // return $request->file('proof_of_payment');
            if($request->file('proof_of_payment')){
                // return 123;
                $file = $request->file('proof_of_payment');
                $nameExtension = $file->getClientOriginalName();
                $extension = pathinfo($nameExtension, PATHINFO_EXTENSION);
                $img_name = pathinfo($nameExtension, PATHINFO_FILENAME);
                $path = "upload/expenses/";
                $proof_of_payment = $img_name.rand().'.'.$extension;
                $expenses->proof_of_payment = $proof_of_payment;
            }
            // return 999;
            if($request->file('payment_voucher')){
                // return 123;
                $file = $request->file('payment_voucher');
                $nameExtension = $file->getClientOriginalName();
                $extension = pathinfo($nameExtension, PATHINFO_EXTENSION);
                $img_name = pathinfo($nameExtension, PATHINFO_FILENAME);
                $path = "upload/expenses/";
                $payment_voucher = $img_name.rand().'.'.$extension;
                $expenses->payment_voucher = $payment_voucher;
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $expenses->ref_user_id  =  1;
            $expenses->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}
