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
        // DB::table('rooms')
        //     ->where('name', 'like', '1%')
        //     ->whereIn('ref_floor_id', [95,96,97,98,99,100,101,102,103])
        //     ->update([
        //         'name' => DB::raw('CONCAT("A", SUBSTRING(name, 2))')
        //     ]);
        // DB::table('rooms')
        //     ->where('name', 'like', '2%')
        //     ->whereIn('ref_floor_id', [95,96,97,98,99,100,101,102,103])
        //     ->update([
        //         'name' => DB::raw('CONCAT("B", SUBSTRING(name, 2))')
        //     ]);
        //     DB::commit();
            // return 1;
        // $response = Http::get('http://100.74.37.42:7953/getRealTimeData.aspx'); // ตำหรุ
        // $response = Http::get('http://100.111.169.37:7953/getRealTimeData.aspx'); // บางปลา
        // $response = Http::get('http://100.79.137.14:7953/getRealTimeData.aspx'); // แพรกษา

        // $response = Http::post('http://kittinakornplace.com/api/update_meter', [$electricity]);
        
        // // ตรวจสอบผลลัพธ์จาก API response
        // if ($response->successful()) {
        //     return $data = $response->json(); // ถ้าผลลัพธ์เป็น JSON
        //     // หรือ $response->body() ถ้าอยากได้เป็น text
        // } else {
        //     // ถ้าการส่ง request ล้มเหลว
        //     return $response->status(); // แสดง status code
        // }
        $branch = Branch::where('ip_meter','!=','')->get();

        // /////////////////////////////////
        foreach($branch as $bra){
            // return $bra['ip_meter'];

            if(session("branch_id") != $bra['id']){
                continue;
            }
            
            $connection = @fsockopen($bra['ip_meter'], 7953, $errno, $errstr, 10); // 10 คือ timeout
            if (!is_resource($connection)) {
                continue;
            }
            $response = Http::get('http://'.$bra['ip_meter'].':7953/getRealTimeData.aspx'); // เจริญใจ

            $xmlString = $response->body();
            
            // แปลง XML เป็น SimpleXMLElement
            $xmlObject = simplexml_load_string($xmlString);

            // แปลง SimpleXMLElement เป็น array ถ้าต้องการ
            $json = json_encode($xmlObject);
            $array = json_decode($json, true);

            $electricity = $array['Meters']['Meter']; // ส่งกลับเป็น array

            $room = Room::whereHas('floor.building', function ($query) use ($bra) {
                                    $query->where('ref_branch_id', $bra['id']);
                                })->get();
            foreach($room as $ro){

                // filter ดึง ข้อมูล meter ห้องนี้
                $filtered = array_filter($electricity, function($item) use ($ro) {
                        // if ($ro->name[0] == '1') {
                        //     return $item['@attributes']['GroupName'] = 'A' . substr($ro->name, 1);
                        // } elseif ($ro->name[0] == '2') {
                        //     return $item['@attributes']['GroupName'] = 'B' . substr($ro->name, 1);
                        // }
                    return $item['@attributes']['GroupName'] == $ro->name;
                });
                $electricity_unit = 0;
                if (!empty($filtered)) {
                    $electricity_unit = reset($filtered)['Value'][0];
                }
                // filter ดึง ข้อมูล meter ห้องนี้ จบ

                $check = Meter::where('ref_room_id', $ro->id)->where('month', date('m'))->where('year', date('Y'))->first();

                if($check){
                    $meter = Meter::find($check->id); 					
                }else{
                    $meter = new Meter; 					
                    $meter->water_unit  =  0;
                }
                $meter->ref_room_id  =  $ro->id;
                $meter->month  =  date('m');
                $meter->year  =  date('Y');
                $meter->electricity_unit  =  $electricity_unit;
                $meter->save();
            }
            break;
        }
        DB::commit();
        // // 100.74.37.42
        
        $data['page_url'] = 'meter';
        $data['buildings'] = Building::get();
        $data['floors'] = Floor::get();

        return view('meter/index', $data);
    }
    public function update_meter(Request $request)
    {
        $dataArray = $request->meter;

        $room = Room::whereHas('floor.building', function ($query) {
                                $query->where('ref_branch_id', 1);
                            })->get();
        foreach($room as $ro){

            // filter ดึง ข้อมูล meter ห้องนี้
            $filtered = array_filter($dataArray, function($item) use ($ro) {
                return $item['@attributes']['GroupName'] == $ro->name;
            });
            $electricity_unit = 0;
            if (!empty($filtered)) {
                $electricity_unit = reset($filtered)['Value'][0];
            }
            // filter ดึง ข้อมูล meter ห้องนี้ จบ

            $check = Meter::where('ref_room_id', $ro->id)->where('month', date('m'))->where('year', date('Y'))->first();

            if($check){
                $meter = Meter::find($check->id); 					
            }else{
                $meter = new Meter; 					
                $meter->water_unit  =  0;
            }
            $meter->ref_room_id  =  $ro->id;
            $meter->month  =  date('m');
            $meter->year  =  date('Y');
            $meter->electricity_unit  =  $electricity_unit;
            $meter->save();
        }
        DB::commit();
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
                        ->leftJoin('buildings', 'buildings.id', '=', 'floors.ref_building_id')
                        ->Where('meters.month', $month)->Where('meters.year', $year)
                        ->where('ref_branch_id',session("branch_id"))
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
