<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CheerCharge;
use App\Models\CommissionsHistory;
use App\Models\DailySalesClosure;
use App\Models\Order;
use App\Models\RoomTypeHasCourse;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                            ->where('type', 1)
                            ->select('orders.*')
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

        $DailySalesClosure = DailySalesClosure::orderBy("id","DESC")->first();

        if (@$DailySalesClosure) {
            $query->where('created_at', ">" ,$DailySalesClosure->date_time);
        }

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // filter by booking_date (date_range, start_date, end_date)
        $dateRange = request('date_range');
        $startDate = request('start_date');
        $endDate = request('end_date');
        if ($dateRange && $dateRange !== 'custom') {
            // 1, 7, 14, 30 days
            $days = intval($dateRange);
            if ($days > 0) {
                $from = Carbon::today()->subDays($days - 1)->format('Y-m-d');
                $to = Carbon::today()->format('Y-m-d');
                $query->whereBetween('booking_date', [$from, $to]);
            }
        } elseif ($dateRange === 'custom' && $startDate && $endDate) {
            $query->whereBetween('booking_date', [$startDate, $endDate]);
        }

        $orderRooms = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderRooms as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);

            if (!empty($order->payment_method)) {
                $order->badge_class = 'bg-info';
                $order->status_label = $order->payment_method;
            } elseif ($order->ref_status_id == 2) {
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
            } elseif ($order->ref_status_id == 4) {
                $order->badge_class = 'bg-danger';
                $order->status_label = 'ยกเลิก';
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

        $room_course_price = 0;
        $room_course = RoomTypeHasCourse::where('ref_room_type_id', $orderRoom->ref_room_type_id)->where('ref_course_id', $orderRoom->service_laundry_cost)->first();
        if($room_course){
            $room_course_price = $room_course->price;
        }

        $statusId   = $orderRoom->status->id ?? null;
        $statusName = $orderRoom->status->name ?? 'ไม่ระบุ';

        $startDateTime = Carbon::parse($orderRoom->booking_date . ' ' . $orderRoom->start_time);
        $endDateTime   = Carbon::parse($orderRoom->booking_date . ' ' . $orderRoom->end_time);
        $now           = Carbon::now();

        $isOngoing  = $now->between($startDateTime, $endDateTime);
        $isOvertime = $now->greaterThan($endDateTime);

        if (!empty($orderRoom->payment_method)) {
            $orderRoom->badge_class = 'bg-info';
            $orderRoom->status_label = $orderRoom->payment_method;
        } elseif ($statusId === 2 || $isOngoing) {
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

        return view('admin.order-room.view', compact('orderRoom', 'statuses','room_course_price'));
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
    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'nullable|string|max:100'
        ]);

        $order = Order::findOrFail($id);
        $order->payment_method = $request->payment_method;

        // --- คำนวณค่าคอมมิชชั่นพนักงานนวด ---
        $commission_value = 0;
        $commission_options_value = 0;
        // 1. คำนวณจาก AddonOption (ถ้ามี addon_options จะไม่รวมกับ $commission_value)
        if ($order->user && $order->addons && $order->addons->count()) {
            foreach ($order->addons as $addonItem) {
                $commission = \App\Models\MassageCommission::where('ref_user_id', $order->user->id)
                    ->where('addon_options_id', $addonItem->ref_option_id)
                    ->where('ref_branch_id', $order->ref_branch_id)
                    ->first();
                // ถ้าไม่เจอ ให้ใช้ค่าเริ่มต้น (ref_user_id = null)
                if (!$commission) {
                    $commission = \App\Models\MassageCommission::whereNull('ref_user_id')
                        ->where('addon_options_id', $addonItem->ref_option_id)
                        ->where('ref_branch_id', $order->ref_branch_id)
                        ->first();
                }
                if ($commission) {
                    if ($commission->commission_amount) {
                        $commission_options_value += $commission->commission_amount;
                    } elseif ($commission->commission_percent) {
                        $commission_options_value += ($commission->commission_percent / 100) * $addonItem->price;
                    }
                }
            }
        }
        // 2. คำนวณจาก service_duration (ถ้ามี addon_options จะไม่รวมกับ $commission_value)
        if ($order->user && $order->service_laundry_cost) {
            $duration = null;
            switch ($order->service_laundry_cost) {
                case 'forty_minutes': $duration = 40; break;
                case 'sixty_minutes': $duration = 60; break;
                case 'ninety_minutes': $duration = 90; break;
            }
            if ($duration) {
                $commission = \App\Models\MassageCommission::where('ref_user_id', $order->user->id)
                    ->where('service_duration', $duration)
                    ->where('ref_branch_id', $order->ref_branch_id)
                    ->first();
                // ถ้าไม่เจอ ให้ใช้ค่าเริ่มต้น (ref_user_id = null)
                if (!$commission) {
                    $commission = \App\Models\MassageCommission::whereNull('ref_user_id')
                        ->where('service_duration', $duration)
                        ->where('ref_branch_id', $order->ref_branch_id)
                        ->first();
                }
                if ($commission) {
                    if ($commission->commission_amount) {
                        $commission_value += $commission->commission_amount;
                    } elseif ($commission->commission_percent) {
                        $room_price = 0;
                        if ($order->room) {
                            if ($duration == 40) $room_price = $order->room->forty_minutes;
                            if ($duration == 60) $room_price = $order->room->sixty_minutes;
                            if ($duration == 90) $room_price = $order->room->ninety_minutes;
                        }
                        $staff_salary = $order->user->salary ?? 0;
                        $commission_base = $room_price + $staff_salary;
                        $commission_value += ($commission->commission_percent / 100) * $commission_base;
                    }
                }
            }
        }

        // --- คำนวณ CheerCharge สำหรับ sales ---
        $price_options_sales = 0;
        if ($order->ref_seller_id && $order->addons && $order->addons->count()) {
            foreach ($order->addons as $addonItem) {
                $cheer = CheerCharge::where('ref_branch_id', $order->ref_branch_id)
                    ->where('addon_options_id', $addonItem->ref_option_id)
                    ->first();
                if ($cheer) {
                    if ($cheer->type == 'baht') {
                        $price_options_sales += $cheer->amount;
                    } elseif ($cheer->type == 'percent') {
                        $price_options_sales += ($cheer->amount / 100) * $addonItem->price;
                    }
                }
            }
        }

        // --- บันทึกค่าคอมมิชชั่นลง commissions_history ---
        $order->save();
        CommissionsHistory::updateOrCreate(
            [
                'order_id' => $order->id,
                'user_message_id' => $order->ref_user_id ?? null,
            ],
            [
                'commission_massage_amount' => $commission_value,
                'price_options_massage' => $commission_options_value,
                'user_sales_id' => $order->ref_seller_id ?? null,
                'price_options_sales' => $price_options_sales,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตวิธีการชำระเงินและค่าคอมมิชชั่นเรียบร้อยแล้ว',
            'payment_method' => $order->payment_method,
            'massage_commission' => $commission_value,
            'options_commission' => $commission_options_value,
            'sales_cheer_charge' => $price_options_sales
        ]);
    }
    
    public function destroy($id)
    {
        try {
            Order::destroy($id);
            DB::commit();
            return true;
        } catch (QueryException $err) {
            DB::rollBack();
        }
        //
    }
}
