<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['page_url'] = 'admin/room';
        $data['page'] = 'สินค้า';
        $user = Auth::user();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin/room/index', $data);
    }

    public function datatable(Request $request)
    {
        $results = Room::orderBy('id', 'DESC');

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
        if (!empty($request->ref_branch_id)) {
            $results = $results->where('ref_branch_id', $request->ref_branch_id);
        }

        $limit = $request->limit ?? 15;
        $results = $results->paginate($limit);

        $data['list_data'] = $results->appends(request()->query());
        $data['query'] = request()->query();
        $data['query']['limit'] = $limit;
        $data['page_url'] = 'admin/room';

        return view('admin/room/table', $data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'Add New Room';
        $data['room'] = Room::get();
        $data['action'] = route('room.store');
        return view('admin/room/form', $data);
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
            $room = new Room;
            $room->ref_branch_id = $request->ref_branch_id;
            $room->name = $request->name;
            $room->sixty_minutes = $request->sixty_minutes;
            $room->ninety_minutes = $request->ninety_minutes;
            $room->remark = $request->remark;
            $room->save();

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

        $data['page_url'] = 'admin/room';
        $data['room'] = Room::find($id);
          $user = Auth::user();

        if ($user->work_status == 3) {
            // super admin เห็นทุก branch
            $data['branch'] = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาของตัวเอง
            $data['branch'] = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin/room/view', $data);
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
            $room = Room::find($id);
            $room->ref_branch_id = $request->ref_branch_id;
            $room->name = $request->name;
            $room->sixty_minutes = $request->sixty_minutes;
            $room->ninety_minutes = $request->ninety_minutes;
            $room->remark = $request->remark;
            $room->save();

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
            Room::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}
