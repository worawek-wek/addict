<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Room;
use App\Models\Branch;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Leave;
use App\Models\Meter;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

DB::beginTransaction();

class MeterController extends Controller
{
    public function index(Request $request)
    {
        
        // $response = Http::get('http://100.64.10.101:7953/getRealTimeData.aspx');
        // $xmlString = $response->body();
        
        // // แปลง XML เป็น SimpleXMLElement
        // $xmlObject = simplexml_load_string($xmlString);

        // // แปลง SimpleXMLElement เป็น array ถ้าต้องการ
        // $json = json_encode($xmlObject);
        // $array = json_decode($json, true);

        // $electricity = $array['Meters']['Meter']; // ส่งกลับเป็น array
        // /////////////////////////////////

        // $room = room::get();
        // foreach($room as $ro){

        //     // filter ดึง ข้อมูล meter ห้องนี้
        //     $filtered = array_filter($electricity, function($item) use ($ro) {
        //         return $item['@attributes']['GroupName'] == $ro->name;
        //     });
        //     $electricity_unit = 0;
        //     if (!empty($filtered)) {
        //         $electricity_unit = reset($filtered)['Value'][0];
        //     }
        //     // filter ดึง ข้อมูล meter ห้องนี้ จบ

        //     $check = Meter::where('ref_room_id', $ro->id)->where('month', 11)->where('year', 2024)->first();

        //     if($check){
        //         $meter = Meter::find($check->id); 					
        //     }else{
        //         $meter = new Meter; 					
        //         $meter->water_unit  =  0;
        //     }
        //     $meter->ref_room_id  =  $ro->id;
        //     $meter->month  =  date('m');
        //     $meter->year  =  date('Y');
        //     $meter->electricity_unit  =  $electricity_unit;
        //     $meter->save();
        // }
        // DB::commit();

        
        $data['page_url'] = 'meter';
        $data['buildings'] = Building::get();
        $data['floors'] = Floor::get();

        return view('meter/index', $data);
    }
    public function datatable($request)
    {
        $month = date('m');
        $year = date('Y');

        if($request->month){
            $month = explode('-', $request->month)[1];
            $year = explode('-', $request->month)[0];
        }

        //////// หา เดือนก่อนหน้า
        $year_month_previous = date('Y-m', strtotime($year.'-'.$month . ' -1 month'));
        // dd($year_month_previous);
        $month_previous = explode('-', $year_month_previous)[1];
        $year_previous = explode('-', $year_month_previous)[0];
        //////// หา เดือนก่อนหน้า จบ

        $results = Room::orderBy('rooms.name')
                        ->leftJoin('meters', 'meters.ref_room_id', '=', 'rooms.id')
                        ->leftJoin('floors', 'floors.id', '=', 'rooms.ref_floor_id')
                        ->Where('meters.month', $month)->Where('meters.year', $year)
                        ->with('room_for_rent')
                        // ->WhereHas('room_for_rent', function ($query) {
                        //     $query->where('status', 0); // กรอง User ที่มี Position status = 'active'
                        // })
                        ->select('rooms.*', 'meters.water_unit', 'meters.electricity_unit', 'meters.id as meters_id');

        // if(@$request->search){
        // $results = $results->orWhere(function ($query) use ($request) {
        //             $query->whereRaw("CONCAT(renters.prefix ,' ' , renters.name, ' ', COALESCE(renters.surname, '')) LIKE ?", ["%{$request->search}%"])
        //                 ->orWhere('rooms.name','LIKE','%'.$request->search.'%');
        //         });
        // }
        // if($request->building != "all"){
        // $results = $results->Where('room_for_rents.ref_building_id', $request->building);
        // }
        if($request->floor != "all"){
            $results = $results->Where('floors.id', $request->floor);
        }

        $limit = 15;
        if(@$request['limit']){
        $limit = $request['limit'];
        }
        $results = $results->paginate($limit);


        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['search_month'] = $month;
        $data['search_year'] = $year;
        $data['month_previous'] = $month_previous;
        $data['year_previous'] = $year_previous;

        return $data;

    }
    public function electricity_datatable(Request $request)
    {
        $data = $this->datatable($request);
        
        return view('meter/electricity-table', $data);
    }
    public function water_datatable(Request $request)
    {
        $data = $this->datatable($request);

        return view('meter/water-table', $data);
    }
    public function water_unit_update(Request $request, $id)
    {
        try{
            $meter = Meter::find($id);
            $meter->water_unit  =  $request->water_unit;
            $meter->save();
            
            DB::commit();
            return $meter->water_unit;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
}
