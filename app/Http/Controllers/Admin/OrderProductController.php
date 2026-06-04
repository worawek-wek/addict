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
use App\Models\HistoryStock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderProductController extends Controller
{
    private const BUSINESS_DAY_START = '10:00';
    private const BUSINESS_DAY_END = '04:01';

    private function currentBusinessDayRange(): array
    {
        $now = Carbon::now();
        $endToday = $now->copy()->setTime(4, 1, 59);

        if ($now->lessThanOrEqualTo($endToday)) {
            return [
                $now->copy()->subDay()->setTime(10, 0, 0),
                $endToday,
            ];
        }

        return [
            $now->copy()->setTime(10, 0, 0),
            $now->copy()->addDay()->setTime(4, 1, 59),
        ];
    }

    private function isOrderInCurrentBusinessDay(Order $order): bool
    {
        if ((int) $order->payment_status === 0) {
            return true;
        }

        $orderDateTime = $order->paid_at
            ? Carbon::parse($order->paid_at)
            : (($order->booking_date && $order->start_time)
                ? Carbon::parse($order->booking_date . ' ' . $order->start_time)
                : null);

        if (!$orderDateTime) {
            return false;
        }

        [$start, $end] = $this->currentBusinessDayRange();

        return $orderDateTime->between($start, $end, true);
    }

    private function rejectIfOrderIsLocked(Order $order)
    {
        if ($this->isOrderInCurrentBusinessDay($order)) {
            return null;
        }

        return response()->json([
            'success' => false,
            'message' => 'ไม่สามารถจัดการคำสั่งซื้อของวันก่อนหน้าได้',
        ], 423);
    }

    private function productDateRangeFromRequest(): array
    {
        $startDate = Carbon::createFromFormat('d/m/Y', request('start_date'))->startOfDay();
        $endDate = Carbon::createFromFormat('d/m/Y', request('end_date') ?: request('start_date'))->startOfDay();

        [$sh, $sm] = explode(':', request('start_time_filter', self::BUSINESS_DAY_START));
        [$eh, $em] = explode(':', request('end_time_filter', self::BUSINESS_DAY_END));

        $startDate->setTime((int) $sh, (int) $sm, 0);
        $endDate->setTime((int) $eh, (int) $em, 59);

        if ($endDate->lessThanOrEqualTo($startDate)) {
            $endDate->addDay();
        }

        return [$startDate, $endDate];
    }

    public function index()
    {
        // โหลดหน้าแรกพร้อมข้อมูลเริ่มต้น
        $limit = request()->limit ?? 10;
        // $orderProducts = $this->getOrderProducts($limit);
        $user = Auth::user(); // user ที่ login อยู่

        // if ($user->work_status == 3) {
            // super admin เห็นทุกสาขา
            $branches = Branch::orderBy('name')->get();
        // } else {
        //     // เห็นเฉพาะสาขาตัวเอง
        //     $branches = Branch::where('id', $user->ref_branch_id)->get();
        // }
        
        $rounds = DailySalesClosure::orderBy('id', 'DESC')->where('ref_account_id', Auth::id())->get();

        return view('admin.order-product.index', compact('branches', 'rounds'));
    }

    public function get_history_by_round($ref_daily_sales_closure_id)
    {
        // $user = Auth::user();
        $DailySalesClosure = DailySalesClosure::find($ref_daily_sales_closure_id);
        $results = Order::orderBy('id', "DESC")->where('ref_daily_sales_closure_id', $ref_daily_sales_closure_id)->where('type', 2)->get();

        $data['DailySalesClosure'] = $DailySalesClosure;
        $data['ref_daily_sales_closure_id'] = $ref_daily_sales_closure_id;
        $data['list_data'] = $results;
        return view('admin.order-product.history', $data);
    }

    public function datatable(Request $request)
    {

        $limit = $request->limit ?? 10;
        $result = $this->getOrderProducts($limit);
        $orderProducts = $result['orderProducts'];
        $check = $result['check'];

        $user = Auth::user();

        // if ($user->work_status == 3) {
            $branches = Branch::orderBy('name')->get();
        // } else {
        //     $branches = Branch::where('id', $user->ref_branch_id)->get();
        // }
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
            // ->whereNull('ref_daily_sales_closure_id')
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
        
        [$startDate, $endDate] = $this->productDateRangeFromRequest();
        // return date('d/m/y H:i:s', strtotime($startDate));
        $query->where(function ($q) use ($startDate, $endDate) {
            $range = [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ];

            $q->where('payment_status', 0)
                ->orWhereRaw(
                    "COALESCE(paid_at, CONCAT(booking_date, ' ', start_time)) BETWEEN ? AND ?",
                    $range
                );
        });

        $check = clone $query;

        $check = $check->where('payment_status', 1)->count();
        $orderProducts = $query->paginate($limit);

        // กำหนด badge และ label
        $nowCarbon = Carbon::now();
        $nowCarbon = Carbon::now();
        foreach ($orderProducts as $order) {
            $startDateTime = Carbon::parse($order->booking_date . ' ' . $order->start_time);
            $endDateTime   = Carbon::parse($order->booking_date . ' ' . $order->end_time);
            $order->can_manage = $this->isOrderInCurrentBusinessDay($order);

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
        if ($locked = $this->rejectIfOrderIsLocked($order)) {
            return $locked;
        }

        $isCancelling = (int) $request->ref_status_id === 4 && (int) $order->ref_status_id !== 4;

        $order->payment_status = $request->status_id;
        $order->ref_status_id = $request->ref_status_id;
        if ((int) $request->status_id === 1 && !$order->paid_at) {
            $order->paid_at = now();
        }
        $order->save();

        
        if ($isCancelling) {
            foreach ($order->products as $product) {

            // ดึง สินค้า ก่อน เพิ่มสต็อก {
                $old_product = Product::find($product->ref_product_id); // ดึง สินค้า ก่อน เพิ่มสต็อก
                $main_stock_remain = $old_product->total_remain ?? 0;
                $ready_for_sale_remain = $old_product->ready_for_sale_total_remain ?? 0;
            // ดึง สินค้า ก่อน เพิ่มสต็อก }
            
                StockReadyForSale::where('ref_product_id', $product->ref_product_id)
                    ->orderByDesc('id')
                    ->limit(1)
                    ->increment('remain', $product->quantity);

            // ดึง สินค้า หลัง เพิ่มสต็อก {
                $new_product = Product::find($product->ref_product_id);
                $new_main_stock_remain = $new_product->total_remain ?? 0;
                $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
            // ดึง สินค้า หลัง เพิ่มสต็อก }

            // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> คืนสต็อกขาย {
                $history_stock = new HistoryStock;
                $history_stock->ref_product_id = $product->ref_product_id; // id สินค้า
                $history_stock->quantity = $product->quantity; // จำนวนที่เคลื่อนไหว
                $history_stock->stock_before_quantity = $main_stock_remain; // จำนวน ก่อน ตัดสต็อก
                $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->stock_ready_for_sale_before_quantity = $ready_for_sale_remain; // จำนวน ก่อน ตัดสต็อก
                $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->quantity_type = 1; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
                $history_stock->withdraw_quantity = 0;
                $history_stock->save();
            // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> คืนสต็อกขาย }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
            'status'  => $order->status->name
        ]);
    }
    public function pdf(Request $request, $daily_sales_closure_id = null)
    {
        // $closures = DailySalesClosure::orderBy("id", "DESC")->where('ref_account_id', Auth::id())->take(1)->get();
        // $DailySalesClosure = $closures[0] ?? null;
        // $DailySalesClosure_before = $closures[1] ?? null;

        // if (@$DailySalesClosure_before) {
        //     $date_before = date('d/m/Y H:i:s', strtotime($DailySalesClosure_before->date_time));
        // } else {
        //     $date_before = date('d/m/Y', strtotime($DailySalesClosure->date_time)) . " 00:00:00";
        // }
            
        [$startDate, $endDate] = $this->productDateRangeFromRequest();

        $product_employee = OrderHasProduct::join('products', 'order_has_products.ref_product_id', '=', 'products.id')
            ->leftJoin('product_type', 'products.type_id', '=', 'product_type.id') // Join เพื่อดึงชื่อประเภท
            ->whereHas('order', function ($query) use ($daily_sales_closure_id, $startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereRaw(
                        "COALESCE(paid_at, CONCAT(booking_date, ' ', start_time)) BETWEEN ? AND ?",
                        [
                            $startDate->format('Y-m-d H:i:s'),
                            $endDate->format('Y-m-d H:i:s')
                        ]
                    );
                })
                    // ->where('ref_daily_sales_closure_id', $daily_sales_closure_id)
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
            ->whereHas('order', function ($query) use ($daily_sales_closure_id, $startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereRaw(
                        "COALESCE(paid_at, CONCAT(booking_date, ' ', start_time)) BETWEEN ? AND ?",
                        [
                            $startDate->format('Y-m-d H:i:s'),
                            $endDate->format('Y-m-d H:i:s')
                        ]
                    );
                })
                    // ->where('ref_daily_sales_closure_id', $daily_sales_closure_id)
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

        $payment_channel = Order::where('orders.payment_status', 1)
            ->where('orders.type', 2)
            ->where('orders.ref_account_id', Auth::id())
            ->groupBy('orders.payment_method')
            ->whereNotNull("orders.payment_method")
            ->join(
                'order_has_products',
                'orders.id',
                '=',
                'order_has_products.ref_order_id'
            )->where(function ($q) use ($startDate, $endDate) {
                $q->whereRaw(
                    "COALESCE(orders.paid_at, CONCAT(orders.booking_date, ' ', orders.start_time)) BETWEEN ? AND ?",
                    [
                        $startDate->format('Y-m-d H:i:s'),
                        $endDate->format('Y-m-d H:i:s')
                    ]
                );
            })
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
        // $data['DailySalesClosure_before'] = $DailySalesClosure_before;
        // $data['date_before'] = $date_before;

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
        $request->validate([
            'payment_channel' => 'required|in:cash,credit_card,alipay,qr_code',
        ]);

        $order = Order::findOrFail($id);
        if ($locked = $this->rejectIfOrderIsLocked($order)) {
            return $locked;
        }

        $order->payment_status = 1;
        $order->payment_method = $request->payment_channel;
        $order->paid_at = now();
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
        if (!$this->isOrderInCurrentBusinessDay($order)) {
            abort(403, 'ไม่สามารถจัดการคำสั่งซื้อของวันก่อนหน้าได้');
        }

        $existingQtyByProduct = $order->products
            ->groupBy('ref_product_id')
            ->map(fn($items) => $items->sum('quantity'));

        $products = Product::where(function ($query) use ($order) {
                $query->where('ref_branch_id', $order->ref_branch_id)
                    ->orWhereNull('ref_branch_id');
            })
            ->where('ref_status_id', 1)
            ->orderBy('name')
            ->get()
            ->map(function ($p) use ($existingQtyByProduct) {
                $currentStock = StockReadyForSale::where('ref_product_id', $p->id)->sum('remain');
                $p->stock = $currentStock + (int) ($existingQtyByProduct[$p->id] ?? 0);
                return $p;
            });
        return view('admin.order-product.edit', compact('order', 'products'));
    }

    public function updateProducts(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($locked = $this->rejectIfOrderIsLocked($order)) {
            return $locked;
        }

        $request->validate([
            'payment_method' => 'nullable|in:cash,credit_card,alipay,qr_code',
        ]);

        try {
            $updated_by = Auth::id();
            DB::beginTransaction();
            $discount = $request->input('discount', 0);
            $payment_method = $request->input('payment_method', null);

            //get items from request
            $items = $request->input('items', []);

            // Restore stock for all existing order items before deleting them
            foreach ($order->products as $oldItem) {

            // ดึง สินค้า ก่อน เพิ่มสต็อก {
                $old_product = Product::find($oldItem->ref_product_id); // ดึง สินค้า ก่อน เพิ่มสต็อก
                $main_stock_remain = $old_product->total_remain ?? 0;
                $ready_for_sale_remain = $old_product->ready_for_sale_total_remain ?? 0;
            // ดึง สินค้า ก่อน เพิ่มสต็อก }

                StockReadyForSale::where('ref_product_id', $oldItem->ref_product_id)
                                    ->orderByDesc('id')
                                    ->limit(1)
                                    ->increment('remain', $oldItem->quantity);

            // ดึง สินค้า หลัง เพิ่มสต็อก {
                $new_product = Product::find($oldItem->ref_product_id);
                $new_main_stock_remain = $new_product->total_remain ?? 0;
                $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
            // ดึง สินค้า หลัง เพิ่มสต็อก }

            // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> คืนสต็อกขาย {
                $history_stock = new HistoryStock;
                $history_stock->ref_product_id = $oldItem->ref_product_id; // id สินค้า
                $history_stock->quantity = 0-$oldItem->quantity; // จำนวนที่เคลื่อนไหว
                $history_stock->stock_before_quantity = $new_main_stock_remain; // จำนวน ก่อน ตัดสต็อก
                $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->stock_ready_for_sale_before_quantity = $ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->quantity_type = 0; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
                $history_stock->withdraw_quantity = 0;
                $history_stock->save();
            // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> คืนสต็อกขาย }

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
                    
                // ดึง สินค้า ก่อน เพิ่มสต็อก {
                    $old_product = Product::find($product->id); // ดึง สินค้า ก่อน เพิ่มสต็อก
                    $main_stock_remain = $old_product->total_remain ?? 0;
                    $ready_for_sale_remain = $old_product->ready_for_sale_total_remain ?? 0;
                // ดึง สินค้า ก่อน เพิ่มสต็อก }

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

                // ดึง สินค้า หลัง เพิ่มสต็อก {
                    $new_product = Product::find($product->id);
                    $new_main_stock_remain = $new_product->total_remain ?? 0;
                    $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
                // ดึง สินค้า หลัง เพิ่มสต็อก }

                // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> คืนสต็อกขาย {
                    $history_stock = new HistoryStock;
                    $history_stock->ref_product_id = $product->id; // id สินค้า
                    $history_stock->quantity = $quantity; // จำนวนที่เคลื่อนไหว
                    $history_stock->stock_before_quantity = $new_main_stock_remain; // จำนวน ก่อน ตัดสต็อก
                    $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
                    $history_stock->stock_ready_for_sale_before_quantity = $ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                    $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                    $history_stock->quantity_type = 0; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
                    $history_stock->withdraw_quantity = 0;
                    $history_stock->save();
                // เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> คืนสต็อกขาย }

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
                if (!$order->paid_at) {
                    $order->paid_at = now();
                }
            } else {
                $order->payment_status = 0;
                $order->payment_method = null;
                $order->paid_at = null;
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
        if ($locked = $this->rejectIfOrderIsLocked($order)) {
            return $locked;
        }

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
            'payment_method' => 'nullable|in:cash,credit_card,alipay,qr_code',
        ]);

        $order = Order::findOrFail($id);
        if ($locked = $this->rejectIfOrderIsLocked($order)) {
            return $locked;
        }

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
