<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Subdistrict;
use App\Models\Renter;
use App\Models\Building;
use App\Models\Floor;
use App\Models\RentBill;
use App\Models\Room;
use App\Models\RoomForRents;
use App\Models\Contract;
use App\Models\StatusRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

DB::beginTransaction();

class RoomController extends Controller
{
    public function index(Request $request)
    {
        
        $data['page_url'] = 'room';
        $data['renter'] = Renter::get();
        $data['province'] = Province::get();
        $data['district'] = District::get();
        $data['subdistrict'] = Subdistrict::get();
        $data['buildings'] = Building::get();
        $data['floors'] = Floor::get();
        // $data['selected_buildings'] = [];

        $room_for_rents = RoomForRents::get('ref_room_id')->toArray();
        $room_check = [];
        foreach($room_for_rents as $r_f_r){
            $room_check[] = $r_f_r['ref_room_id'];
        }
        $data['room_check'] = $room_check;
        $data['summary'] = $this->summary(session("branch_id"));
        
        return view('room/index', $data);
    }
    public function show($id)
    {
       $data['page_url'] = 'room';
       $data['room'] = Room::find($id);
       $room_for_rent = RoomForRents::leftJoin('renters', 'room_for_rents.ref_renter_id', '=', 'renters.id')
                                                    ->where('room_for_rents.ref_room_id', $id)
                                                    ->select('room_for_rents.*', 'renters.*', DB::raw("CONCAT(renters.name, ' ', IFNULL(renters.surname, '')) as full_name"))
                                                    ->orderBy('room_for_rents.created_at', 'desc') // หรือใช้ 'id' ตามที่ต้องการ
                                                    ->first();
        $data['room_for_rent'] = $room_for_rent;
        $data['otherRooms'] = Room::whereNot('id', $id)->get();
        $province = Province::find($room_for_rent->ref_province_id)->name_in_thai;
        $district = District::find($room_for_rent->ref_district_id)->name_in_thai;
        $subdistrict = Subdistrict::find($room_for_rent->ref_subdistrict_id);
        $data['address'] = $room_for_rent->addess.' '.$subdistrict->name_in_thai.' '.$district.' '.$province.' '.$subdistrict->zip_code;
        return view('room/view', $data);
    }

    public function get_room_rental_contract($id)
    {
        $renter = Renter::find($id);
        $data['renter'] = $renter;
        $province = Province::find($renter->ref_province_id)->name_in_thai;
        $district = District::find($renter->ref_district_id)->name_in_thai;
        $subdistrict = Subdistrict::find($renter->ref_subdistrict_id);
        $data['address'] = $renter->addess.' '.$subdistrict->name_in_thai.' '.$district.' '.$province.' '.$subdistrict->zip_code;
        $data['room_for_rent'] = RoomForRents::where('ref_renter_id', $id)->get();

        return view('room/room-rental-contract', $data);
    }
    public function datatable(Request $request)
    {
        $results = Room::orderBy('id','DESC')
                                ->leftJoin('contracts', 'rooms.id', '=', 'contracts.ref_room_id')
                                ->leftJoin('room_for_rents', 'rooms.id', '=', 'room_for_rents.ref_room_id')
                                ->leftJoin('renters', 'room_for_rents.ref_renter_id', '=', 'renters.id')
                                ->leftJoin('rent_bills', function ($join) {
                                    $join->on('room_for_rents.id', '=', 'rent_bills.ref_room_for_rent_id')
                                         ->where('rent_bills.ref_status_id', '=', '3'); // เงื่อนไขเพิ่มเติมที่ต้องการ
                                })
                                ->select('room_for_rents.*', 'renters.prefix' , 
                                DB::raw('
                                    IFNULL(
                                        CONCAT(renters.name, " ", COALESCE(renters.surname, "")),
                                        " "
                                    ) as renter_name
                                '),
                                DB::raw('
                                    CASE 
                                        WHEN rent_bills.id IS NOT NULL THEN "ค้างชำระ"
                                        WHEN contracts.id IS NOT NULL THEN "มีผู้พักอาศัย"
                                        WHEN contracts.id IS NULL AND renters.id IS NOT NULL THEN "ห้องจอง"
                                        ELSE "ห้องว่าง"
                                    END as status_name
                                '),
                                'rooms.name as room_name');
                                        // WHEN contracts.id IS NULL = 0 THEN "no"
        
        if(@$request->search){
            $results = $results->orWhere(function ($query) use ($request) {
                                    $query->whereRaw("CONCAT(renters.prefix ,' ' , renters.name, ' ', COALESCE(renters.surname, '')) LIKE ?", ["%{$request->search}%"])
                                        ->orWhere('rooms.name','LIKE','%'.$request->search.'%');
                                });
        }
        if($request->building != "all"){
            $results = $results->Where('room_for_rents.ref_building_id', $request->building);
        }
        if($request->floor != "all"){
            $results = $results->Where('room_for_rents.ref_floor_id', $request->floor);
        }

        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }
        // $data['prefix'] = [ 1 => 'บริษัท', 2 => 'นาย', 3 => 'นางสาว', 4 => 'นาง'];
        $results = $results->paginate($limit);

        $status_room = StatusRoom::select('name', 'color')->get()->toArray();

        $status_room = array_column($status_room, 'color', 'name');

        // return $results->items();
        // dd($results);
        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['status_room'] = $status_room;

        $data['list_data'] = $results;

        return view('room/table', $data);
    }
    public function room_summary()
    {
        return $this->summary(session("branch_id"));

    }
    public function get_districts($id)
    {
        return $district = District::where('province_id', $id)->get();
    }
    public function get_subdistricts($id)
    {
        return Subdistrict::where('district_id', $id)->get();
    }
    public function get_floors($id)
    {
        if($id == "all"){
            return Floor::get();
        }
        return Floor::where('ref_building_id', $id)->get();
    }
    public function selected(Request $request)
    {
        $data = [];
        if($request->buildings){
            $buildings = Building::select('id','name')->get();
            $data['buildings'] = array_combine(array_column($buildings->toArray(), 'id'), array_column($buildings->toArray(), 'name'));

            $floors = Floor::get();
            $data['floors'] = array_combine(array_column($floors->toArray(), 'id'), array_column($floors->toArray(), 'name'));

            $rooms = Room::get();
            $data['rooms'] = array_combine(array_column($rooms->toArray(), 'id'), array_column($rooms->toArray(), 'name'));

            $data['selected_buildings'] = $request['buildings'];
        };
        
        return view('room/selected', $data);
    }
///////////
    public function insert_contract(Request $request)
    {
        try{
            $contract_date = Carbon::createFromFormat('d/m/Y', $request->contract_date)->format('Y-m-d');
            
            foreach($request->contract as $row){

                $deduction_booking_date = Carbon::createFromFormat('d/m/Y', $row['deduction_booking_date'])->format('Y-m-d');
                
                $insert = new Contract;

                $insert->ref_renter_id  =  $request->ref_renter_id;
                $insert->homeland  =  $request->homeland;
                $insert->phone  =  $request->phone;
                $insert->id_card_number  =  $request->id_card_number; 
                $insert->address  =  $request->address;
                $insert->contract_date  =  $contract_date;
                $insert->period  =  $request->period;
                $insert->remark  =  $request->remark;

                $insert->ref_room_id  =  $row['ref_room_id'];
                $insert->security_deposit  =  $row['security_deposit'];
                $insert->deduction_booking_amount  =  $row['deduction_booking_amount'];
                $insert->deduction_booking_date  =  $deduction_booking_date;
                $insert->receipt_no  =  $row['receipt_no'];
                $insert->water_meter_start_living  =  $row['water_meter_start_living'];
                $insert->electricity_meter_start_living  =  $row['electricity_meter_start_living'];
                $insert->save();
                
                $r_b = new RentBill;
                $r_b->ref_room_for_rent_id  =  $request->ref_room_for_rent_id;
                $r_b->month  =  date('m')-1;
                $r_b->year  =  date('Y');
                $r_b->electricity_unit  =  300;
                $r_b->electricity_amount  =  105;
                $r_b->water_unit  =  200;
                $r_b->water_amount  =  360;
                $r_b->invoice_number =  $this->generateInvoiceCode();
                $r_b->ref_status_id =  3;
                $r_b->save();
                
            }

            
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return false;
        }
        //
    }
    public function store(Request $request)
    {
        $birthdate = Carbon::createFromFormat('d/m/Y', $request->birthdate)->format('Y-m-d');
        $booking_date = Carbon::createFromFormat('d/m/Y', $request->booking_date)->format('Y-m-d');
        $date_stay = Carbon::createFromFormat('d/m/Y', $request->date_stay)->format('Y-m-d');
        $payment_received_date = Carbon::createFromFormat('d/m/Y', $request->date_stay)->format('Y-m-d');
        // return $request;
        try{
            
            // return $this->generateInvoiceCode();
            $insert = new Renter;
            $insert->prefix  =  $request->prefix;
            $insert->name  =  $request->name;
            $insert->surname  =  $request->surname;
            $insert->phone  =  $request->phone;
            $insert->id_card_number  =  $request->id_card_number;
            $insert->address  =  $request->address;
            $insert->ref_subdistrict_id  =  $request->ref_subdistrict_id;
            $insert->ref_district_id  =  $request->ref_district_id;
            $insert->ref_province_id  =  $request->ref_province_id;
            $insert->zipcode  =  $request->zipcode;
            $insert->birthdate  =  $birthdate;
            $insert->booking_date  =  $booking_date;
            $insert->booking_channel  =  $request->booking_channel;
            $insert->save();

            foreach($request->buildings as $key => $building){
                foreach($building as $key_2 => $floor){
                    foreach($floor as $room){

                        $r_t_r = new RoomForRents;
                        $r_t_r->date_stay  =  $date_stay;
                        $r_t_r->ref_room_id  =  $room;
                        $r_t_r->ref_floor_id  =  $key_2;
                        $r_t_r->ref_building_id  =  $key;
                        $r_t_r->ref_branch_id  =  session("branch_id");
                        $r_t_r->ref_renter_id  =  $insert->id;
                        $r_t_r->ref_user_id  =  1;
                        $r_t_r->deposit  =  $request->deposit;
                        $r_t_r->payment_method  =  $request->payment_method;
                        $r_t_r->payment_received_date  =  $payment_received_date;
                        $r_t_r->save();

                    }
                }
            }

            
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
            return false;
        }
        //
    }
    public function generateInvoiceCode()
    {
        // Get the current year and month
        $year = Carbon::now()->year; // 2024
        $month = Carbon::now()->month-1; // 10
        
        // Format year and month
        $yearMonth = $year . str_pad($month, 2, '0', STR_PAD_LEFT); // 202410
        
        // Find the latest invoice in the same year and month
        $latestInvoice = RentBill::where('year', $year)
                                ->where('month', $month)
                                ->latest('id')
                                ->first();

        // Calculate the new invoice number (sequence)
        $sequence = $latestInvoice ? substr($latestInvoice->invoice_number, -6) + 1 : 1;
        $sequenceCode = str_pad($sequence, 6, '0', STR_PAD_LEFT); // 000001

        // Generate the invoice code
        $invoiceCode = 'INV' . $yearMonth . $sequenceCode;

        return $invoiceCode;
    }
}
