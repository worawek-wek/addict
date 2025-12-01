<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RoomGroupModel;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomGroupController extends Controller
{
    protected $paginate_default = 10;
    protected $page_url = '/admin/room-groups';

    public function index()
    {
        $page_url = $this->page_url;
        $branches = Branch::get();
        return view('admin.roomGroup.index', compact('page_url', 'branches'));
    }

    public function datatable(Request $request)
    {
        $limit = $request->input('limit', $this->paginate_default);
        $search = $request->input('search');

        $query = RoomGroupModel::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $data = $query->withCount('RoomChildren')->paginate($limit);
        $page_url = $this->page_url;

        return view('admin.roomGroup.datatable', compact('data', 'page_url'));
    }


    public function create(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'branch_id' => 'required|exists:branchs,id',
            ]);
            RoomGroupModel::create([
                'name' => $request->name,
                'branch_id' => $request->branch_id,
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Room group created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error creating room group: ' . $e->getMessage()
            ]);
        }
    }



    public function delete(Request $request, $id)
    {
        try {
            $roomGroup = RoomGroupModel::findOrFail($id);
            $roomGroup->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Room group deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error deleting room group: ' . $e->getMessage()
            ]);
        }
    }


    public function getRoom(Request $request, $id = null)
    {
        if (!empty($id)) {
            $data  = Room::where('room_group_id', $id)->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ]);
        }

        $data = Room::whereNull('room_group_id')->get();
        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function addRoomToGroup(Request $request, $id)
    {
        try {
            $request->validate([
                'room_ids' => 'required|array',
                'room_ids.*' => 'exists:rooms,id'
            ]);

            Room::whereIn('id', $request->room_ids)->update(['room_group_id' => $id]);

            return response()->json([
                'status' => 200,
                'message' => 'Rooms added to group successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error adding rooms to group: ' . $e->getMessage()
            ]);
        }
    }

    public function removeRoomFromGroup(Request $request, $roomId)
    {
        try {
            $room = Room::findOrFail($roomId);
            $room->room_group_id = null;
            $room->save();

            return response()->json([
                'status' => 200,
                'message' => 'Room removed from group successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error removing room from group: ' . $e->getMessage()
            ]);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $roomGroup = RoomGroupModel::findOrFail($id);
            $roomGroup->name = $request->name;
            $roomGroup->branch_id = $request->branch_id;
            $roomGroup->save();

            return response()->json([
                'status' => 200,
                'message' => 'Room group updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error updating room group: ' . $e->getMessage()
            ]);
        }
    }
}
