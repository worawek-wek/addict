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
use App\Models\ProductType;
use App\Models\RoomType;
use App\Models\StockReadyForSale;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRoomController extends Controller
{
    public function index()
    {
        $getchild = Order::join('users', 'orders.ref_user_id', '=', 'users.id')
            ->where('orders.type', 1)
            ->select(
                'orders.ref_user_id',
                'users.name',
            )
            ->groupBy(
                'orders.ref_user_id',
                'users.name',
            )
            ->get();

        // โหลดหน้าแรกพร้อมข้อมูลเริ่มต้น
        $limit = request()->limit ?? 10;
        // $orderRooms = $this->getOrderRooms($limit);
        $user = Auth::user(); // user ที่ login อยู่

        if ($user->work_status == 3) {
            // super admin เห็นทุกสาขา
            $branches = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาตัวเอง
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-room.index', compact('branches', 'getchild'));
    }

    public function datatable(Request $request)
    {
        $limit = $request->limit ?? 10;
        $childSelect = $request->childselect;

        $orderRooms = $this->getOrderRooms($limit, $childSelect);

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }

        return view('admin.order-room.datatable', compact('orderRooms', 'branches'));
    }
    private function getOrderRooms($limit, $childSelect = null)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::with(['branch', 'customer', 'user', 'room', 'status'])
            ->where('type', 1)
            ->whereNull('ref_daily_sales_closure_id')
            ->select('orders.*')
            ->orderBy('booking_date')
            ->orderBy('created_at', 'DESC');

        // ✅ filter เฉพาะสาขาของ user ที่ login
        $userBranchId = Auth::user()->ref_branch_id ?? null;
        if ($userBranchId) {
            $query->where('ref_branch_id', $userBranchId);
        }

        // filter สาขา (ถ้าเป็น admin อาจเลือกได้)
        if (request()->filled('branch_id')) {
            $query->where('ref_branch_id', request()->branch_id);
        }

        if (!empty($childSelect)) {
            $query->where('ref_user_id', $childSelect);
        }

        // if (request('start_date')) {
        //     $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
        //     $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
        //     if (request('start_time_filter')) {
        //         [$sh, $sm] = explode(':', request('start_time_filter'));
        //         $startDate->setTime((int)$sh, (int)$sm, 0);
        //     }
        //     if (request('end_time_filter')) {
        //         [$eh, $em] = explode(':', request('end_time_filter'));
        //         $endDate->setTime((int)$eh, (int)$em, 59);
        //     }
        //     $query->where(function ($q) use ($startDate, $endDate) {
        //                                                                 $q->whereBetween('booking_date', [$startDate, $endDate])
        //                                                                     ->orWhere('ref_status_id', 2);
        //                                                             });
        // }
        // $DailySalesClosure = DailySalesClosure::orderBy("id", "DESC")->first();

        // if (@$DailySalesClosure) {
        //     $query->where('created_at', ">", $DailySalesClosure->date_time);
        // }

        // filter ค้นหา
        if (request()->filled('search')) {
            $search = request()->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // filter by booking_date (date_range, start_date, end_date)
        $dateRange = request('date_range');
        $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
        $endDate   = Carbon::createFromFormat('d/m/Y', request('end_date'))->endOfDay();
        if ($dateRange && $dateRange !== 'custom') {
            // 1, 7, 14, 30 days
            $days = intval($dateRange);
            if ($days > 0) {
                $from = Carbon::today()->subDays($days - 1)->format('Y-m-d');
                $to = Carbon::today()->format('Y-m-d');
                $query->where(function ($q) use ($from, $to) {
                                                                        $q->whereBetween('booking_date', [$from, $to])
                                                                            ->orWhere('ref_status_id', 2);
                                                                    });
            }
        } elseif ($startDate && $endDate) {
            
            $query->where(function ($q) use ($startDate, $endDate) {
                                                                        $q->whereRaw(
                                                                            "CONCAT(booking_date, ' ', start_time) BETWEEN ? AND ?",
                                                                            [
                                                                                $startDate->format('Y-m-d H:i:s'),
                                                                                $endDate->format('Y-m-d H:i:s')
                                                                            ]
                                                                        )
                                                                        ->orWhere('ref_status_id', 2);
                                                                    });
        }
        
// http://127.0.0.1:9800/admin/order-rooms/datatable?branch_id=1&date_range=&start_date=05%2F05%2F2026&start_time_filter=10%3A00&end_date=06%2F05%2F2026&end_time_filter=04%3A01&childselect=&limit=25
// http://127.0.0.1:9800/admin/order-rooms/datatable?branch_id=1&date_range=custom&start_date=01%2F05%2F2026&start_time_filter=10%3A00&end_date=06%2F05%2F2026&end_time_filter=04%3A01&childselect=&limit=25
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
        if ($room_course) {
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

        return view('admin.order-room.view', compact('orderRoom', 'statuses', 'room_course_price'));
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

    public function getslip(Request $request, $id)
    {
        $order = Order::with([
            'branch',
            'customer',
            'user',
            'room',
            'room_type',
            'course',
            'seller',
            'status',
            'addons.option',
            'products.product',
        ])->findOrFail($id);
        $payment_method = $order->payment_method ?? 'ยังไม่ระบุวิธีชำระเงิน';

        $qr = QrCode::size(150)->generate(url("admin/order-rooms/{$order->id}"));

        // Build product list grouped by type (mirrors checkout logic)
        $grouped_products = [];
        if ($order->products && $order->products->count()) {
            foreach ($order->products as $orderProduct) {
                $product = $orderProduct->product;
                if (!$product) continue;
                $price    = $orderProduct->price;
                $qty      = $orderProduct->quantity;
                $type     = ProductType::find($product->type_id);
                $typeName = $type ? $type->name : 'อื่นๆ / ไม่ระบุประเภท';
                $grouped_products[$typeName][] = '<tr>
                    <td>' . $product->name . '</td>
                    <td class="text-center">' . $qty . '</td>
                    <td class="text-right">' . number_format($price, 2) . '</td>
                    <td class="text-right">' . number_format($price * $qty, 2) . '</td>
                </tr>';
            }
        }
        $list_product = '';
        foreach ($grouped_products as $typeName => $rows) {
            $list_product .= '<tr class="table-active" style="background-color: #f3f4f6;">
                <td colspan="4"><strong>' . $typeName . '</strong></td>
            </tr>';
            foreach ($rows as $row) {
                $list_product .= $row;
            }
        }
        if (!$order->ref_room_type_id) {
            // Product-only slip (no room type) — same as checkout non-room path
            $slip = "<!DOCTYPE html>
                <html lang='th'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>รายละเอียดการจอง</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; }
                        .invoice { width: 69mm; font-size: 11px; padding: 10px; box-sizing: border-box; }
                        .header { display: flex; justify-content: space-between; align-items: end; font-weight: bold; font-size: 10px; margin-bottom: 5px; }
                        .title { flex-grow: 1; text-align: center; font-size: 11px; }
                        .right-align { text-align: right; }
                        .info-text { margin: 2px 0; }
                        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; border-top: 1px dashed #000; border-bottom: 1px dashed #000; }
                        th, td { padding: 3px 2px; text-align: left; font-size: 11px; }
                        th { border-bottom: 1px dashed #000; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        @media print {
                            @page { size: 69mm auto; margin: 0; }
                            body { width: 69mm; margin: 0; }
                            .invoice { width: 69mm; padding: 5px; }
                        }
                    </style>
                </head>
                <body>
                    <div class='invoice'>
                        <div class='header' align='right'>
                            <span class='title'>ใบแจ้งหนี้ชั่วคราว</span>
                            <span class='right-align'>No_: " . $order->order_number . "</span>
                        </div>
                        <p class='right-align info-text'><strong>แคชเชียร์:</strong> Addict</p>
                        <p class='info-text'><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date('Y-m-d', strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>
                    <strong></strong>วิธีชำระเงิน:</strong> $payment_method<br>

                        <table>
                            <thead>
                                <tr>
                                    <th>รายการสินค้า</th>
                                    <th class='text-center'>จำนวน</th>
                                    <th class='text-right'>@ ราคา</th>
                                    <th class='text-right'>รวม</th>
                                </tr>
                            </thead>
                            <tbody>" . $list_product . "</tbody>
                        </table>
                    </div>
                </body>
                </html>";
        } else {
            // Room / massage slip with staff coupon and QR — same as checkout room path
            $slip = "<!DOCTYPE html>
                        <html lang='th'>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>รายละเอียดการจอง</title>
                            <style>
                                body { font-family: Arial, sans-serif; font-size: 11px; }
                                .invoice { width: 69mm; font-size: 11px; padding: 20px; }
                                .header { display: flex; justify-content: space-between; align-items: end; font-weight: bold; font-size: 10px; }
                                .title { flex-grow: 1; text-align: center; font-size: 11px; }
                                .right-align { text-align: right; }
                                table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; border-top: 1px solid #000; }
                                th, td { padding: 2px; text-align: left; font-size: 11px; }
                                th { border-bottom: 1px solid #000; }
                                td { border-bottom: 1px solid #000; }
                                @media print {
                                    @page { size: 69mm auto; margin: 0; }
                                    body { width: 69mm; margin: 0; }
                                    .invoice { width: 69mm; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class='invoice'>
                                <div class='header' align='right'>
                                    <span class='title'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ใบแจ้งหนี้ชั่วคราว </span>
                                    <span class='right-align'>No_: " . $order->order_number . "</span>
                                </div>
                                <p class='right-align'><strong>แคชเชียร์:</strong> Addict</p>
                                <p><strong>ห้อง:</strong> " . ($order->room->name ?? '') . "</p>
                                <p><strong>เปิดห้อง:</strong> " . \Carbon\Carbon::parse(date('Y-m-d', strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i') . "</p>
                                <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date('Y-m-d', strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>
                    <strong></strong>วิธีชำระเงิน:</strong> $payment_method<br>

                                <table>
                                    <tr><th>จำนวน</th><th>รายการสินค้า</th><th>@ ราคา</th><th>รวม</th></tr>
                                    <tr>
                                        <td>1</td>
                                        <td>" . ($order->user->nickname ?? '') . " + " . ($order->course->name ?? '') . " " . ($order->room_type->name ?? '') . "</td>
                                        <td>" . $order->total_price . "</td>
                                        <td>" . $order->total_price . "</td>
                                    </tr>
                                    <tr>
                                        <td colspan='3' style='border-top:unset;padding:10px'> ผู้ดูแล " . ($order->seller->user_id ?? '') . " " . ($order->seller->nickname ?? '') . " </td>
                                    </tr>
                                </table>
                            </div>
                            <div style='page-break-before: always;'></div>
                            <div class='invoice'>
                                <div class='header' align='right'>
                                    <span class='title'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ใบคูปองพนักงาน </span>
                                    <span class='right-align'>No_: " . $order->order_number . "</span>
                                </div>
                                <p class='right-align'><strong>แคชเชียร์:</strong> Addict</p>
                                <p><strong>ห้อง:</strong> " . ($order->room->name ?? '') . "</p>
                                <p><strong>เปิดห้อง:</strong> " . \Carbon\Carbon::parse(date('Y-m-d', strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i') . "</p>
                                <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date('Y-m-d', strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>
                                <p><strong>วิธีชำระเงิน:</strong> $payment_method</p>
                                <table>
                                    <tr><th>รหัส</th><th>ชื่อพนักงาน</th><th>ชั่วโมงรวม</th></tr>
                                    <tr>
                                        <td style='border:unset;padding-top:5px'>" . ($order->user->user_id ?? '') . "</td>
                                        <td style='border:unset;padding-top:5px'>" . ($order->user->nickname ?? '') . " + " . ($order->course->name ?? '') . " " . ($order->room_type->name ?? '') . "</td>
                                        <td style='border:unset;padding-top:5px'>" . (isset($order->course->minute) ? floor($order->course->minute / 60) : '') . "</td>
                                    </tr>
                                    <tr>
                                        <td colspan='3' style='border-top:unset;padding:10px'> ผู้ดูแล " . ($order->seller->user_id ?? '') . " " . ($order->seller->nickname ?? '') . " </td>
                                    </tr>
                                </table>
                                <span style='padding-top:10px'>ให้เก็บไว้ตรวจสอบ</span>
                            </div>
                            <div style='page-break-before: always;'></div>
                            <div style='padding: 10px;'>
                                {$qr}
                            </div>
                        </body>
                    </html>";
        }
        if ($order->products && $order->products->count() > 0) {
            $productList = function () use ($order) {
                $list = '';
                foreach ($order->products as $orderProduct) {
                    $product = $orderProduct->product;
                    if (!$product) continue;
                    $price    = $orderProduct->price;
                    $qty      = $orderProduct->quantity;
                    $list .= '<tr>
                        <td>' . $product->name . '</td>
                        <td class="text-center">' . $qty . '</td>
                        <td class="text-right">' . number_format($price, 2) . '</td>
                        <td class="text-right">' . number_format($price * $qty, 2) . '</td>
                    </tr>';
                }
                return $list;
            };
            $slip .= "<div style='page-break-before: always;'></div>
                <div class='invoice'>
                    <div class='header' align='right'>
                        <span class='title'>รายการสินค้า</span>
                        <span class='right-align'>No_: " . $order->order_number . "</span>

                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>รายการสินค้า</th>
                                <th class='text-center'>จำนวน</th>
                                <th class='text-right'>@ ราคา</th>
                                <th class='text-right'>รวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            " . $productList() . "
                        </tbody>
                    </table>
                    <div style='padding-top:10px;'>
                    <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>
                    <strong></strong>วิธีชำระเงิน:</strong> $payment_method<br>

                    </div>
                </div>";
        }


        return response()->json([
            'status' => true,
            'data'   => $slip,
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
                case 'forty_minutes':
                    $duration = 40;
                    break;
                case 'sixty_minutes':
                    $duration = 60;
                    break;
                case 'ninety_minutes':
                    $duration = 90;
                    break;
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
