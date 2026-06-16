<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomPOSController extends Controller
{
    private function findActiveRoomOrder(int $roomId): ?Order
    {
        return Order::where('ref_room_id', $roomId)
            ->where('ref_status_id', 2)
            ->where('type', 1)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();
    }

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
                $activeOrder = $this->findActiveRoomOrder($room->id);

                $room->is_busy = $activeOrder ? true : false;
                if ($activeOrder) {
                    $staffName = null;
                    if ($activeOrder->ref_user_id) {
                        $staff = \App\Models\User::find($activeOrder->ref_user_id);
                        $staffName = $staff ? ($staff->nickname ?? $staff->name) : null;
                    }
                    $startDateTime = Carbon::parse($activeOrder->booking_date . ' ' . $activeOrder->start_time);
                    $endDateTime = Carbon::parse($activeOrder->booking_date . ' ' . $activeOrder->end_time);

                    if ($endDateTime->lessThan($startDateTime)) {
                        $endDateTime->addDay();
                    }

                    $room->active_order = (object) [
                        'id' => $activeOrder->id,
                        'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                        'end_time'   => $endDateTime->format('Y-m-d H:i:s'),
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
            ->where('ref_status_id', 2)
            ->where('type', 1)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
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
