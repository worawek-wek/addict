<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeaveController;
use App\Models\User;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Work_shift;
use App\Models\Schedule;
use App\Models\Leave;
use App\Models\RentBill;
use App\Models\RoomForRents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

DB::beginTransaction();

class FrontClockInController extends Controller
{
    private function normalizeCardCode($value): string
    {
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', trim((string) $value));

        return strtr($value, [
            'ๅ' => '1',
            '/' => '2',
            '-' => '3',
            'ภ' => '4',
            'ถ' => '5',
            'ุ' => '6',
            'ึ' => '7',
            'ค' => '8',
            'ต' => '9',
            'จ' => '0',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $branch = null)
    {

        // $user = User::find(2);
        // $user->name = "1";
        // $user->save();
        // DB::commit();

        // if(!is_null($id)){
        //     session(["branch_id" => $id]);
        //     return redirect('clock-in');
        // }
        $branchModel = Branch::findOrFail($branch);
        $data['branch'] = $branchModel;
        $data['page_url'] = url("admin/{$branchModel->id}/clock-in");

        return view('clock-in/index', $data);
    }
    public function clock_in(Request $request, $branch = null)
    {
        try{
            $branchModel = Branch::find($branch);
            if (!$branchModel) {
                return "เข้างานผิดพลาด ไม่พบสาขา";
            }

            $userCode = $this->normalizeCardCode($request->user_code);
            $matchedUsers = User::where(function ($q) use ($userCode) {
                $q->where('user_code', $userCode)
                    ->orWhere('user_id', $userCode);
            });

            if ($userCode === '') {
                return "เข้างานผิดพลาด ไม่พบพนักงาน";
            }

            $find = (clone $matchedUsers)->where('ref_branch_id', $branchModel->id)->first();

            if(!$find){
                if ((clone $matchedUsers)->where('ref_branch_id', '!=', $branchModel->id)->exists()) {
                    return "เข้างานผิดพลาด พนักงานไม่อยู่สาขานี้";
                }

                return "เข้างานผิดพลาด ไม่พบพนักงาน";
            }
            $user = User::find($find->id);
            if ((int) $user->work_status === 1) {
                return "รหัส $userCode วันนี้ได้เข้างานแล้ว";
            }

            $user->work_status = 1;
            $user->ref_status_id = 1;
            $user->save();

            DB::commit();

            return "คุณ $user->nickname เข้างานสำเร็จ";

        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function datatable(Request $request)
    {
        $results = RentBill::orderBy('id','DESC')
                                ->join('room_for_rents', 'rent_bills.ref_room_for_rent_id', '=', 'room_for_rents.id')
                                ->join('renters', 'room_for_rents.ref_renter_id', '=', 'renters.id')
                                ->join('rooms', 'room_for_rents.ref_room_id', '=', 'rooms.id')
                                ->Where('rent_bills.ref_status_id', 3)
                                ->select('rent_bills.*', 'renters.prefix' , DB::raw('CONCAT(renters.name, " ", COALESCE(renters.surname, "")) as renter_name'), 'rooms.name as room_name', 'rooms.rent');

        if(@$request->search){
            $results = $results->Where(function ($query) use ($request) {
                                    $query->whereRaw("CONCAT(renters.prefix ,' ' , renters.name, ' ', renters.surname) LIKE ?", ["%{$request->search}%"])
                                        ->orWhere('rooms.name','LIKE','%'.$request->search.'%');
                                });
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

        return view('admin/dashboard/table', $data);
    }
    public function change_password_form()
    {
        $user = Auth::user();

        $user->work_start_date_th = $this->ChangeDateToTH($user->work_start_date);
        $user->birthday_th = $this->ChangeDateToTH($user->birthday);

        $data['user'] = $user;
        return view('admin/dashboard/change_password', $data);

    }
    public function invoice($id)
    {
        $data['page_url'] = 'dashboard';
        $invoice = RentBill::find($id);
        $data['invoice'] = $invoice;

        return view('admin/dashboard/invoice', $data);
    }

    public function ChangeDateToTH($date)
    {
        ////////////////////// แปลงรูปแบบวันเกิดเป็น ไทย
        // สร้าง Carbon instance จากวันที่
        $m = date('m', strtotime($date));
        $date = Carbon::createFromFormat('Y-m-d', $date);

        // คำนวณปีพุทธศักราช
        $buddhistYear = $date->year + 543;

        // แปลงวันที่เป็นรูปแบบไทย
        $thaiDate = $date->formatLocalized('%e %B ' . $buddhistYear);

        $monthTH = [
                "01" => "มกราคม",
                "02" => "กุมภาพันธ์",
                "03" => "มีนาคม",
                "04" => "เมษายน",
                "05" => "พฤษภาคม",
                "06" => "มิถุนายน",
                "07" => "กรกฎาคม",
                "08" => "สิงหาคม",
                "09" => "กันยายน",
                "10" => "ตุลาคม",
                "11" => "พฤศจิกายน",
                "12" => "ธันวาคม"
        ];
        $monthEN = [
                "01" => "January",
                "02" => "February",
                "03" => "March",
                "04" => "April",
                "05" => "May",
                "06" => "June",
                "07" => "July",
                "08" => "August",
                "09" => "September",
                "10" => "October",
                "11" => "November",
                "12" => "December"
        ];
        return str_replace($monthEN[$m], $monthTH[$m], $thaiDate);
    }
}
