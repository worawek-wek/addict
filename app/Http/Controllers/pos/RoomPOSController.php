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
        $rooms = Room::select('id', 'name', 'ref_branch_id')
            ->get()
            ->map(function ($room) {
                $activeOrder = Order::where('ref_room_id', $room->id)
                    ->where('ref_status_id', 2) // 2 = กำลังใช้งาน
                    ->whereDate('booking_date', Carbon::today())
                    ->whereTime('start_time', '<=', Carbon::now()->format('H:i:s'))
                    ->whereTime('end_time', '>=', Carbon::now()->format('H:i:s'))
                    ->first();

                $room->is_busy = $activeOrder ? true : false;
                return $room;
            });

        return view('pos.room.index', compact('rooms'));
    }


    // ✅ โหลดลูกค้าในห้อง
    public function getCustomers($roomId)
    {
        $orders = Order::with('customer')
            ->where('ref_room_id', $roomId)
            ->where('ref_status_id', 2) // กำลังใช้งาน
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
