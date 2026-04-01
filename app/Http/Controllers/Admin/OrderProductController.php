<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CheerCharge;
use App\Models\DailySalesClosure;
use App\Models\CommissionsHistory;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderHasProduct;
use App\Models\Product;
use App\Models\StockReadyForSale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderProductController extends Controller
{
    public function index()
    {
        // โหลดหน้าแรกพร้อมข้อมูลเริ่มต้น
        $limit = request()->limit ?? 10;
        $orderProducts = $this->getOrderProducts($limit);
        $user = Auth::user(); // user ที่ login อยู่

        if ($user->work_status == 3) {
            // super admin เห็นทุกสาขา
            $branches = Branch::orderBy('name')->get();
        } else {
            // เห็นเฉพาะสาขาตัวเอง
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-product.index', compact('orderProducts', 'branches'));
    }

    public function datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $orderProducts = $this->getOrderProducts($limit)['orderProducts'];
        $check = $this->getOrderProducts($limit)['check'];

        $user = Auth::user();

        if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        } else {
            $branches = Branch::where('id', $user->ref_branch_id)->get();
        }
        return view('admin.order-product.datatable', compact('orderProducts', 'branches', 'check'));
    }

    private function getOrderProducts($limit)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $query = Order::with(['branch', 'customer', 'user', 'room', 'status'])
            ->where('ref_account_id', Auth::id())
            ->where('type', 2)
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

        $check = clone $query;

        $check = $check->where('payment_status', 1)->count();
        $orderProducts = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderProducts as $order) {
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

        return ['check' => $check, 'orderProducts' => $orderProducts];
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

        if ($request->ref_status_id == 4) {
            foreach ($order->products as $product) {
                StockReadyForSale::where('ref_product_id', $product->ref_product_id)
                    ->orderByDesc('id')
                    ->limit(1)
                    ->increment('remain', $product->quantity);
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

        if (@$DailySalesClosure_before) {
            $date_before = date('d/m/Y H:i:s', strtotime($DailySalesClosure_before->date_time));
        } else {
            $date_before = date('d/m/Y', strtotime($DailySalesClosure->date_time)) . " 00:00:00";
        }

        $product_employee = OrderHasProduct::join('products', 'order_has_products.ref_product_id', '=', 'products.id')
            ->leftJoin('product_type', 'products.type_id', '=', 'product_type.id') // Join เพื่อดึงชื่อประเภท
            ->whereHas('order', function ($query) use ($DailySalesClosure) {
                $query->whereNull('ref_daily_sales_closure_id')
                    // ->where('ref_daily_sales_closure_id', $DailySalesClosure->id)
                    ->where('customer_type', 1)
                    ->where('payment_status', 1)
                    ->where('type', 2)
                    ->where('ref_account_id', Auth::id());
            })
            ->groupBy('products.type_id', 'product_type.name')
            ->select(
                'products.type_id',
                'product_type.name as type_name',
                DB::raw('SUM(order_has_products.quantity) as total_qty'),
                DB::raw('SUM(order_has_products.price * order_has_products.quantity) as total_price'),
                DB::raw('SUM(order_has_products.cost * order_has_products.quantity) as total_cost')
            );
        $data['product_employee'] = $product_employee->get();

        $product_customer = OrderHasProduct::join('products', 'order_has_products.ref_product_id', '=', 'products.id')
            ->leftJoin('product_type', 'products.type_id', '=', 'product_type.id')
            ->whereHas('order', function ($query) use ($DailySalesClosure) {
                $query->whereNull('ref_daily_sales_closure_id')
                    // ->where('ref_daily_sales_closure_id', $DailySalesClosure->id)
                    ->where('customer_type', 2)
                    ->where('payment_status', 1)
                    ->where('type', 2)
                    ->where('ref_account_id', Auth::id());
            })
            ->groupBy('products.type_id', 'product_type.name')
            ->select(
                'products.type_id',
                'product_type.name as type_name',
                DB::raw('SUM(order_has_products.quantity) as total_qty'),
                DB::raw('SUM(order_has_products.price * order_has_products.quantity) as total_price'),
                DB::raw('SUM(order_has_products.cost * order_has_products.quantity) as total_cost')
            );
        $data['product_customer'] = $product_customer->get();

        $payment_channel = Order::whereNull('ref_daily_sales_closure_id')
            ->where('orders.payment_status', 1)
            ->where('orders.ref_account_id', Auth::id())
            ->groupBy('orders.payment_method')
            ->whereNotNull("orders.payment_method")
            ->join(
                'order_has_products',
                'orders.id',
                '=',
                'order_has_products.ref_order_id'
            )
            ->select(
                'orders.payment_method',
                DB::raw('SUM(order_has_products.price * order_has_products.quantity) as total_price')
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

        return view('admin.order-product.pdf', $data);
    }

    public function show($id)
    {
        $orderProduct = Order::with(['branch', 'room', 'status', 'addons.option', 'customer', 'user'])
            ->findOrFail($id);

        $statusId   = $orderProduct->status->id ?? null;
        $statusName = $orderProduct->status->name ?? 'ไม่ระบุ';

        $startDateTime = Carbon::parse($orderProduct->booking_date . ' ' . $orderProduct->start_time);
        $endDateTime   = Carbon::parse($orderProduct->booking_date . ' ' . $orderProduct->end_time);
        $now           = Carbon::now();

        $isOngoing  = $now->between($startDateTime, $endDateTime);
        $isOvertime = $now->greaterThan($endDateTime);

        if (!empty($orderProduct->payment_method)) {
            $orderProduct->badge_class = 'bg-info';
            $orderProduct->status_label = $orderProduct->payment_method;
        } elseif ($statusId === 2 || $isOngoing) {
            $orderProduct->badge_class = 'bg-success';
            $orderProduct->status_label = $statusName;
        } elseif ($isOvertime) {
            $orderProduct->badge_class = 'bg-danger';
            $orderProduct->status_label = 'เกินเวลา';
        } elseif (strtolower($statusName) === 'pending') {
            $orderProduct->badge_class = 'bg-warning';
            $orderProduct->status_label = $statusName;
        } elseif ($statusName === 'ยกเลิก') {
            $orderProduct->badge_class = 'bg-danger';
            $orderProduct->status_label = $statusName;
        } else {
            $orderProduct->badge_class = 'bg-secondary';
            $orderProduct->status_label = $statusName;
        }
        $statuses = OrderStatus::all();

        return view('admin.order-product.view', compact('orderProduct', 'statuses'));
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
            ->whereIn('payment_status', [1, 3])
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


    public function printSlip($id)
    {
        $order = Order::with(['branch', 'products.product', 'seller'])->findOrFail($id);
        return view('admin.order-product.slip', compact('order'));
    }

    public function edit(Request $request, $id)
    {
        $order = Order::with(['branch', 'products.product', 'seller'])->findOrFail($id);
        $products = Product::where('ref_branch_id', $order->ref_branch_id)
            ->orWhereNull('ref_branch_id')
            ->get()
            ->map(function ($p) {
                $p->stock = StockReadyForSale::where('ref_product_id', $p->id)->sum('remain');
                return $p;
            });
        return view('admin.order-product.edit', compact('order', 'products'));
    }

    public function updateProducts(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $updated_by = Auth::id();
            $order = Order::find($id);
            $discount = $request->input('discount', 0);
            $payment_method = $request->input('payment_method', null);

            //get items from request
            $items = $request->input('items', []);

            // Restore stock for all existing order items before deleting them
            foreach ($order->products as $oldItem) {
                StockReadyForSale::where('ref_product_id', $oldItem->ref_product_id)
                    ->orderByDesc('id')
                    ->limit(1)
                    ->increment('remain', $oldItem->quantity);
            }

            //clear old items
            $order->products()->delete();

            $order->discount        = $discount;
            $order->payment_method  = $request->input('payment_method') ?: null;
            $order->total_price     = 0;
            $order->updated_by      = $updated_by;

            //add new items and decrement stock
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $price    = isset($item['price']) ? floatval($item['price']) : $product->price;
                    $quantity = $item['qty'] ?? $item['quantity'] ?? 1;
                    $totalPrice = $price * $quantity;
                    OrderHasProduct::create([
                        'ref_order_id'   => $order->id,
                        'ref_product_id' => $product->id,
                        'quantity'       => $quantity,
                        'price'          => $price,
                        'total_price'    => $totalPrice,
                        'cost'           => 0.00,
                    ]);
                    // Decrement stock for the newly added item
                    StockReadyForSale::where('ref_product_id', $product->id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->decrement('remain', $quantity);
                    $order->total_price += $totalPrice;
                } else {
                    throw new \Exception("ไม่พบสินค้า ID: " . $item['product_id']);
                }
            }


            // Apply discount
            $order->total_price = max(0, $order->total_price - $discount);

            $payment_status = $request->input('payment_status', null);
            if ($payment_status == 1) {
                $order->payment_status = 1;
                $order->payment_method = $payment_method ?: null;
            } else {
                $order->payment_status = 0;
                $order->payment_method = null;
            }

            $order->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'บันทึกเรียบร้อย']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    public function removeProduct(Request $request, $id, $productId)
    {
        $order = Order::findOrFail($id);
        $row = $order->products()->where('ref_product_id', $productId)->first();
        if ($row) {
            StockReadyForSale::where('ref_product_id', $productId)
                ->orderByDesc('id')->limit(1)->increment('remain', $row->quantity);
            $row->delete();
            $total = $order->products()->sum(DB::raw('price * quantity'));
            $order->total_price = $total;
            $order->save();
        }
        return response()->json(['success' => true]);
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
}
