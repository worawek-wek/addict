<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomPOSController extends Controller
{
    public function index()
    {
    

        $user = auth()->user();
        $rooms = Room::orderBy('room_group_id')->orderByRaw('CAST(name AS UNSIGNED)')
            ->when($user && $user->ref_position_id != 0, function($q) use ($user) {
                $q->where('ref_branch_id', $user->ref_branch_id);
            })
            ->where('ref_status_id', 1)
            ->get()
            ->map(function ($room) {
                $activeOrder = Order::where('ref_room_id', $room->id)
                    ->where('ref_status_id', 2) // 2 = กำลังใช้งาน
                    ->whereDate('booking_date', Carbon::today())
                    ->whereTime('start_time', '<=', Carbon::now()->format('H:i:s'))
                    ->whereTime('end_time', '>=', Carbon::now()->format('H:i:s'))
                    ->first();

                $room->is_busy = $activeOrder ? true : false;
                if ($activeOrder) {
                    $staffName = null;
                    if ($activeOrder->ref_user_id) {
                        $staff = \App\Models\User::find($activeOrder->ref_user_id);
                        $staffName = $staff ? ($staff->nickname ?? $staff->name) : null;
                    }
                    $room->active_order = (object) [
                        'start_time' => $activeOrder->start_time,
                        'end_time'   => $activeOrder->end_time,
                        'staff_name' => $staffName,
                    ];
                }
                return $room;
            });

        return view('pos.room.index', compact('rooms'));
    }


    // ✅ โหลดลูกค้าในห้อง
    public function getCustomers($roomId)
    {
        $orders = Order::with('customer')
            ->where('ref_room_id', $roomId)
            ->whereDate('booking_date', Carbon::today())
            ->whereTime('start_time', '<=', Carbon::now()->format('H:i:s'))
            ->whereTime('end_time', '>=', Carbon::now()->format('H:i:s'))
            ->get();

        $customers = $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'name'     => $order->customer->name ?? 'Unknown',
                'phone'    => $order->customer->phone ?? '',
            ];
        });

        return response()->json($customers);
    }
}
