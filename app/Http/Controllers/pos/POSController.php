<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\AddonOption;
use App\Models\CardStocks;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderHasAddonOption;
use App\Models\OrderHasProduct;
use App\Models\Product;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // ---------------- Products + Search ----------------
        $q = trim((string) $request->get('q', ''));

        $products = Product::with('latestStock')
            ->when($q !== '', fn($b) => $b->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->get();

        if ($request->ajax() || $request->boolean('ajax')) {
            return view('pos.partials.product-grid', compact('products'))->render();
        }

        // ---------------- Cart Totals ----------------
        $cart = Session::get('cart', []);
        $subtotal = collect($cart)->sum(fn($i) => (float)($i['price'] ?? 0) * (int)($i['qty'] ?? 0));
        $discount = 0;
        $tax = 0;
        $total = $subtotal - $discount + $tax;

        // ---------------- Rooms (only by branch) ----------------
        $branchId = Auth::user()->ref_branch_id ?? null;

        $rooms = Room::query()
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->orderBy('id')
            ->get();

        $roomGroups = $this->groupRoomsForModal($rooms);

        $storefrontName = 'Cashier';
        $addonOptions = AddonOption::orderBy('name')->get();

        return view('pos.index', compact(
            'products',
            'cart',
            'subtotal',
            'discount',
            'tax',
            'total',
            'roomGroups',
            'storefrontName',
            'addonOptions'
        ));
    }



    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'qty'   => 1,
                'image' => $product->image ?? 'https://via.placeholder.com/150',
            ];
        }

        Session::put('cart', $cart);

        return redirect()->route('pos.index');
    }
    protected function groupRoomsForModal($rooms): array
    {
        $out = [
            [
                'name'  => 'Rooms',
                'rooms' => [],
            ]
        ];

        foreach ($rooms as $r) {
            $out[0]['rooms'][] = [
                'id'    => (int) $r->id,
                'label' => $r->name,   // ใช้ชื่อห้องตรง ๆ
            ];
        }

        return $out;
    }
    public function updateCart(Request $request, $id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $qty = (int) $request->input('qty');
            $product = \App\Models\Product::find($id);
            $stock = $product->total_remain ?? 0;

            // กันไม่ให้เกิน stock
            if ($qty > $stock) {
                return redirect()->route('pos.index')
                    ->with('error', "สต็อกไม่พอ (เหลือ {$stock})");
            }

            if ($qty >= 1) {
                $cart[$id]['qty'] = $qty;
            } else {
                unset($cart[$id]); // ถ้า 0 ลบออก
            }

            Session::put('cart', $cart);
        }

        return redirect()->route('pos.index');
    }



    public function removeFromCart($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }

        return redirect()->route('pos.index');
    }

    public function checkout(Request $request)
    {
        $roomId  = $request->input('room_id');
        $orderId = $request->input('order_id');
        $method  = $request->input('payment_method');
        $cash    = $request->input('cash_amount');

        $cart = Session::get('cart', []);
        $isWalkIn = ($orderId === 'walkin');

        // ================== แก้ไขส่วนนี้ ==================
        // 1. ตรวจสอบข้อมูลพื้นฐานที่ต้องมีเสมอ
        if (!$roomId || !$orderId || !$method) {
            return redirect()->route('pos.index')
                ->with('error', 'กรุณาเลือกห้อง, Order, และวิธีจ่ายเงิน');
        }

        // 2. ตรวจสอบตะกร้าสินค้า โดยจะเช็คก็ต่อเมื่อ "ไม่ใช่" Walk-in
        if (!$isWalkIn && empty($cart)) {
            return redirect()->route('pos.index')
                ->with('error', 'ลูกค้าในห้องต้องมีสินค้าในตะกร้า');
        }
        // ===============================================

        $order = null;

        // --- ส่วนที่จำเป็นเพื่อป้องกัน Error ---
        if ($isWalkIn) {
            $duration = $request->input('duration_minutes');
            $serviceCostName = match ((int)$duration) {
                40 => 'forty_minutes',
                60 => 'sixty_minutes',
                90 => 'ninety_minutes',
                default => null, // กำหนดเป็น null หากค่าไม่ตรงกับที่กำหนด
            };
            // ถ้าเป็น Walk-in ให้สร้าง Order ใหม่แบบง่ายๆ ก่อน
            // (ยังไม่รวมการคำนวณราคา, จะทำในขั้นตอนถัดไป)
            $order = Order::create([
                'ref_branch_id'      => Auth::user()->ref_branch_id,
                'order_number'    => Auth::user()->ref_branch_id . strtoupper(uniqid()), // สร้างเลข Order แบบง่ายๆ
                'ref_customer_id'   => $request->input('customer_id') ?: null,
                'ref_user_id'    => $request->input('staff_id'),
                'ref_seller_id'     => $request->input('mama_id'),
                'ref_room_id'     => $roomId,
                'service_laundry_cost'     => $serviceCostName,
                'ref_status_id'      => 2,
                'booking_date'     => Carbon::today(),
                'start_time'      => Carbon::now()->format('H:i:s'),
                'end_time'        => Carbon::now()->addMinutes($duration)->format('H:i:s'),
                'total_price' => $request->input('total_price'),
            ]);
            if ($request->filled('addon_id')) {
                $addon = AddonOption::find($request->input('addon_id'));
                if ($addon) {
                    // สร้าง record ใหม่ในตาราง order_has_addon_options
                    OrderHasAddonOption::create([
                        'ref_order_id'  => $order->id,
                        'ref_option_id' => $addon->id,
                        'price'         => $addon->price,
                    ]);
                }
            }
        } else {
            // ถ้าเป็นลูกค้าปกติ ให้ค้นหา Order เดิม
            $order = Order::findOrFail($orderId);
        }
        // ------------------------------------


        // --- โค้ดส่วนที่เหลือของคุณ (ทำงานกับตัวแปร $order ที่ได้มา) ---
        foreach ($cart as $item) {
            // 1) บันทึกสินค้าใน order_has_products
            $order->products()->create([
                'ref_product_id' => $item['id'],
                'price'          => $item['price'],
                'quantity'       => $item['qty'],
            ]);

            // 2) ลด stock
            $stock = CardStocks::where('ref_product_id', $item['id'])
                ->where('quantity', '!=', 0)
                ->first();

            if ($stock) {
                $newRemain = max(0, $stock->remain - $item['qty']);
                $stock->remain = $newRemain;
                $stock->save();
            }
        }

        // 3) คำนวณยอดรวม
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $discount = 0;
        $tax      = 0;
        $total    = $subtotal - $discount + $tax;
        $total_price = $order->total_price + $total;

        $order->total_price = $total_price;
        $order->updated_at  = now();
        $order->save();

        Session::forget('cart');

        return redirect()->route('pos.index')->with('success', 'Checkout สำเร็จแล้ว');
    }


    public function getActiveCustomersInRoom($roomId)
    {
        $now = now();
        $today = $now->toDateString();
        $time  = $now->format('H:i:s');

        $orders = \App\Models\Order::where('ref_room_id', $roomId)
            ->where('ref_status_id', 2) // กำลังใช้งาน
            ->whereDate('booking_date', $today)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>=', $time)
            ->with('customer:id,name,phone') // ต้องมี relation Order->customer
            ->get(['id', 'ref_customer_id']); // เอา id ของ order ด้วย

        // map response
        $data = $orders->map(function ($o) {
            return [
                'order_id'    => $o->id,
                'customer_id' => $o->ref_customer_id,
                'name'        => $o->customer->name ?? 'Unknown',
                'phone'       => $o->customer->phone ?? null,
            ];
        });

        return response()->json($data);
    }
    public function searchStaff(Request $request)
    {
        $q = $request->get('q', '');
        $branchId = Auth::user()->ref_branch_id;

        $staff = User::query()
            ->where('ref_position_id', 2)
            ->where('ref_branch_id', $branchId)
            ->when($q, function ($query) use ($q) {
                $query->where('nickname', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'nickname', 'salary']); // ✅ เอาเฉพาะที่ต้องใช้

        return response()->json($staff);
    }
    public function searchAddons(Request $request)
    {
        $query = $request->input('q');

        $addons = AddonOption::select('id', 'name', 'price')
            ->where('name', 'like', "%{$query}%")
            ->get();

        return response()->json($addons);
    }
    public function searchSalesStaff(Request $request)
    {
        $branchId = auth()->user()->ref_branch_id;
        $query = $request->input('q');

        $staff = User::where('ref_position_id', 1)
            ->where('ref_branch_id', $branchId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('nickname', 'like', "%{$query}%");
            })
            ->select('id', 'nickname', 'name')
            ->get();

        return response()->json($staff->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nickname ? "{$item->nickname} | {$item->name}" : $item->name,
            ];
        }));
    }
    public function calculateSummary(Request $request)
    {
        // 1. รับข้อมูลทั้งหมดที่จำเป็นจาก request และ session
        $cart = Session::get('cart', []);
        $addonId = $request->input('addon_id');
        $roomId = $request->input('room_id');
        $duration = $request->input('duration_minutes');
        $staffId = $request->input('staff_id');

        $items = [];
        $subtotal = 0;

        // 2. คำนวณราคาห้อง (ถ้ามี)
        if ($roomId && $duration) {
            $room = Room::find($roomId);
            if ($room) {
                $priceColumn = match ((int)$duration) {
                    40 => 'forty_minutes',
                    60 => 'sixty_minutes',
                    90 => 'ninety_minutes',
                    default => null
                };

                if ($priceColumn && isset($room->{$priceColumn})) {
                    $roomPrice = $room->{$priceColumn};
                    $items[] = [
                        'name'    => 'ค่าบริการห้อง (' . $room->name . ')',
                        'details' => $duration . ' นาที',
                        'total'   => $roomPrice,
                    ];
                    $subtotal += $roomPrice;
                }
            }
        }

        // 3. คำนวณราคาพนักงาน (ถ้ามี)
        if ($staffId) {
            $staff = User::find($staffId);
            if ($staff && isset($staff->salary)) {
                $staffPrice = $staff->salary;
                $items[] = [
                    'name'    => 'ค่าบริการพนักงาน (' . ($staff->nickname ?? 'N/A') . ')',
                    'details' => 'บริการ',
                    'total'   => $staffPrice,
                ];
                $subtotal += $staffPrice;
            }
        }

        // 4. คำนวณราคาสินค้าในตะกร้า (ถ้ามี)
        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['qty'];
            $items[] = [
                'name'    => $item['name'],
                'details' => number_format($item['price'], 2) . ' x ' . $item['qty'],
                'total'   => $itemTotal,
            ];
            $subtotal += $itemTotal;
        }

        // 5. คำนวณราคาสินค้าเสริม (ถ้ามี)
        if ($addonId) {
            $addon = AddonOption::find($addonId);
            if ($addon) {
                $items[] = [
                    'name'    => $addon->name . ' (เสริม)',
                    'details' => number_format($addon->price, 2) . ' x 1',
                    'total'   => $addon->price,
                ];
                $subtotal += $addon->price;
            }
        }

        // 6. คำนวณยอดรวมสุทธิ (สามารถเพิ่มส่วนลด, ภาษี ได้ตรงนี้)
        $discount = 0;
        $tax = 0;
        $total = ($subtotal - $discount) + $tax;

        // 7. ส่งผลลัพธ์กลับไปเป็น JSON
        return response()->json([
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax'      => $tax,
            'total'    => $total,
        ]);
    }
}
