<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Bank;
use App\Models\Building;
use App\Models\Service;
use App\Models\RoomHasService;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Branch;
use App\Models\Apartment;
use App\Models\Province;
use App\Models\District;
use App\Models\Subdistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

DB::beginTransaction();

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fine(Request $request)
    {
        $data['buildings'] = Building::where('ref_branch_id', session("branch_id"))->get();
        $data['floors'] = Floor::whereHas('building', function ($query) {
                                            $query->where('ref_branch_id', session("branch_id"));
                                        })->get();
        $data['page_url'] = 'setting/fine';

        return view('setting/setting-fine', $data);
    }
    public function fine_datatable(Request $request)
    {
        $results = Room::leftJoin('floors', 'rooms.ref_floor_id', '=', 'floors.id')
                        ->leftJoin('buildings', 'floors.ref_building_id', '=', 'buildings.id')
                        ->whereHas('floor.building', function ($query) {
                            $query->where('ref_branch_id', session("branch_id"));
                        })
                        ->select('rooms.*');
        
        if($request->building != "all"){
            $results = $results->Where('buildings.id', $request->building);
        }
        if($request->floor != "all"){
            $results = $results->Where('floors.id', $request->floor);
        }

        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }
        // $data['prefix'] = [ 1 => 'บริษัท', 2 => 'นาย', 3 => 'นางสาว', 4 => 'นาง'];
        $results = $results->paginate($limit);

        $data['list_data'] = $results;

        return view('setting/setting-fine-table', $data);
    }

    public function fine_edit($id)
    {
        $data['page_url'] = 'fine';
        $data['fine'] = Room::find($id);
        return view('setting/setting-fine-form', $data);
    }

    public function fine_update(Request $request, $id)
    {
        
        try{

            $update = Room::whereIn('id', explode(',', $id))
                            ->update([
                                'fine_type' => $request->fine_type == null?0:$request->fine_type,
                                'fine_day' => $request->fine_day == null?0:$request->fine_day,
                                // 'after_fine_day' => $request->after_fine_day == null?0:$request->after_fine_day,
                                'maximum_fine' => $request->maximum_fine == null?0:$request->maximum_fine,
                                'start_fine_day' => $request->start_fine_day == null?0:$request->start_fine_day
                            ]);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function manage_bill(Request $request)
    {
        return view('setting/setting-manageBill');
    }
    public function rental_contract(Request $request)
    {
        return view('setting/setting-rentalContract');
    }
    public function dorm_info(Request $request)
    {
        $data['province'] = Province::get();
        $data['district'] = District::get();
        $data['subdistrict'] = Subdistrict::get();
        $apartment = Apartment::orderBy('id','desc')->first();
        $data['apartment'] = $apartment;
        return view('setting/setting-dormInfo', $data);
    }
    public function room_rent(Request $request)
    {
        $data['buildings'] = Building::where('ref_branch_id', session("branch_id"))->get();
        $data['floors'] = Floor::whereHas('building', function ($query) {
                                            $query->where('ref_branch_id', session("branch_id"));
                                        })->get();
        $data['page_url'] = 'setting/room-rent';

        return view('setting/setting-roomRent',$data);
    }
    public function room_rent_datatable(Request $request)
    {
        $results = Room::leftJoin('floors', 'rooms.ref_floor_id', '=', 'floors.id')
                        ->leftJoin('buildings', 'floors.ref_building_id', '=', 'buildings.id')
                        ->whereHas('floor.building', function ($query) {
                            $query->where('ref_branch_id', session("branch_id"));
                        })
                        ->select('rooms.*');
        
        if($request->building != "all"){
            $results = $results->Where('buildings.id', $request->building);
        }
        if($request->floor != "all"){
            $results = $results->Where('floors.id', $request->floor);
        }

        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }
        // $data['prefix'] = [ 1 => 'บริษัท', 2 => 'นาย', 3 => 'นางสาว', 4 => 'นาง'];
        $results = $results->paginate($limit);

        $data['list_data'] = $results;

        return view('setting/setting-roomRent-table', $data);
    }

    public function room_rent_edit($id)
    {
        $data['page_url'] = 'room_rent';
        $data['room_rent'] = Room::find($id);
        return view('setting/setting-roomRent-form', $data);
    }

    public function room_rent_update(Request $request, $id)
    {
        
        try{

            $update = Room::whereIn('id', explode(',', $id))
                            ->update([
                                'rent' => $request->rent == null?0:$request->rent,
                                'furniture_rental' => $request->furniture_rental == null?0:$request->furniture_rental
                            ]);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function room_layout(Request $request)
    {
        // $response = Http::get('http://100.64.10.101:7953/getRealTimeData.aspx');
        // $xmlString = $response->body();
        
        // // แปลง XML เป็น SimpleXMLElement
        // $xmlObject = simplexml_load_string($xmlString);

        // // แปลง SimpleXMLElement เป็น array ถ้าต้องการ
        // $json = json_encode($xmlObject);
        // $array = json_decode($json, true);

        // return $array; // ส่งกลับเป็น array
        
        $data['building'] = Building::where('ref_branch_id',session("branch_id"))->get();
        // $data['floor'] = Floor::where('ref_building_id',session("branch_id"))->get();
        return view('setting/setting-roomLayout', $data);
    }
    public function insert_all(Request $request)
    {
        try{
            $targetPath = rand().'_'.$request->file('file')->getClientOriginalName();
            $request->file('file')->move('upload/time_excel/', $targetPath);

            $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

            $spreadSheet = $Reader->load('upload/time_excel/'.$targetPath);
            $excelSheet = $spreadSheet->getActiveSheet();
            $spreadSheetAry = $excelSheet->toArray();
            // $ta = [];
            foreach($spreadSheetAry as $key => $value){
                if($key == 0){ continue; }
                if (strpos($value[0], 'ตึก') !== false) {
                    // ทำสิ่งที่ต้องการเมื่อพบคำว่า "ตึก"
                    $building = new Building;
                    $building->name  =  $value[0];
                    $building->ref_branch_id  =  session("branch_id");
                    $building->save();
                    continue;
                }
                if (strpos($value[0], 'ชั้น') !== false) {
                    // ทำสิ่งที่ต้องการเมื่อพบคำว่า "ชั้น"
                    $floor = new Floor;
                    $floor->name  =  $value[0];
                    $floor->ref_building_id  =  $building->id;
                    $floor->save();
                    continue;
                }

                $room = new Room;
                $room->name  =  $value[0];
                $room->ref_floor_id  =  $floor->id;
                $room->save();
                // if($value[0] == ''){ continue; }
                // if($value[0] != '' && @$spreadSheetAry[$key-1][0] == ''){ continue; }

                // $check = UserTime::where("employee_code", $value[2])->where("day_date", date('Y-m-d', strtotime($value[6])))->first();
                // if(!$check){
                //     $user = new UserTime;
                //     $user->ref_user_id = User::where('employee_id', $value[2])->first()->id;
                //     $user->machine_code = $value[1];
                //     $user->employee_code = $value[2];
                //     $user->position_name = $value[4];
                //     $user->branch_name = $value[5];
                //     $user->day_date = date('Y-m-d', strtotime($value[6]));
                //     $user->day_time_in = $value[7];
                //     $user->day_time_out = $value[8];
                //     $user->save();
                // }else{
                //     $user = UserTime::find($check->id);
                //     $user->ref_user_id = User::where('employee_id', $value[2])->first()->id;
                //     $user->machine_code = $value[1];
                //     $user->employee_code = $value[2];
                //     $user->position_name = $value[4];
                //     $user->branch_name = $value[5];
                //     $user->day_date = date('Y-m-d', strtotime($value[6]));
                //     $user->day_time_in = $value[7];
                //     $user->day_time_out = $value[8];
                //     $user->save();
                // }
                // if($value[0] == ''){ break; }
                
            }

            DB::commit();
            return 1;
            $data = array(
                "status" => true,
                "modal" => "".view('user-time/import-excel-modal')
            );
            return $data;
            
        } catch (QueryException $err) {
            DB::rollBack();
        }
        
        $data['building'] = Building::where('ref_branch_id',session("branch_id"))->get();
        // $data['floor'] = Floor::where('ref_building_id',session("branch_id"))->get();
        return view('setting/setting-roomLayout', $data);
    }
    public function room_layout_building(Request $request)
    {
        $data['building'] = Building::where('ref_branch_id',session("branch_id"))->get();
        return view('setting/setting-roomLayout-building', $data);
    }
    public function room_layout_building_insert(Request $request)
    {
        try{
            $user = new Building;
            $user->name  =  $request->name;
            $user->ref_branch_id  =  session("branch_id");
            $user->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function room_layout_building_delete($id)
    {
        try{

            Building::destroy($id);

            $floor = Floor::where('ref_building_id', $id)->get('id')->toArray();

            Floor::where('ref_building_id', $id)->delete();
            Room::whereIn('ref_floor_id', $floor)->delete();

            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function room_layout_floor($building_id)
    {
        $data['row']['floor'] = Floor::where('ref_building_id', $building_id)->get();
        return view('setting/setting-roomLayout-floor', $data);
    }
    public function room_layout_floor_insert(Request $request)
    {
        try{
            $user = new Floor;
            $user->name  =  $request->name;
            $user->ref_building_id  =  $request->ref_building_id;
            $user->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function room_layout_floor_delete($id)
    {
        try{
            Floor::destroy($id);
            Room::where('ref_floor_id', $id)->delete();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function room_layout_room($floor_id)
    {
        $data['floor']['room'] = Room::where('ref_floor_id', $floor_id)->get();
        return view('setting/setting-roomLayout-room', $data);
    }
    public function room_layout_room_insert(Request $request)
    {
        try{
            $user = new Room;
            $user->name  =  $request->name;
            $user->ref_floor_id  =  $request->ref_floor_id;
            $user->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function room_layout_room_update(Request $request, $id)
    {
        try{
            $user = Room::find($id);
            $user->name  =  $request->name;
            $user->save();
            
            DB::commit();
            return $user->name;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function room_layout_room_delete($id)
    {
        try{
            Room::destroy($id);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    // public function water_electric_bill(Request $request)
    // {
    //     return view('setting/setting-waterElectricBill');
    // }
    public function water_electric_bill(Request $request)
    {
        $data['buildings'] = Building::where('ref_branch_id', session("branch_id"))->get();
        $data['floors'] = Floor::whereHas('building', function ($query) {
                                            $query->where('ref_branch_id', session("branch_id"));
                                        })->get();
        $data['page_url'] = 'setting/water-electric-bill';

        return view('setting/setting-waterElectricBill', $data);
    }
    public function water_electric_bill_datatable(Request $request)
    {
        $results = Room::leftJoin('floors', 'rooms.ref_floor_id', '=', 'floors.id')
                        ->leftJoin('buildings', 'floors.ref_building_id', '=', 'buildings.id')
                        ->whereHas('floor.building', function ($query) {
                            $query->where('ref_branch_id', session("branch_id"));
                        })
                        ->select('rooms.*');
        
        if($request->building != "all"){
            $results = $results->Where('buildings.id', $request->building);
        }
        if($request->floor != "all"){
            $results = $results->Where('floors.id', $request->floor);
        }

        $limit = 15;
        if(@$request['limit']){
            $limit = $request['limit'];
        }
        // $data['prefix'] = [ 1 => 'บริษัท', 2 => 'นาย', 3 => 'นางสาว', 4 => 'นาง'];
        $results = $results->paginate($limit);

        $data['list_data'] = $results;

        return view('setting/setting-waterElectricBill-table', $data);
    }

    public function water_bill_edit($id)
    {
        $data['page_url'] = 'water-electric-bill';
        $water_bill = Room::find($id);
        $data['water_bill'] = $water_bill;
        if($water_bill){
            $data['how'] = [$water_bill->how_to_cal_water => 'checked' ];
        }else{
            $data['how'] = [ 4 => 'checked' ];
        }
        return view('setting/setting-waterBill-form', $data);
    }

    public function electric_bill_edit($id)
    {
        $data['page_url'] = 'water-electric-bill';
        $electric_bill = Room::find($id);
        $data['electric_bill'] = $electric_bill;
        if($electric_bill){
            $data['how'] = [$electric_bill->how_to_cal_ele => 'checked' ];
        }else{
            $data['how'] = [ 3 => 'checked' ];
        }
        return view('setting/setting-electricBill-form', $data);
    }

    public function water_bill_update(Request $request, $id)
    {
        
        try{

            $update = Room::whereIn('id', explode(',', $id))
                            ->update([
                                'how_to_cal_water' => $request->how_to_cal_water == null?0:$request->how_to_cal_water,
                                'water_baht_per_unit' => $request->water_baht_per_unit == null?0:$request->water_baht_per_unit,
                                'water_monthly_rental' => $request->water_monthly_rental == null?0:$request->water_monthly_rental
                            ]);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function electric_bill_update(Request $request, $id)
    {
        
        try{

            $update = Room::whereIn('id', explode(',', $id))
                            ->update([
                                'how_to_cal_ele' => $request->how_to_cal_ele == null?0:$request->how_to_cal_ele,
                                'ele_baht_per_unit' => $request->ele_baht_per_unit == null?0:$request->ele_baht_per_unit,
                                'ele_monthly_rental' => $request->ele_monthly_rental == null?0:$request->ele_monthly_rental
                            ]);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function service_discount(Request $request)
    {
        $data['page_url'] = 'setting/setting-serviceDiscount';
        return view('setting/setting-serviceDiscount', $data);
    }

    public function service_datatable(Request $request)
    {
        $data['building'] = Building::where('ref_branch_id',session("branch_id"))->get();
        
        // $data['list_data'] = $results;

        return view('setting/setting-service-table', $data);
    }
    public function get_service(Request $request)
    {
        $data['service'] = Service::where('ref_branch_id',session("branch_id"))->get();
        
        // $data['list_data'] = $results;

        return view('setting/setting-service-form-add', $data);
    }
    public function service_room($id)
    {
        $data['service_id'] = RoomHasService::where('ref_room_id', $id)->pluck('ref_service_id')->toArray();
        $data['service'] = Service::where('ref_branch_id',session("branch_id"))->get();
        
        $data['room_id'] = $id;

        return view('setting/setting-service-view', $data);
    }

    public function service_insert(Request $request)
    {
        
        try{
            $insert = new Service;
            $insert->name  =  $request->name;
            $insert->price  =  $request->price;
            $insert->ref_branch_id  =  session("branch_id");
            $insert->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function service_update(Request $request, $id)
    {
        // return 1;
        try{

            $update = Service::find($id);
            $update->name  =  $request->name;
            $update->price  =  $request->price;
            $update->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function service_room_update(Request $request)
    {
        try{
            $ref_room_id = explode(',',$request->ref_room_id);
            RoomHasService::whereIn('ref_room_id', $ref_room_id)->delete();
            foreach($ref_room_id as $room_id){
                foreach($request->ref_service_id as $id){
                    $insert = new RoomHasService;
                    $insert->ref_room_id  =  $room_id;
                    $insert->ref_service_id  =  $id;
                    $insert->save();
                }
            }
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function service_on_room_delete(Request $request)
    {
        try{
            RoomHasService::whereIn('ref_room_id', $request->id)->delete();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function service_delete($id)
    {
        try{
            service::destroy($id);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }

    /////////////////////////////////////////////////
    /////////////////////////////////////////////////

    public function bank(Request $request)
    {
        
        $data['page_url'] = 'setting/bank';
        $data['bank'] = Bank::where('ref_branch_id',session("branch_id"))->get();

        return view('setting/setting-bank', $data);
    }
    public function bank_datatable(Request $request)
    {
        $results = Bank::where('ref_branch_id',session("branch_id"))->orderBy('id','DESC')->get();
        
        $data['list_data'] = $results;

        return view('setting/setting-bank-table', $data);
    }
    
    public function bank_insert(Request $request)
    {
        
        try{
            $insert = new Bank;
            $insert->bank  =  $request->bank;
            $insert->branch  =  $request->branch;
            $insert->bank_account_name  =  $request->bank_account_name;
            $insert->bank_account_number  =  $request->bank_account_number;
            $insert->remark  =  $request->remark;
            $insert->ref_branch_id  =  session("branch_id");
            $insert->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    public function bank_edit($id)
    {
        $data['page_url'] = 'bank';
        $data['bank'] = Bank::find($id);
        return view('setting/setting-bank-form', $data);
    }
 

    public function bank_update(Request $request, $id)
    {
        
        try{

            $update = Bank::find($id);
            $update->bank  =  $request->bank;
            $update->branch  =  $request->branch;
            $update->bank_account_name  =  $request->bank_account_name;
            $update->bank_account_number  =  $request->bank_account_number;
            $update->remark  =  $request->remark;
            $update->save();
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function bank_delete($id)
    {
        try{
            Bank::destroy($id);
            
            DB::commit();
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
    /////////////////////////////////////////////////
}
