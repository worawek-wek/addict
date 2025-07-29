<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Bank;
use App\Models\AdditionalCosts;
use App\Models\RentBill;
use App\Models\Branch;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Leave;
use App\Models\RoomForRents;
use App\Models\StatusRentBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

DB::beginTransaction();

class BillController extends Controller
{
    public function index(Request $request)
    {
        $data['page_url'] = 'admin/bill';
        $data['status_rent_bill'] = StatusRentBill::get();
        $data['buildings'] = Building::get();
        $data['floors'] = Floor::get();

        return view('admin/bill/index', $data);
    }
    public function waiting_for_confirmation(Request $request)
    {
        $request['limit'] = 9999999;
        $request['re'] = 1;
        $request['ref_status_id'] = 2;
        $data['list_data'] = $this->datatable($request);
        return view('admin/bill/waiting-for-confirmation', $data);
    }
    
    public function datatable(Request $request)
    {
        $results = RentBill::orderBy('rent_bills.id','DESC')
                                ->join('room_for_rents', 'rent_bills.ref_room_for_rent_id', '=', 'room_for_rents.id')
                                ->join('renters', 'room_for_rents.ref_renter_id', '=', 'renters.id')
                                ->join('rooms', 'room_for_rents.ref_room_id', '=', 'rooms.id')
                                ->join('floors', 'rooms.ref_floor_id', '=', 'floors.id')
                                ->join('buildings', 'floors.ref_building_id', '=', 'buildings.id')
                                ->where('buildings.ref_branch_id', session("branch_id"))
                                ->distinct('rent_bills.id')
                                ->select('rent_bills.*', 'renters.prefix' , DB::raw('CONCAT(renters.name, " ", COALESCE(renters.surname, "")) as renter_name'), 'rooms.name as room_name', 'rooms.rent');
        
        if(@$request->search){
            $results = $results->orWhere(function ($query) use ($request) {
                                    $query->whereRaw("CONCAT(renters.prefix ,' ' , renters.name, ' ', renters.surname) LIKE ?", ["%{$request->search}%"])
                                        ->orWhere('rooms.name','LIKE','%'.$request->search.'%');
                                });
        }
        if(@$request->ref_status_id != "all"){
            $results = $results->Where('rent_bills.ref_status_id','LIKE','%'.$request->ref_status_id.'%');
        }
        if(@$request->room_name){
            $results = $results->Where('rooms.name','LIKE','%'.$request->room_name.'%');
        }
        if(@$request->invoice_number){
            $results = $results->Where('rent_bills.invoice_number','LIKE','%'.$request->invoice_number.'%');
        }
        if(@$request->room_rent != "all"){
            $results = $results->Where('rooms.rent','LIKE','%'.$request->room_rent.'%');
        }
        if(@$request->building != "all"){
            $results = $results->Where('room_for_rents.ref_building_id', $request->building);
        }
        if(@$request->floor != "all"){
            $results = $results->Where('room_for_rents.ref_floor_id', $request->floor);
        }

        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }
        // $data['prefix'] = [ 1 => 'บริษัท', 2 => 'นาย', 3 => 'นางสาว', 4 => 'นาง'];

        $results = $results->paginate($limit);
        // return $results->items();
        // dd($results);
        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['list_data'] = $results;
        
        if(@$request->re){
            return $data['list_data'];
        }

        return view('admin/bill/table', $data);
    }
    public function incomplete_update(Request $request)
    {
        try{
            // return true;
            // return $this->generateInvoiceCode();
            $rent_bill = RentBill::find($request->id);
            $rent_bill->payment_channel = $request->payment_channel;
            $rent_bill->water_amount = $request->water_amount;
            $rent_bill->water_unit = $request->water_unit;
            $rent_bill->ref_status_id = 2;
            $rent_bill->save();
            
            if(@$request->add_expenses_title){
                $expenses = new AdditionalCosts();
                $expenses->title = $request->add_expenses_title;
                $expenses->amount = $request->add_expenses_price;
                $expenses->ref_rent_bill_id = $rent_bill->id;
                $expenses->status = 1;
                $expenses->save();
            }
            if(@$request->discount_title){
                $update = new AdditionalCosts();
                $update->title = $request->discount_title;
                $update->amount = $request->discount_price;
                $update->ref_rent_bill_id = $rent_bill->id;
                $update->status = 2;
                $update->save();
            }

            // foreach($request->buildings as $key => $building){
            //     $r_t_r = new RoomForRents;
            //     $r_t_r->date_stay  =  $date_stay;
            //     $r_t_r->ref_room_id  =  $room;
            //     $r_t_r->ref_floor_id  =  $key_2;
            //     $r_t_r->ref_building_id  =  $key;
            //     $r_t_r->ref_branch_id  =  $key;
            //     $r_t_r->ref_renter_id  =  $insert->id;
            //     $r_t_r->ref_user_id  =  1;
            //     $r_t_r->deposit  =  $request->deposit;
            //     $r_t_r->payment_method  =  $request->payment_method;
            //     $r_t_r->payment_received_date  =  $payment_received_date;
            //     $r_t_r->save();
                
            //     $r_b = new RentBill;
            //     $r_b->ref_room_for_rent_id  =  $r_t_r->id;
            //     $r_b->month  =  date('m')-1;
            //     $r_b->year  =  date('Y');
            //     $r_b->electricity_unit  =  300;
            //     $r_b->electricity_amount  =  105;
            //     $r_b->water_unit  =  200;
            //     $r_b->water_amount  =  106;
            //     $r_b->invoice_number =  $this->generateInvoiceCode();
            //     $r_b->ref_status_id =  1;
            //     $r_b->save();
            // }

            
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return false;
        }
        //
    }
    public function invoice($id)
    {
        $data['page_url'] = 'admin/bill';
        $invoice = RentBill::find($id);
        $data['expenses'] = AdditionalCosts::where('ref_rent_bill_id', $id)->get();
        $data['invoice'] = $invoice;
        $data['bank'] = Bank::get();

        if($invoice->ref_status_id == 3){
            return view('admin/bill/incomplete', $data);
        }
        return view('admin/bill/invoice', $data);
    }
    public function bill_summary()
    {
        return $this->summary(session("branch_id"));
    }
    public function change_status_bill(Request $request, $id)
    {
        // return $request;
        try{
            if($id == 'all'){
                $insert = RentBill::where('ref_status_id', 2);
                $insert->update(['ref_status_id' => $request->status]);
                DB::commit();
                return true;
            }
            $insert = RentBill::whereIn('id', explode(',', $id));
            $insert->update(['ref_status_id' => $request->status]);

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return false;
        }
        //
    }
}