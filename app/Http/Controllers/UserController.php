<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\UserTime;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Work_shift;
use App\Models\Schedule;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Mpdf\Mpdf;

DB::beginTransaction();

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // สร้าง instance ของ mPDF

    public function index()
    {

        $data['page_url'] = 'admin/user';
        $data['page'] = 'พนักงาน';
        $data['user'] = User::get();
        $data['position'] = Position::get();
        $user = Auth::user();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        }        // $data['title'] = 'Profile';

        return view('admin/user/index', $data);
    }

    public function profile($id = null)
    {
        if ($id) {
            $user = User::find($id);
        } else {
            $user = Auth::user();
        }

        $user->birthday_th = $this->ChangeDateToTH($user->birthday);
        ////////////////////// แปลงรูปแบบวันเกิดเป็น ไทย

        $user->position_name = Position::find($user->ref_position_id)->position_name;
        $data['page_url'] = 'admin/user';
        $data['title'] = 'Profile';
        $data['user'] = $user;

        return view('admin/user/profile', $data);
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

    public function datatable(Request $request)
    {
        $results = User::where('work_status', '!=', 3)
            ->orderBy('id', 'DESC');

        if (@$request->search) {
            $results = $results->orWhere(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('salary', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('remark', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->ref_branch_id != "all") {
            $results = $results->Where('ref_branch_id', $request->ref_branch_id);
        }
        if ($request->ref_position_id != "all") {
            $results = $results->Where('ref_position_id', $request->ref_position_id);
        }

        $limit = 15;
        if (@$request['limit']) {
            $limit = $request['limit'];
        }

        $results = $results->with('position')->paginate($limit);
        // return $results->items();
        // dd($results);
        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;

        $data['list_data'] = $results;
        return view('admin/user/table', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New Employee';
        $data['branch'] = Branch::get();
        $data['position'] = Position::get();
        $data['work_shift'] = Work_shift::get();
        $data['schedule'] = Schedule::get();
        $data['leave'] = Leave::get();
        $data['boss'] = User::get();
        $data['action'] = route('user.store');
        $data['user'] = [
            'leave_just_id' => ["1", "2", "3", "9"],
            'leave_id_number' => ["1" => "6", "2" => "30", "3" => "6", "9" => "10"]
        ];
        // return $data['user'];
        return view('admin/user/form', $data);
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

        $image_name = "";
        if ($request->file('image_name')) {
            $file = $request->file('image_name');
            $nameExtension = $file->getClientOriginalName();
            $extension = pathinfo($nameExtension, PATHINFO_EXTENSION);
            $img_name = pathinfo($nameExtension, PATHINFO_FILENAME);
            $path = "upload/user/";
            $image_name = $img_name . rand() . '.' . $extension;
        }
        try {
            $user = new User;
            $user->name  =  $request->name;
            $user->username  =  $request->email;
            $user->user_code  =  $request->user_code;
            $user->nickname  =  $request->nickname;
            $user->email  =  $request->email;
            $user->ref_position_id  =  $request->ref_position_id;
            $user->remark  =  $request->remark;
            $user->salary  =  $request->salary;
            $user->image_name = $image_name;
            $user->ref_branch_id  =  $request->ref_branch_id;
            // $user->ref_branch_id  =  session("branch_id");
            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();
            if (@$file) $file->move($path, $image_name);
            return 1;
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
        $user = User::with(['branch', 'position'])->findOrFail($id);

        return view('admin/user/show', compact('user'));
    }
    public function ChangeDateFormat($date)
    {
        $dateCreate = date_create_from_format('d F, Y', $date);
        $formattedDate = date_format($dateCreate, 'Y-m-d');
        return $formattedDate;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $data['page_url'] = 'admin/user';
        $data['position'] = Position::get();
        $data['user'] = User::find($id);
        $user = Auth::user();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin/user/view', $data);
    }

    public function change_status(Request $request, $id)
    {
        try {

            $user = User::find($id);
            $user->ref_status_id = $request->ref_status_id;
            $user->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
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

        try {
            $user = User::find($id);
            $user->user_code  =  $request->user_code;
            $user->name  =  $request->name;
            $user->nickname  =  $request->nickname;
            $user->email  =  $request->email;
            $user->ref_position_id  =  $request->ref_position_id;
            $user->ref_branch_id  =  $request->ref_branch_id;
            $user->remark  =  $request->remark;
             $user->salary  =  $request->salary;
            if (!empty($request->password)) {
                $user->password = Hash::make($request->password);
            }

            if ($request->file('image_name')) {
                // return 123;
                $file = $request->file('image_name');
                $nameExtension = $file->getClientOriginalName();
                $extension = pathinfo($nameExtension, PATHINFO_EXTENSION);
                $img_name = pathinfo($nameExtension, PATHINFO_FILENAME);
                $path = "upload/user/";
                $image_name = $img_name . rand() . '.' . $extension;
                $user->image_name = $image_name;
            }
            $user->save();

            DB::commit();

            if (@$file) {
                @unlink("$path/$lastImage");
                $file->move($path, $image_name);
            }
            return 1;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function clock_in(Request $request)
    {
        try {
            $find = User::where('user_code', $request->user_code)->first();

            if (!$find) {
                return "เข้างานผิดพลาด ไม่พบพนักงาน";
            }
            $user = User::find($find->id);
            $user->work_status = 1;
            $user->save();

            DB::commit();

            return "คุณ $user->nickname เข้างานสำเร็จ";
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function check_password(Request $request)
    {
        $user = Auth::user();
        if (Hash::check($request->password, $user->password)) {
            return true;
        }
        return false;
    }
    public function change_password(Request $request)
    {
        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();
            // return $user->password;
            DB::commit();
            return redirect('/')->with('message', 'Insert Employee "' . $request->name . '" success');
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function edit_news(Request $request, $id = 1)
    {
        try {
            $news = News::find($id);
            $news->detail = $request->detail;
            $news->save();

            DB::commit();
            // $data['news_detail'] = $user->detail;
            return $news->detail;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
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
            $user = User::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}

// $targetPath = $request->file('image_name')->getClientOriginalName();
// $request->file('image_name')->move('upload/excel/', $targetPath);

// $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

// $spreadSheet = $Reader->load('upload/excel/'.$targetPath);
// $excelSheet = $spreadSheet->getActiveSheet();
// return $spreadSheetAry = $excelSheet->toArray();
