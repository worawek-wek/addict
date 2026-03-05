<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CheerCharge;
use App\Models\DailySalesClosure;
use App\Models\CommissionsHistory;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderHasDrink;
use App\Models\StockReadyForSale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDrinkController extends Controller
{
    public function index()
    {
        // โหลดหน้าแรกพร้อมข้อมูลเริ่มต้น
        $limit = request()->limit ?? 10;
        $orderDrinks = $this->getOrderDrinks($limit);
        $user = Auth::user(); // user ที่ login อยู่

        if ($user->work_status == 3) {
            // super admin เห็นทุกสาขา
            $branches = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาตัวเอง
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-drink.index', compact('orderDrinks', 'branches'));
    }

    public function datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $orderDrinks = $this->getOrderDrinks($limit);

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-drink.datatable', compact('orderDrinks', 'branches'));
    }

    private function getOrderDrinks($limit)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::with(['branch', 'customer', 'user', 'room', 'status'])
                        ->where('ref_account_id', Auth::id())
                        ->where('type', 3)
                        ->select('orders.*')
                        ->orderByRaw("
                                        CASE
                                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) <= '{$now}' AND CONCAT(booking_date, ' ', end_time) >= '{$now}' AND (payment_method IS NULL OR payment_method = '') THEN 1 -- จอง (ถึงเวลาแล้ว) ที่ยังไม่มี payment_method
                                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', start_time) > '{$now}' THEN 2 -- จอง
                                            WHEN ref_status_id = 1 AND CONCAT(booking_date, ' ', end_time) < '{$now}' THEN 3 -- จอง (เกินเวลา)
                                            WHEN ref_status_id = 2 THEN 4 -- อยู่ระหว่างใช้บริการ
                                            WHEN ref_status_id = 3 THEN 5 -- ใช้บริการเสร็จสิ้น
                                            WHEN payment_method IS NOT NULL AND payment_method != '' THEN 6 -- payment_method มีข้อมูลอยู่ก่อนสถานะยกเลิก
                                            WHEN ref_status_id = 4 THEN 7 -- ยกเลิก
                                            ELSE 8 -- ไม่ระบุ
                                        END
                                    ")
                        ->orderBy('id', "DESC")
                        ->whereNull('ref_daily_sales_closure_id')
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
        // $DailySalesClosure = DailySalesClosure::orderBy("id","DESC")->where('ref_account_id', Auth::id())->first(); //////////////////////////

        // if (@$DailySalesClosure) { //////////////////////////
        //     $query->where('created_at', ">" , $DailySalesClosure->date_time); //////////////////////////
        // } //////////////////////////

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->where('order_number', 'like', "%{$search}%");
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

        $orderDrinks = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderDrinks as $order) {
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

        return $orderDrinks;
    }


    public function updateStatus(Request $request, $id)
    {
        // return 123;
        $request->validate([
            'status_id' => 'required|exists:order_status,id'
        ]);

        $order = Order::findOrFail($id);
        $order->payment_status = $request->status_id;
        $order->ref_status_id = $request->ref_status_id;
        $order->save();

        if($request->ref_status_id == 4){
            foreach($order->drinks as $drink){
                StockReadyForSale::where('ref_drink_id', $drink->ref_drink_id)
                                    ->orderByDesc('id')
                                    ->limit(1)
                                    ->increment('qty', $drink->quantity);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
            'status'  => $order->status->name
        ]);
    }
    public function pdf()
    {
        $closures = DailySalesClosure::orderBy("id", "DESC")->where('ref_account_id', Auth::id())->take(1)->get();
        $DailySalesClosure = $closures[0] ?? null;
        $DailySalesClosure_before = $closures[1] ?? null;

        if(@$DailySalesClosure_before){
            $date_before = date('d/m/Y H:i:s', strtotime($DailySalesClosure_before->date_time));
        }else{
            $date_before = date('d/m/Y', strtotime($DailySalesClosure->date_time))." 00:00:00";
        }

        $drink_emplaoy = OrderHasDrink::whereHas('order', function ($query) use ($DailySalesClosure) {
                                    $query->where('ref_daily_sales_closure_id', $DailySalesClosure->id)
                                            ->where('customer_type', 1)
                                            ->where('ref_account_id', Auth::id());
                                })
                                ->groupBy('ref_drink_id')
                                ->select(
                                    'ref_drink_id',
                                    DB::raw('SUM(quantity) as total_qty'),
                                    DB::raw('SUM(price * quantity) as total_price')
                                );
        $data['drink_employee'] = $drink_emplaoy->get();

        $drink_customer = OrderHasDrink::whereHas('order', function ($query) use ($DailySalesClosure) {
                                    $query->where('ref_daily_sales_closure_id', $DailySalesClosure->id)
                                    ->where('customer_type', 2)
                                    ->where('ref_account_id', Auth::id());
                                })
                                ->groupBy('ref_drink_id')
                                ->select(
                                    'ref_drink_id',
                                    DB::raw('SUM(quantity) as total_qty'),
                                    DB::raw('SUM(price * quantity) as total_price')
                                );
        $data['drink_customer'] = $drink_customer->get();

        $payment_channel = Order::where('orders.ref_daily_sales_closure_id',  $DailySalesClosure->id)
                                ->where('orders.ref_account_id', Auth::id())
                                ->groupBy('orders.payment_method')
                                ->whereNotNull("orders.payment_method")
                                ->join(
                                    'order_has_drinks',
                                    'orders.id',
                                    '=',
                                    'order_has_drinks.ref_order_id'
                                )
                                ->select(
                                    'orders.payment_method',
                                    DB::raw('SUM(order_has_drinks.price * order_has_drinks.quantity) as total_price')
                                );
        $data['payment_channel'] = $payment_channel->get();
            // ->orderBy('booking_date')
            // ->orderBy('start_time');

        // // ✅ filter เฉพาะสาขาของ user ที่ login
        // $userBranchId = Auth::user()->ref_branch_id ?? null;
        // if ($userBranchId) {
        //     $query->where('ref_branch_id', $userBranchId);
        // }

        // // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        // if (request()->filled('branch_id')) {
        //     $query->where('ref_branch_id', request()->branch_id);
        // }

        // if (@$DailySalesClosure) {
        //     $query->where('ref_daily_sales_closure_id', $DailySalesClosure->id);
        // }
            
        $data['total_price'] = 0;
        $data['DailySalesClosure_before'] = $DailySalesClosure_before;
        $data['date_before'] = $date_before;

        return view('admin.order-drink.pdf', $data);
    }

    public function show($id)
    {
        $orderDrink = Order::with(['branch', 'room', 'status', 'addons.option', 'customer', 'user'])
            ->findOrFail($id);

        $statusId   = $orderDrink->status->id ?? null;
        $statusName = $orderDrink->status->name ?? 'ไม่ระบุ';

        $startDateTime = Carbon::parse($orderDrink->booking_date . ' ' . $orderDrink->start_time);
        $endDateTime   = Carbon::parse($orderDrink->booking_date . ' ' . $orderDrink->end_time);
        $now           = Carbon::now();

        $isOngoing  = $now->between($startDateTime, $endDateTime);
        $isOvertime = $now->greaterThan($endDateTime);

        if (!empty($orderDrink->payment_method)) {
            $orderDrink->badge_class = 'bg-info';
            $orderDrink->status_label = $orderDrink->payment_method;
        } elseif ($statusId === 2 || $isOngoing) {
            $orderDrink->badge_class = 'bg-success';
            $orderDrink->status_label = $statusName;
        } elseif ($isOvertime) {
            $orderDrink->badge_class = 'bg-danger';
            $orderDrink->status_label = 'เกินเวลา';
        } elseif (strtolower($statusName) === 'pending') {
            $orderDrink->badge_class = 'bg-warning';
            $orderDrink->status_label = $statusName;
        } elseif ($statusName === 'ยกเลิก') {
            $orderDrink->badge_class = 'bg-danger';
            $orderDrink->status_label = $statusName;
        } else {
            $orderDrink->badge_class = 'bg-secondary';
            $orderDrink->status_label = $statusName;
        }
        $statuses = OrderStatus::all();

        return view('admin.order-drink.view', compact('orderDrink', 'statuses'));
    }
    public function closures()
    {

        $dsc_insert = new DailySalesClosure;
        $dsc_insert->ref_account_id = Auth::id();
        $dsc_insert->save();

        Order::where('ref_daily_sales_closure_id')
                ->where('ref_account_id', Auth::id())
                ->where('type', 2)
                ->whereNull('ref_daily_sales_closure_id')
                ->where('payment_status', 1)
                ->update(["ref_daily_sales_closure_id" => $dsc_insert->id]);        

        return response()->json([
            'success' => true,
            'message' => 'ปิดการขายเรียบร้อยแล้ว',
            'status'  => 'ชำระเงิน'
        ]);
    }
    public function confirmPayment(Request $request, $id)
    {
        // return $request;
        $order = Order::findOrFail($id);
        $order->payment_status = 1;
        $order->payment_method = $request->payment_channel;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'คอนเฟิร์มชำระเงินเรียบร้อยแล้ว',
            'status'  => 'ชำระเงิน'
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
}
