<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderRoomController extends Controller
{
    public function index()
    {
        // โหลดหน้าแรกพร้อมข้อมูลเริ่มต้น
        $limit = request()->limit ?? 10;
        $orderRooms = $this->getOrderRooms($limit);
        $user = Auth::user(); // user ที่ login อยู่

        if ($user->work_status == 3) {
            // super admin เห็นทุกสาขา
            $branches = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาตัวเอง
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-room.index', compact('orderRooms', 'branches'));
    }

    public function datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $orderRooms = $this->getOrderRooms($limit);

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-room.datatable', compact('orderRooms', 'branches'));
    }

    private function getOrderRooms($limit)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::with(['branch', 'customer', 'user', 'room', 'status'])
            ->select('orders.*')
            ->orderByRaw("
            CASE
                WHEN ref_status_id = 2 THEN 1
                WHEN ref_status_id = 1
                     AND CONCAT(booking_date, ' ', end_time) >= '{$now}' THEN 2
                WHEN ref_status_id = 1
                     AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3
                WHEN ref_status_id = 3 THEN 4
                ELSE 5
            END
        ")
            ->orderBy('booking_date')
            ->orderBy('start_time');

        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $orderRooms = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderRooms as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);

            if ($order->ref_status_id == 2) {
                $order->badge_class = 'bg-success';
                $order->status_label = 'อยู่ระหว่างใช้บริการ';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->between($startDateTime, $endDateTime)) {
                $order->badge_class = 'bg-primary';
                $order->status_label = 'จอง (ถึงเวลาแล้ว)';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->lessThan($startDateTime)) {
                $order->badge_class = 'bg-warning';
                $order->status_label = 'จอง';
            } elseif ($order->ref_status_id == 1 && $nowCarbon->greaterThan($endDateTime)) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'จอง (เกินเวลา)';
            } elseif ($order->ref_status_id == 3) {
                $order->badge_class = 'bg-secondary';
                $order->status_label = 'ใช้บริการเสร็จสิ้น';
            } else {
                $order->badge_class = 'bg-dark';
                $order->status_label = 'ไม่ระบุ';
            }
        }

        return $orderRooms;
    }



    public function show($id)
    {
        $orderRoom = Order::with(['branch', 'room', 'status', 'addons.option', 'customer', 'user'])
            ->findOrFail($id);

        $statusId   = $orderRoom->status->id ?? null;
        $statusName = $orderRoom->status->name ?? 'ไม่ระบุ';

        $startDateTime = Carbon::parse($orderRoom->booking_date . ' ' . $orderRoom->start_time);
        $endDateTime   = Carbon::parse($orderRoom->booking_date . ' ' . $orderRoom->end_time);
        $now           = Carbon::now();

        $isOngoing  = $now->between($startDateTime, $endDateTime);
        $isOvertime = $now->greaterThan($endDateTime);

        if ($statusId === 2 || $isOngoing) {
            $orderRoom->badge_class = 'bg-success';
            $orderRoom->status_label = $statusName;
        } elseif ($isOvertime) {
            $orderRoom->badge_class = 'bg-danger';
            $orderRoom->status_label = 'เกินเวลา';
        } elseif (strtolower($statusName) === 'pending') {
            $orderRoom->badge_class = 'bg-warning';
            $orderRoom->status_label = $statusName;
        } elseif ($statusName === 'ยกเลิก') {
            $orderRoom->badge_class = 'bg-danger';
            $orderRoom->status_label = $statusName;
        } else {
            $orderRoom->badge_class = 'bg-secondary';
            $orderRoom->status_label = $statusName;
        }
        $statuses = OrderStatus::all();

        return view('admin.order-room.view', compact('orderRoom', 'statuses'));
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:order_status,id'
        ]);

        $order = Order::findOrFail($id);
        $order->ref_status_id = $request->status_id;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
            'status'  => $order->status->name
        ]);
    }
}
