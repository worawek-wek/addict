<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

DB::beginTransaction();

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/course';
        $data['page'] = 'คอร์ส';
        $user = Auth::user();

        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }
        return view('admin/course/index', $data);
    }

    public function change_status(Request $request, $id)
    {
        try {

            $user = Course::find($id);
            $user->ref_status_id = $request->ref_status_id;
            $user->save();

            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function change_online_booking(Request $request, $id)
    {
        try {
            $course = Course::find($id);
            $course->show_online_booking = $request->show_online_booking ? 1 : 0;
            $course->save();

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
                Course::where('sort', '>', $old_sort)->where('sort', '<=', $new_sort)->decrement('sort'); // ลดลง -1
            }else{
                Course::where('sort', '<', $old_sort)->where('sort', '>=', $new_sort)->increment('sort'); // เพิ่มขึ้น +1
            }
            Course::where('id', $id)->update(['sort' => $new_sort]); // ลดลง
            // return 123;
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
    }
    public function datatable(Request $request)
    {
        $results = Course::orderBy('name');

        // 🔍 search
        if (!empty($request->search)) {
            $results = $results->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('remark', 'LIKE', '%' . $request->search . '%');
            });
        }

        // 🔍 filter branch
        if (!empty($request->ref_branch_id)) {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }

        $limit = $request->limit ?? 15;
        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['page_url'] = 'admin/course';

        return view('admin/course/table', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New Course';
        $data['course'] = Course::get();
        $data['action'] = route('course.store');
        return view('admin/course/form', $data);
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
            $lastSort = Course::lockForUpdate()->max('sort') ?? 0;

            $course = new Course;
            $course->ref_branch_id = $request->ref_branch_id;
            $course->name = $request->name;
            $course->minute = $request->minute;
            $course->show_online_booking = $request->boolean('show_online_booking');
            // $course->sixty_minutes = $request->sixty_minutes;
            // $course->ninety_minutes = $request->ninety_minutes;
            // $course->forty_minutes = $request->forty_minutes;
            $course->remark = $request->remark;
            $course->sort  =  $lastSort + 1;
            $course->save();

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

        $data['page_url'] = 'admin/course';
        $data['course'] = Course::find($id);
        $user = Auth::user();

        // if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาของตัวเอง
        //     $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        // }
        return view('admin/course/view', $data);
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
        try {
            $course = Course::find($id);
            $course->ref_branch_id = $request->ref_branch_id;
            $course->name = $request->name;
            $course->minute = $request->minute;
            $course->show_online_booking = $request->boolean('show_online_booking');
            // $course->forty_minutes = $request->forty_minutes;
            // $course->sixty_minutes = $request->sixty_minutes;
            // $course->ninety_minutes = $request->ninety_minutes;
            $course->remark = $request->remark;
            $course->save();

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
    public function delete($id)
    {
        try {
            Course::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}
