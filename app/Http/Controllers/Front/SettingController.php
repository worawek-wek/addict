<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Building;
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
        return view('setting/setting-fine');
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
        return view('setting/setting-roomRent');
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
        
        $data['building'] = Building::where('ref_branch_id',1)->get();
        // $data['floor'] = Floor::where('ref_building_id',1)->get();
        return view('setting/setting-roomLayout', $data);
    }
    public function room_layout_building(Request $request)
    {
        $data['building'] = Building::where('ref_branch_id',1)->get();
        return view('setting/setting-roomLayout-building', $data);
    }
    public function room_layout_building_insert(Request $request)
    {
        try{
            $user = new Building;
            $user->name  =  $request->name;
            $user->ref_branch_id  =  1;
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
    public function water_electric_bill(Request $request)
    {
        return view('setting/setting-waterElectricBill');
    }
    public function service_discount(Request $request)
    {
        return view('setting/setting-serviceDiscount');
    }
    public function bank(Request $request)
    {
        return view('setting/setting-bank');
    }
}
