<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Course;
use App\Models\RoomTypeHasCourse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/room-type';
        $data['page'] = 'สินค้า';
        $user = Auth::user();
        $data['course'] = Course::get();
        $data['room'] = Room::orderByRaw('CAST(name AS UNSIGNED)')->get();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin/room-type/index', $data);
    }

    public function change_status(Request $request, $id)
    {
        try {

            $user = RoomType::find($id);
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
                // RoomType::where('ref_branch_id', Auth::user()->ref_branch_id)->where('sort', '>', $old_sort)->where('sort', '<=', $new_sort)->decrement('sort'); // ลดลง -1
                RoomType::where('sort', '>', $old_sort)->where('sort', '<=', $new_sort)->decrement('sort'); // ลดลง -1
            }else{
                // RoomType::where('ref_branch_id', Auth::user()->ref_branch_id)->where('sort', '<', $old_sort)->where('sort', '>=', $new_sort)->increment('sort'); // เพิ่มขึ้น +1
                RoomType::where('sort', '<', $old_sort)->where('sort', '>=', $new_sort)->increment('sort'); // เพิ่มขึ้น +1
            }
            RoomType::where('id', $id)->update(['sort' => $new_sort]); // ลดลง
            // return 123;
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function datatable(Request $request)
    {
        $results = RoomType::orderBy('sort', 'ASC');

        // 🔍 search
        if (!empty($request->search)) {
            $results = $results->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('remark', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('sixty_minutes', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('ninety_minutes', 'LIKE', '%' . $request->search . '%');
            });
        }

        // 🔍 filter branch
        // if (!empty($request->ref_branch_id)) {
        //     $results = $results->whereHas('room', function ($query) use ($request) {
        //                             $query->where('ref_branch_id', $request->ref_branch_id);
        //                         });
        // }
        // 🔍 filter room
        // if (!empty($request->ref_room_id)) {
        //     $results = $results->where('ref_room_id', $request->ref_room_id);
        // }

        $limit = $request->limit ?? 15;
        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['page_url'] = 'admin/room-type';

        return view('admin/room-type/table', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New RoomType';
        $data['room_type'] = RoomType::get();
        $data['action'] = route('room-type.store');
        return view('admin/room-type/form', $data);
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
            $last_sort = 0;
            $last = RoomType::orderBy('sort', 'DESC')->first();
            if($last){
                $last_sort = $last->sort;
            }
            $room_type = new RoomType;
            // $room_type->ref_room_id = $request->ref_room_id;
            $room_type->name = $request->name;
            $room_type->remark = $request->remark;
            $room_type->sort = $last_sort+1;
            $room_type->save();

            foreach($request->course as $c_id => $course){
                // return $course['price'];
                $update = new RoomTypeHasCourse;
                $update->price = $course['price'];
                $update->commission = $course['commission'] ?? 0;
                $update->coupon = $course['coupon'] ?? 0;
                $update->ref_course_id = $c_id;
                $update->ref_room_type_id = $room_type->id;
                $update->save();
            }

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

        $data['page_url'] = 'admin/room-type';
        $room = Room::get();
        $data['room'] = $room;
        $couse = Course::get();
        // $couse = Course::where('ref_room_type_id', $id)->get();
        foreach($couse as $row){
            $rthc = RoomTypeHasCourse::where('ref_course_id', $row->id)->where('ref_room_type_id', $id)->first();
            if(!$rthc){
                $rthc = new RoomTypeHasCourse;
                $rthc->ref_room_type_id = $id;
                $rthc->ref_course_id = $row->id;
                $rthc->save();
            }
        }
        DB::commit();
        $room_type = RoomType::find($id);
        $data['room_type'] = $room_type;
        $user = Auth::user();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin/room-type/view', $data);
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
        // return $request;
        try {
            foreach($request->edit as $h_id => $edit){
                // return $edit['price'];
                $update = RoomTypeHasCourse::find($h_id);
                $update->price = $edit['price'];
                $update->commission = $edit['commission'];
                $update->coupon = $edit['coupon'];
                $update->save();
            }
            $room_type = RoomType::find($id);
            // $room_type->ref_room_id = $request->ref_room_id;
            $room_type->name = $request->name;
            // $room_type->forty_minutes = $request->forty_minutes;
            // $room_type->sixty_minutes = $request->sixty_minutes;
            // $room_type->ninety_minutes = $request->ninety_minutes;
            // $room_type->commission_forty_minutes = $request->commission_forty_minutes;
            // $room_type->commission_sixty_minutes = $request->commission_sixty_minutes;
            // $room_type->commission_ninety_minutes = $request->commission_ninety_minutes;
            // $room_type->coupon_forty_minutes = $request->coupon_forty_minutes;
            // $room_type->coupon_sixty_minutes = $request->coupon_sixty_minutes;
            // $room_type->coupon_ninety_minutes = $request->coupon_ninety_minutes;
            $room_type->remark = $request->remark;
            $room_type->save();

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
            $sort = RoomType::find($id)->sort;
            RoomType::where('sort', '>', $sort)->decrement('sort'); // ลดลง -1
            RoomType::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}