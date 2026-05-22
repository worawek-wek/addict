<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\AddonOption;
use App\Models\StockReadyForSale;
use App\Models\CardStocks;
use App\Models\HistoryStock;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderHasAddonOption;
use App\Models\OrderHasProduct;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomTypeHasCourse;
use App\Models\DrinkStockReadyForSale;
use App\Models\Course;
use App\Models\User;
use App\Models\Branch;
use App\Models\Drink;
use App\Models\DrinkCardStocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $room_id)
    {
        $room = Room::find($room_id);

        $activeOrder = Order::where('ref_room_id', $room->id)
                            ->where('ref_status_id', 2)
                            ->whereDate('booking_date', today())
                            ->whereTime('start_time', '<=', now())
                            ->whereTime('end_time', '>=', now())
                            ->first();

        $room->is_busy = (bool) $activeOrder;

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
        $data['room'] = $room;

        // ---------------- Products + Search ----------------
        $q = trim((string) $request->get('q', ''));

        $branchId = Auth::user()->ref_branch_id ?? null;
        $data['products'] = Product::with('latestStock')
            ->when($q !== '', fn($b) => $b->where('name', 'like', "%{$q}%"))
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->where('ref_status_id', 1)
            ->orderBy('name')
            ->get();

        if ($request->ajax() || $request->boolean('ajax')) {
            return view('pos.partials.product-grid', $data)->render();
        }

        // ---------------- Cart Totals ----------------
        $data['branches'] = Branch::all();
        $data['room_type'] = RoomType::orderBy('name')
                                        ->where('ref_status_id', 1)
                                        ->where('ref_branch_id', $branchId)
                                        ->get();
        $data['course'] = Course::orderBy('name')->where('ref_branch_id', $branchId)->where('ref_status_id', 1)->get();
        $cart = Session::get('cart', []);
        $data['cart'] = $cart;
        $subtotal = collect($cart)->sum(fn($i) => (float)($i['price'] ?? 0) * (int)($i['qty'] ?? 0));
        $discount = 0;
        $tax = 0;
        $data['subtotal'] = $subtotal;
        $data['discount'] = $discount;
        $data['tax'] = $tax;
        $data['total'] = $subtotal - $discount + $tax;
        $data['room_id'] = $room_id;

        // ---------------- Rooms (only by branch) ----------------
        $branchId = Auth::user()->ref_branch_id ?? null;

        $rooms = Room::query()
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->orderBy('id')
            ->get();

        $data['roomGroups'] = $this->groupRoomsForModal($rooms);

        $data['storefrontName'] = 'Cashier';
        $data['addonOptions'] = AddonOption::where('branch', $branchId)->orderBy('name')->get();

        $data['branches'] = Branch::all();

        return view('pos.index', $data);
    }
    public function product($product_id = null)
    {
        if (@$product_id) {
            $data['product_id'] = $product_id;
        }
        $branchId = Auth::user()->ref_branch_id ?? null;
        $data['products'] = Product::with('latestStock', 'producttype')
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->where('ref_status_id', 1)
            ->orderBy('name')
            ->get();

        // ---------------- Cart Totals ----------------
        $data['branches'] = Branch::all();
        $data['room_type'] = RoomType::orderBy('name')
                                        ->where('ref_status_id', 1)
                                        ->whereHas('room', function ($query) use ($branchId) {
                                            $query->where('ref_branch_id', $branchId);
                                        })
                                        ->get();
        $data['course'] = Course::orderBy('name')->where('ref_status_id', 1)->get();
        $cart = Session::get('cart', []);
        $data['cart'] = $cart;
        $subtotal = collect($cart)->sum(fn($i) => (float)($i['price'] ?? 0) * (int)($i['qty'] ?? 0));
        $discount = 0;
        $tax = 0;
        $data['subtotal'] = $subtotal;
        $data['discount'] = $discount;
        $data['tax'] = $tax;
        $data['total'] = $subtotal - $discount + $tax;

        // ---------------- Rooms (only by branch) ----------------
        $branchId = Auth::user()->ref_branch_id ?? null;

        $rooms = Room::query()
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->orderBy('id')
            ->get();

        $data['roomGroups'] = $this->groupRoomsForModal($rooms);

        $data['storefrontName'] = 'Cashier';
        $data['addonOptions'] = AddonOption::orderBy('name')->get();

        $data['branches'] = Branch::all();

        return view('pos.product', $data);
    }
    public function drink(Request $request, $drink_id = null)
    {
        // ---------------- Products + Search ----------------
        $q = trim((string) $request->get('q', ''));

        $branchId = Auth::user()->ref_branch_id ?? null;
        $data['drinks'] = Drink::with('latestStock')
            ->when($q !== '', fn($b) => $b->where('name', 'like', "%{$q}%"))
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->where('ref_status_id', 1)
            ->orderBy('name')
            ->get();

        if ($request->ajax() || $request->boolean('ajax')) {
            return view('pos.partials.drink-grid', $data)->render();
        }

        // ---------------- Cart Totals ----------------
        $data['branches'] = Branch::all();
        $data['room_type'] = RoomType::orderBy('name')
                                        ->where('ref_status_id', 1)
                                        ->whereHas('room', function ($query) use ($branchId) {
                                            $query->where('ref_branch_id', $branchId);
                                        })
                                        ->get();
        $data['course'] = Course::orderBy('name')->where('ref_status_id', 1)->get();
        $cart = Session::get('cart', []);
        $data['cart'] = $cart;
        $subtotal = collect($cart)->sum(fn($i) => (float)($i['price'] ?? 0) * (int)($i['qty'] ?? 0));
        $discount = 0;
        $tax = 0;
        $data['subtotal'] = $subtotal;
        $data['discount'] = $discount;
        $data['tax'] = $tax;
        $data['total'] = $subtotal - $discount + $tax;
        $data['room_id'] = 1;
        $data['drink_id'] = $drink_id;

        // ---------------- Rooms (only by branch) ----------------
        $branchId = Auth::user()->ref_branch_id ?? null;

        $rooms = Room::query()
            ->when($branchId, fn($b) => $b->where('ref_branch_id', $branchId))
            ->orderBy('id')
            ->get();

        $data['roomGroups'] = $this->groupRoomsForModal($rooms);

        $data['storefrontName'] = 'Cashier';
        $data['users'] = User::orderBy('name')->get();

        $data['branches'] = Branch::all();

        return view('pos.drink', $data);
    }

    public function get_user(Request $request)
    {
        try {
            return $find = User::where('ref_branch_id', Auth::user()->ref_branch_id)
                                ->where(function ($q) use ($request) {
                                    $q->where('user_code', $request->user_code)
                                        ->orWhere('user_id', $request->user_code);
                                })->first();
            // if(@$request->ref_position_id){
            // $find = $find->where('ref_position_id', $request->ref_position_id)->first();
            if (!$find) {
                return [
                    "id" => null,
                    "name" => "ไม่พบพนักงาน"
                ];;
            }
            return [
                "id" => $find->id,
                "name" => "$find->nickname"
            ];
            // }
            if (!$find) {
                return "เข้างานผิดพลาด ไม่พบพนักงาน";
            }
            $user = User::find($find->id);
            $user->work_status = 1;
            $user->ref_status_id = 1;
            $user->save();

            DB::commit();

            return [
                "id" => $user->id,
                "name" => "$user->name"
            ];
        } catch (QueryException $err) {
            DB::rollBack();
        }
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

        $payment_met = $request->input('payment_method');
        // // return 123;
        // return $request;
        // $roomId  = $request->input('room_id');
        // $orderId = $request->input('order_id');
        // $method  = $request->input('payment_method_radio');
        // $cash    = $request->input('cash_amount');
        // $cart = Session::get('cart', []);
        // $isWalkIn = ($orderId === 'walkin');

        // // ================== แก้ไขส่วนนี้ ==================
        // // 1. ตรวจสอบข้อมูลพื้นฐานที่ต้องมีเสมอ
        // if (!$roomId || !$orderId || !$method) {
        //     return redirect()->route('pos.index')
        //         ->with('error', 'กรุณาเลือกห้อง, Order, และวิธีจ่ายเงิน');
        // }

        // // 2. ตรวจสอบตะกร้าสินค้า โดยจะเช็คก็ต่อเมื่อ "ไม่ใช่" Walk-in
        // if (!$isWalkIn && empty($cart)) {
        //     return redirect()->route('pos.index')
        //         ->with('error', 'ลูกค้าในห้องต้องมีสินค้าในตะกร้า');
        // }
        // // ===============================================

        // $order = null;

        // // --- ส่วนที่จำเป็นเพื่อป้องกัน Error ---
        // if ($isWalkIn) {
        //     $duration = $request->input('duration_minutes');
        //     $serviceCostName = match ((int)$duration) {
        //         40 => 'forty_minutes',
        //         60 => 'sixty_minutes',
        //         90 => 'ninety_minutes',
        //         default => null,
        //     };
        // return $request->input('ref_course_id');
        if (@$request->input('ref_course_id')) {
            $course = Course::find($request->input('ref_course_id'));
            $duration = (int) filter_var($course->name, FILTER_SANITIZE_NUMBER_INT);
        }
        //     // สร้าง Order ใหม่แบบง่ายๆ ก่อน
        //     $addon_ids = [];
        //     if ($request->filled('addon_id')) {
        //         $addon_ids = is_array($request->input('addon_id')) ? $request->input('addon_id') : [$request->input('addon_id')];
        //     }
        // return $request->input('type');
        $mama_id = $request->input('mama_id');
        $order = Order::create([
            'type'      => $request->input('type') ?? 1,
            'ref_branch_id'      => Auth::user()->ref_branch_id,
            'order_number'    => Auth::user()->ref_branch_id . strtoupper(uniqid()),
            'ref_customer_id'   => $request->input('customer_id') ?: null,
            'ref_account_id'    => Auth::id(),
            'ref_user_id'    => $request->input('staff_id') ?? null,
            'customer_type'     => $request->input('customer_type') ?? 2,
            'ref_seller_id'     => $request->input('reception_id'),
            'ref_room_id'     => $request->input('ref_room_id') ?? null,
            'ref_room_type_id'     => $request->input('ref_room_type_id') ?? null,
            'service_laundry_cost'     => $request->input('ref_course_id') ?? null,
            'ref_status_id'      => 2,
            'booking_date'     => Carbon::today(),
            'start_time'      => Carbon::now()->format('H:i:s'),
            'end_time'        => @$duration ? Carbon::now()->addMinutes($duration)->format('H:i:s') : null,
            // 'total_price' => 3000,
            'discount' => preg_replace('/[^0-9.]/', '', $request->input('discount') ?? 0.00),
            'total_price' => preg_replace('/[^0-9.]/', '', $request->input('total_price')),
            'payment_method' => $request->input('payment_method') ?? null,
            'payment_status' => $request->input('payment_status') ?? 1,
        ]);
        // เพิ่ม addon option ใน order_has_addon_options
        if ($request->filled('ref_option_id')) {
            foreach ($request->ref_option_id as $addon_id) {
                $addon = AddonOption::find($addon_id);
                if ($addon) {
                    OrderHasAddonOption::create([
                        'ref_order_id'  => $order->id,
                        'ref_option_id' => $addon->id,
                        'price'         => $addon->price,
                    ]);
                }
            }
        }

        // // --- คำนวณค่าคอมมิชชั่นและค่าเชียร์ ---
        // $commission_value = 0;
        // $commission_options_value = 0;
        // $price_options_sales = 0;
        // // Massage commission (service_duration)
        // if ($order->ref_user_id && $serviceCostName) {
        //     $duration = null;
        //     switch ($serviceCostName) {
        //         case 'forty_minutes': $duration = 40; break;
        //         case 'sixty_minutes': $duration = 60; break;
        //         case 'ninety_minutes': $duration = 90; break;
        //     }
        //     if ($duration) {
        //         $commission = \App\Models\MassageCommission::where('ref_user_id', $order->ref_user_id)
        //             ->where('service_duration', $duration)
        //             ->where('ref_branch_id', $order->ref_branch_id)
        //             ->first();
        //         if (!$commission) {
        //             $commission = \App\Models\MassageCommission::whereNull('ref_user_id')
        //                 ->where('service_duration', $duration)
        //                 ->where('ref_branch_id', $order->ref_branch_id)
        //                 ->first();
        //         }
        //         if ($commission) {
        //             if ($commission->commission_amount) {
        //                 $commission_value += $commission->commission_amount;
        //             } elseif ($commission->commission_percent) {
        //                 $room_price = 0;
        //                 $room = Room::find($roomId);
        //                 if ($room) {
        //                     if ($duration == 40) $room_price = $room->forty_minutes;
        //                     if ($duration == 60) $room_price = $room->sixty_minutes;
        //                     if ($duration == 90) $room_price = $room->ninety_minutes;
        //                 }
        //                 $staff_salary = User::find($order->ref_user_id)->salary ?? 0;
        //                 $commission_base = $room_price + $staff_salary;
        //                 $commission_value += ($commission->commission_percent / 100) * $commission_base;
        //             }
        //         }
        //     }
        // }
        // // Massage commission (addon options)
        // if ($order->ref_user_id && !empty($addon_ids)) {
        //     foreach ($addon_ids as $addon_id) {
        //         $commission = \App\Models\MassageCommission::where('ref_user_id', $order->ref_user_id)
        //             ->where('addon_options_id', $addon_id)
        //             ->where('ref_branch_id', $order->ref_branch_id)
        //             ->first();
        //         if (!$commission) {
        //             $commission = \App\Models\MassageCommission::whereNull('ref_user_id')
        //                 ->where('addon_options_id', $addon_id)
        //                 ->where('ref_branch_id', $order->ref_branch_id)
        //                 ->first();
        //         }
        //         $addon = AddonOption::find($addon_id);
        //         if ($commission && $addon) {
        //             if ($commission->commission_amount) {
        //                 $commission_options_value += $commission->commission_amount;
        //             } elseif ($commission->commission_percent) {
        //                 $commission_options_value += ($commission->commission_percent / 100) * $addon->price;
        //             }
        //         }
        //     }
        // }
        //     // CheerCharge for sales
        //     if ($order->ref_seller_id && !empty($addon_ids)) {
        //         foreach ($addon_ids as $addon_id) {
        //             $cheer = \App\Models\CheerCharge::where('ref_branch_id', $order->ref_branch_id)
        //                 ->where('addon_options_id', $addon_id)
        //                 ->first();
        //             $addon = AddonOption::find($addon_id);
        //             if ($cheer && $addon) {
        //                 if ($cheer->type == 'baht') {
        //                     $price_options_sales += $cheer->amount;
        //                 } elseif ($cheer->type == 'percent') {
        //                     $price_options_sales += ($cheer->amount / 100) * $addon->price;
        //                 }
        //             }
        //         }
        //     }
        //     // Save to commissions_history
        //     \App\Models\CommissionsHistory::updateOrCreate(
        //         [
        //             'order_id' => $order->id,
        //             'user_message_id' => $order->ref_user_id ?? null,
        //         ],
        //         [
        //             'commission_massage_amount' => $commission_value,
        //             'price_options_massage' => $commission_options_value,
        //             'user_sales_id' => $order->ref_seller_id ?? null,
        //             'price_options_sales' => $price_options_sales,
        //         ]
        //     );
        // // } else {
        // //     // ถ้าเป็นลูกค้าปกติ ให้ค้นหา Order เดิม
        // //     $order = Order::findOrFail($orderId);
        // // }
        // // ------------------------------------


        $customerType = $request->input('customer_type', 2);
        $grouped_products = [];
        if (!empty($request->qty)) {
            foreach ($request->qty as $id => $q) {
                if ($q == 0 || $q == null) {
                    continue;
                }

                $product = Product::find($id);
                $main_stock_remain = $product->total_remain ?? 0;
                $ready_for_sale_remain = $product->ready_for_sale_total_remain ?? 0;
                // StockReadyForSale::where('ref_product_id', $row->id)->sum('remain')
                // return $main_stock_remain + $ready_for_sale_remain;
                if (!$product) continue;

                $price = $customerType == 1 ? $product->price_staff : $product->price;

                $stock = StockReadyForSale::where('ref_product_id', $id)
                    ->where('remain', '!=', 0)
                    ->first();

                $product_cost = 0;
                if ($stock) {
                    $main_stock = CardStocks::find($stock->ref_lot_id);
                    if ($main_stock && $main_stock->quantity > 0) {
                        $product_cost = $main_stock->cost_price / $main_stock->quantity;
                    }

                    $newRemain = max(0, $stock->remain - $q);
                    $stock->remain = $newRemain;
                    $stock->save();
                }

                $new_product = Product::find($id);
                $new_main_stock_remain = $new_product->total_remain ?? 0;
                $new_ready_for_sale_remain = $new_product->ready_for_sale_total_remain ?? 0;
                
// เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> ตัดสต็อก {

                $history_stock = new HistoryStock;
                $history_stock->ref_product_id = $id; // id สินค้า
                $history_stock->quantity = $q; // จำนวนที่เคลื่อนไหว
                $history_stock->stock_before_quantity = $main_stock_remain; // จำนวน ก่อน ตัดสต็อก
                $history_stock->stock_after_quantity = $new_main_stock_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->stock_ready_for_sale_before_quantity = $ready_for_sale_remain; // จำนวน ก่อน ตัดสต็อก
                $history_stock->stock_ready_for_sale_after_quantity = $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                $history_stock->quantity_type = 0; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
                $history_stock->withdraw_quantity = 0;
                $history_stock->save();
////////////////////////////////////////////////////////////////
                // $history_stock = new HistoryStock;
                // $history_stock->ref_product_id = $id; // id สินค้า
                // $history_stock->quantity = $q; // จำนวนที่เคลื่อนไหว
                // $history_stock->stock_before_quantity = $main_stock_remain + $ready_for_sale_remain; // จำนวน ก่อน ตัดสต็อก
                // $history_stock->stock_after_quantity = $new_main_stock_remain + $new_ready_for_sale_remain; // จำนวน หลัง ตัดสต็อก
                // $history_stock->quantity_type = 0; // 0 = ลด(ขาย) , 1 = เพิ่ม , 2 = ลด(นำออก)
                // $history_stock->save();
// เพิ่ม ประวัติ การเคลื่อนไหวสต็อก -> ตัดสต็อก }
                
                $order->products()->create([
                    'ref_product_id' => $id,
                    'price'          => $price,
                    'quantity'       => $q,
                    'cost'           => $product_cost,
                    'stock_before_quantity' => $main_stock_remain + $ready_for_sale_remain,
                    'stock_after_quantity' => $new_main_stock_remain + $new_ready_for_sale_remain,
                ]);

                $type = \App\Models\ProductType::find($product->type_id);
                $typeName = $type ? $type->name : 'อื่นๆ / ไม่ระบุประเภท';

                $grouped_products[$typeName][] = '<tr>
                <td>' . $product->name . '</td>
                <td class="text-center">' . $q . '</td>
                <td class="text-right">' . number_format($price, 2) . '</td>
                <td class="text-right">' . number_format($price * $q, 2) . '</td>
            </tr>';
            }
        }

        $list_product = "";
        foreach ($grouped_products as $typeName => $rows) {
            $list_product .= '<tr class="table-active" style="background-color: #f3f4f6;">
                <td colspan="4"><strong>' . $typeName . '</strong></td>
            </tr>';

            foreach ($rows as $row) {
                $list_product .= $row;
            }
        }

        // 3) คำนวณยอดรวม
        // $subtotal = collect($request->qty)->sum(fn($i) => 100 * $q);
        // $discount = 0;
        // $total    = $subtotal - $discount;
        // $total_price = $order->total_price + $total;

        // $order->total_price = $total_price;
        $order->updated_at  = now();
        $order->save();
        if (!$request->input('ref_room_type_id')) {
            // $room_type = RoomType::find(1);
            // $room_type_name = $room_type->name;
            // dd(\Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i'));
            $qr = QrCode::size(150)->generate(url("admin/order-rooms/$order->id"));

$subtotal = $order->products->sum(fn($p) => $p->price * $p->quantity);
$discount = $order->discount ?? 0;
$total    = max(0, $subtotal - $discount);

$payment_status = '';

if (!$order->payment_status) {
    $payment_status = "<div class='unpaid-watermark'>ยังไม่ชำระเงิน</div>";
}

$slip = "
<!DOCTYPE html>
<html lang='th'>

<head>

    <meta charset='UTF-8'>

    <meta name='viewport'
        content='width=device-width, initial-scale=1.0'>

    <title>Receipt</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: Arial, sans-serif;
            font-size:11px;
            color:#000;
        }

        /* =========================
            INVOICE
        ========================== */

        .invoice{
            width:69mm;
            padding:5px;
            font-size:11px;
        }

        .invoice-header{
            display:flex;
            justify-content:space-between;
            margin-bottom:5px;
            font-weight:bold;
        }

        .invoice-title{
            flex:1;
            text-align:center;
        }

        .invoice-table{
            width:100%;
            border-collapse:collapse;
            margin-top:5px;
            border-top:1px dashed #000;
            border-bottom:1px dashed #000;
        }

        .invoice-table th,
        .invoice-table td{
            padding:3px 2px;
            font-size:11px;
        }

        .invoice-table th{
            border-bottom:1px dashed #000;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        /* =========================
            RECEIPT
        ========================== */

        .receipt{
            width:69mm;
            max-width:69mm;
            padding:5px;
            position:relative;
        }

        .receipt-header{
            text-align:center;
            border-bottom:1px dashed #000;
            padding-bottom:8px;
            margin-bottom:8px;
        }

        .receipt-header h1{
            font-size:16px;
            margin-bottom:2px;
        }

        .receipt-subtitle{
            font-size:11px;
            margin-bottom:5px;
        }

        .receipt-info{
            display:flex;
            justify-content:space-between;
            margin-top:3px;
            font-size:11px;
        }

        .receipt-info label{
            display:inline-block;
            width:65px;
        }

        .receipt-section-title{
            text-align:center;
            font-weight:bold;
            margin-bottom:5px;
            font-size:12px;
        }

        .receipt-table{
            width:100%;
            border-collapse:collapse;
            margin-top:5px;
        }

        .receipt-table th,
        .receipt-table td{
            padding:3px 2px;
            font-size:11px;
        }

        .receipt-table th{
            border-bottom:1px dashed #000;
        }

        .receipt-right{
            text-align:right;
        }

        .receipt-total{
            margin-top:8px;
            border-top:1px dashed #000;
            padding-top:5px;
        }

        .receipt-total-line{
            display:flex;
            justify-content:space-between;
            margin-bottom:3px;
            font-size:11px;
        }

        .receipt-grand-total{
            display:flex;
            justify-content:space-between;
            border-top:1px solid #000;
            padding-top:5px;
            margin-top:5px;
            font-size:14px;
            font-weight:bold;
        }

        .receipt-footer{
            text-align:center;
            margin-top:10px;
            font-size:11px;
        }

        .payment-success{
            text-align:center;
            font-size:13px;
            font-weight:bold;
            color:green;
            margin-top:10px;
        }

        .payment-unpaid{
            text-align:center;
            font-size:13px;
            font-weight:bold;
            color:#d9534f;
            margin-top:10px;
        }

        .unpaid-watermark{
            position:absolute;
            top:45%;
            left:50%;
            transform:translate(-50%, -50%) rotate(-30deg);
            font-size:34px;
            color:rgba(255,0,0,0.12);
            border:3px solid rgba(255,0,0,0.12);
            padding:8px 15px;
            font-weight:bold;
            z-index:0;
        }

        .receipt-content{
            position:relative;
            z-index:1;
        }

        @page{
            size:69mm auto;
            margin:0;
        }

        @media print{

            body{
                margin:0;
                padding:0;
            }

            .invoice,
            .receipt{
                width:69mm;
                max-width:69mm;
                padding:5px;
            }

        }

    </style>

</head>

<body>

    <!-- =======================
        INVOICE
    ======================== -->

    <div class='invoice'>

        <div class='invoice-header'>

            <div class='invoice-title'>
                ใบแจ้งหนี้ชั่วคราว
            </div>

            <div>
                No : ".$order->order_number."
            </div>

        </div>

        <div>
            แคชเชียร์ : Addict
        </div>

        <div>
            เช็คบิล :
            ".\Carbon\Carbon::parse(
                date('Y-m-d', strtotime($order->booking_date))
                .' '.
                $order->end_time
            )->format('d/m/Y H:i:s')."
        </div>

        <table class='invoice-table'>

            <thead>

                <tr>

                    <th>รายการสินค้า</th>

                    <th class='text-center'>
                        จำนวน
                    </th>

                    <th class='text-right'>
                        ราคา
                    </th>

                    <th class='text-right'>
                        รวม
                    </th>

                </tr>

            </thead>

            <tbody>

                ".$list_product."

            </tbody>

        </table>

    </div>

    <div style='page-break-before:always;'></div>

    <!-- =======================
        RECEIPT
    ======================== -->

    <div class='receipt'>

        ".$payment_status."

        <div class='receipt-content'>

            <div class='receipt-header'>

                <h1>
                    Addict Coffee House
                </h1>

                <div class='receipt-subtitle'>
                    ใบเสร็จรับเงิน / Receipt
                </div>

                <div class='receipt-info'>
                    <span>เลขที่</span>
                    <span>".$order->order_number."</span>
                </div>

                <div class='receipt-info'>
                    <span>วันที่</span>
                    <span>".date('d/m/Y H:i')."</span>
                </div>

                <div class='receipt-info'>
                    <span>สาขา</span>
                    <span>".($order->branch->name ?? '-')."</span>
                </div>

                <div class='receipt-info'>
                    <span>พนักงาน</span>
                    <span>".($order->seller->nickname ?? '-')."</span>
                </div>

            </div>

            <div class='receipt-section-title'>
                รายการสินค้า
            </div>

            <table class='receipt-table'>

                <thead>

                    <tr>

                        <th>#</th>
                        <th>สินค้า</th>

                        <th class='receipt-right'>
                            จำนวน
                        </th>

                        <th class='receipt-right'>
                            รวม
                        </th>

                    </tr>

                </thead>

                <tbody>
";
$key_p = 1;
foreach ($order->products as $item) {

    $slip .= "
        <tr>
            <td>".$key_p++."</td>

            <td>
                ".$item->product->name."
            </td>

            <td class='receipt-right'>
                ".$item->quantity."
            </td>

            <td class='receipt-right'>
                ".number_format(
                    $item->price * $item->quantity,
                    2
                )."
            </td>

        </tr>
    ";
}

$slip .= "

                </tbody>

            </table>

            <div class='receipt-total'>

                <div class='receipt-total-line'>

                    <span>Subtotal</span>

                    <span>
                        ".number_format($subtotal, 2)."
                    </span>

                </div>
";

if ($discount > 0) {

    $slip .= "
        <div class='receipt-total-line'>

            <span>ส่วนลด</span>

            <span>
                - ".number_format($discount, 2)."
            </span>

        </div>
    ";
}

$slip .= "

                <div class='receipt-grand-total'>

                    <span>ยอดรวมสุทธิ</span>

                    <span>
                        ".number_format($total, 2)." ฿
                    </span>

                </div>

            </div>
";

if ($order->payment_status) {

    $slip .= "
        <div class='payment-success'>

            ✓ ชำระเงินแล้ว
    ";

    if ($order->payment_method) {

        $slip .= "
            <div>
                ".$order->payment_method."
            </div>
        ";
    }

    $slip .= "
        </div>
    ";

} else {

    $slip .= "
        <div class='payment-unpaid'>

            ⚠ ยังไม่ชำระเงิน

        </div>
    ";
}

$slip .= "

            <div class='receipt-footer'>

                ขอบคุณที่ใช้บริการ / Thank you for your business!

            </div>

        </div>

    </div>

</body>

</html>
";
            return response()->json([
                'status' => true,
                'data' => $slip
            ]);
            return 1;
        }
        $room_type = RoomType::find($request->input('ref_room_type_id'));
        $room_type_name = $room_type->name;
        // dd(\Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i'));
        $qr = QrCode::size(150)->generate(url("admin/order-rooms/$order->id"));

        $slip = "<!DOCTYPE html>
                        <html lang='th'>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>รายละเอียดการจอง</title>
                            <style>
                                body { font-family: Arial, sans-serif; font-size: 11px; }
                                .invoice { width: 69mm; font-size: 11px;padding: 20px; }
                                .header { display: flex; justify-content: space-between; align-items: end; font-weight: bold; font-size: 10px; }
                                .title { flex-grow: 1; text-align: center; font-size: 11px; }
                                .right-align { text-align: right; }
                                table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; border-top: 1px solid #000; }
                                th, td { padding: 2px; text-align: left; font-size: 11px; }
                                th { border-bottom: 1px solid #000; }
                                td { border-bottom: 1px solid #000; }

                                @media print {
                                    @page {
                                        size: 69mm auto;
                                        margin: 0;
                                    }

                                    body {
                                        width: 69mm;
                                        margin: 0;
                                    }

                                    .invoice {
                                        width: 69mm;
                                    }
                                }
                            </style>

                        </head>
                        <body>
                            <div class='invoice'>
                                <div class='header' align='right'>
                                    <span class='title'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ใบแจ้งหนี้ชั่วคราว </span>
                                    <span class='right-align'>No_: $order->order_number</span>
                                </div>
                                <p class='right-align'><strong>แคชเชียร์:</strong> Addict</p>
                                <p><strong>ห้อง:</strong> " . $order->room->name . "</p>
                                <p><strong>เปิดห้อง:</strong> " . \Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i') . "</p>
                                <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>
                                <p></p><strong>วิธีชำระเงิน:</strong> $payment_met</p>

                                <table>
                                    <tr>
                                        <th>จำนวน</th>
                                        <th>รายการสินค้า</th>
                                        <th>@ ราคา</th>
                                        <th>รวม</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>" . $order->user->nickname . " + " . $order->course->name . " " . $order->room_type->name . "</td>
                                        <td>$order->total_price</td>
                                        <td>$order->total_price</td>
                                    </tr>
                                    <tr>
                                        <td colspan='3' style='border-top:unset;padding:10px'> ผู้ดูแล " . $order->seller->user_id . " " . $order->seller->nickname . " </td>
                                    </tr>
                                </table>
                            </div>
                            <div style='page-break-before: always;'></div>
                            <div class='invoice'>
                                <div class='header' align='right'>
                                    <span class='title'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ใบคูปองพนักงาน </span>
                                    <span class='right-align'>No_: $order->order_number</span>
                                </div>
                                <p class='right-align'><strong>แคชเชียร์:</strong> Addict</p>
                                <p><strong>ห้อง:</strong> " . $order->room->name . "</p>
                                <p><strong>เปิดห้อง:</strong> " . \Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i') . "</p>
                                <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>
                                <p></p><strong>วิธีชำระเงิน:</strong> $payment_met</p>


                                <table>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ชื่อพนักงาน</th>
                                        <th>ชั่วโมงรวม</th>
                                    </tr>
                                    <tr>
                                        <td style='border:unset;padding-top:5px'>" . $order->user->user_id . "</td>
                                        <td style='border:unset;padding-top:5px'>" . $order->user->nickname . " + " . $order->course->name . " " . $order->room_type->name . "</td>
                                        <td style='border:unset;padding-top:5px'>" . floor($order->course->minute / 60) . "</td>
                                    </tr>
                                    <tr>
                                        <td colspan='3' style='border-top:unset;padding:10px'> ผู้ดูแล " . $order->seller->user_id . " " . $order->seller->nickname . " </td>
                                    </tr>
                                </table>
                                <span style='padding-top:10px'>ให้เก็บไว้ตรวจสอบ</span>

                            </div>
                            <div style='page-break-before: always;'></div>
                                <div style='padding: 10px;'>
                                $qr
                                </div>
                        </body>
                    </html>
                    ";
        if ($request->has('qty') && is_array($request->input('qty')) && count($request->input('qty')) > 0) {
            $pr = Product::whereIn('id', array_keys($request->input('qty')))->get()->groupBy(function ($product) {
                $type = ProductType::find($product->type_id);
                return $type ? $type->name : 'อื่นๆ / ไม่ระบุประเภท';
            });
            $productList = function () use ($pr, $request) {
                $prList = "";
                foreach ($pr as $typeName => $products) {
                    foreach ($products as $product) {
                        $qty = $request->input('qty')[$product->id] ?? 0;
                        if ($qty > 0) {
                            $price = $request->input('customer_type') == 1 ? $product->price_staff : $product->price;
                            $prList .= "<tr>
                            <td>{$product->name}</td>
                            <td class='text-center'>{$qty}</td>
                            <td class='text-right'>" . number_format($price, 2) . "</td>
                            <td class='text-right'>" . number_format($price * $qty, 2) . "</td>
                        </tr>";
                        }
                    }
                    return $prList;
                };
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
                    <strong></strong>วิธีชำระเงิน:</strong> $payment_met<br>

                    </div>
                </div>";
        }


        return response()->json([
            'status' => true,
            'data' => $slip
        ]);
        return 1;
    }


    public function drink_checkout(Request $request)
    {
        $mama_id = $request->input('mama_id');
        $order = Order::create([
            'type'      => 3,
            'ref_branch_id'      => Auth::user()->ref_branch_id,
            'order_number'    => Auth::user()->ref_branch_id . strtoupper(uniqid()),
            'ref_customer_id'   => $request->input('customer_id') ?: null,
            'ref_account_id'    => Auth::id(),
            'ref_user_id'    => $request->input('staff_id') ?? null,
            'customer_type'     => $request->input('customer_type') ?? 2,
            'ref_seller_id'     => $request->input('reception_id'),
            'ref_room_id'     => $request->input('ref_room_id') ?? null,
            'ref_room_type_id'     => $request->input('ref_room_type_id') ?? null,
            'service_laundry_cost'     => $request->input('ref_course_id') ?? null,
            'ref_status_id'      => 2,
            'booking_date'     => Carbon::today(),
            'start_time'      => Carbon::now()->format('H:i:s'),
            'end_time'        => @$duration ? Carbon::now()->addMinutes($duration)->format('H:i:s') : null,
            // 'total_price' => 3000,
            'discount' => preg_replace('/[^0-9.]/', '', $request->input('discount') ?? 0.00),
            'total_price' => preg_replace('/[^0-9.]/', '', $request->input('total_price')),
            'payment_method' => $request->input('payment_method') ?? null,
            'payment_status' => $request->input('payment_status') ?? 1,
        ]);

        // --- โค้ดส่วนที่เหลือของคุณ (ทำงานกับตัวแปร $order ที่ได้มา) ---
        $list_drink = "";
        foreach ($request->qty as $id => $q) {
            if ($q == 0) {
                continue;
            }
            $customerType = $request->input('customer_type', 2); // default = 2

            $drink = Drink::find($id);

            $price = $customerType == 1
                ? $drink->price_staff
                : $drink->price;
            // if(@$request->input('customer_type') == 1){
            //     $price = Drink::find($id)->price_staff;
            // }else{
            //     $price = Drink::find($id)->price;
            // }
            // 1) บันทึกสินค้าใน order_has_drinks

            // 2) ลด stock
            $stock = DrinkStockReadyForSale::where('ref_drink_id', $id)
                ->where('remain', '!=', 0)
                ->first();

            $main_stock = DrinkCardStocks::find($stock->ref_lot_id);
            $product_cost = $main_stock->cost_price / $main_stock->quantity;

            if ($stock) {
                $newRemain = max(0, $stock->remain - $q);
                $stock->remain = $newRemain;
                $stock->save();
            }

            $order->drinks()->create([
                'ref_drink_id' => $id,
                'price'          => $price,
                'quantity'       => $q,
                'cost'       => $product_cost,
            ]);

            $list_drink .= '<tr>
                                <td>' . $drink->name . '</td>
                                <td style="text-align: center;">' . $q . '</td>
                                <td style="text-align: right;">' . $price . '</td>
                                <td style="text-align: right;">' . $price * $q . '</td>
                            </tr>';
        }

        $order->updated_at  = now();
        $order->save();
        if (!$request->input('ref_room_type_id')) {
            // $room_type = RoomType::find(1);
            // $room_type_name = $room_type->name;
            // dd(\Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->start_time)->format('d/m/Y H:i'));
            $qr = QrCode::size(150)->generate(url("admin/order-rooms/$order->id"));

            $slip = "<!DOCTYPE html>
                        <html lang='th'>
                        <head>
                            <meta charset='UTF-8'>
                            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                            <title>รายละเอียดการจอง</title>
                            <style>
                                body { font-family: Arial, sans-serif; font-size: 11px; }
                                .invoice { width: 69mm; font-size: 11px;padding: 20px; }
                                .header { display: flex; justify-content: space-between; align-items: end; font-weight: bold; font-size: 10px; }
                                .title { flex-grow: 1; text-align: center; font-size: 11px; }
                                .right-align { text-align: right; }
                                table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; border-top: 1px solid #000; }
                                th, td { padding: 2px; text-align: left; font-size: 11px; }
                                th { border-bottom: 1px solid #000; }
                                td { border-bottom: 1px solid #000; }

                                @media print {
                                    @page {
                                        size: 69mm auto;
                                        margin: 0;
                                    }

                                    body {
                                        width: 69mm;
                                        margin: 0;
                                    }

                                    .invoice {
                                        width: 69mm;
                                    }
                                }
                            </style>

                        </head>
                        <body>
                            <div class='invoice'>
                                <div class='header' align='right'>
                                    <span class='title'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  ใบแจ้งหนี้ชั่วคราว </span>
                                    <span class='right-align'>No_: $order->order_number</span>
                                </div>
                                <p class='right-align'><strong>แคชเชียร์:</strong> Addict</p>
                                <p><strong>เช็คบิล:</strong> " . \Carbon\Carbon::parse(date("Y-m-d", strtotime($order->booking_date)) . ' ' . $order->end_time)->format('d/m/Y H:i:s') . "</p>

                                <table>
                                    <tr>
                                        <th>รายการสินค้า</th>
                                        <th style='text-align: center;'>จำนวน</th>
                                        <th style='text-align: right;'>@ ราคา</th>
                                        <th style='text-align: right;'>รวม</th>
                                    </tr>" . $list_drink . "
                                    <tr>
                                        <td>ส่วนลด</td>
                                        <td></td>
                                        <td></td>
                                        <td style='text-align: right;'>$order->discount</td>
                                    </tr>
                                </table>
                            </div>
                        </body>
                    </html>
                    ";
            if ($request->quantity && count($request->quantity) > 0) {
            }
            return response()->json([
                'status' => true,
                'data' => $slip
            ]);
            return 1;
        }
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
                $query->where(function ($sub) use ($q) {
                    $sub->where('nickname', 'like', "%{$q}%")
                        ->orWhere('user_code', 'like', "%{$q}%");
                });
            })
            ->limit(20)
            ->get(['id', 'nickname', 'salary', 'user_code']);

        return response()->json($staff);
    }
    public function searchAddons(Request $request)
    {
        $query = $request->input('q');
        $branchId = Auth::user()->ref_branch_id;

        $addons = AddonOption::select('id', 'name', 'price')
            ->where('name', 'like', "%{$query}%")
            ->where('branch', $branchId)
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
    public function calculate(Request $request)
    {
        // return $request;
        $rthc = RoomTypeHasCourse::where('ref_room_type_id', $request->ref_room_type_id)->where('ref_course_id', $request->ref_course_id)->first();

        $room_course = @$rthc->price ?? 0;
        $subtotal = @$room_course ?? 0;

        if (@$request->ref_option_id) {
            $subtotal += AddonOption::whereIn('id', $request->ref_option_id)->sum('price');
        }
        // return $request;
        foreach ($request->qty ?? [] as $key => $qty) {
            // return $qty;
            if ($qty > 0) {
                $subtotal += Product::find($key)->price * $qty;
            }
        }
        // if(@$request->discount > 0){
        $discount = $request->discount ?? 0;
        // $tax      = $subtotal * 0.07;
        $total = $subtotal - $discount;
        // $subtotal -= $discount;
        // }

        return response()->json([
            // 'items' => $items,
            'room_course' => number_format($room_course, 2),
            'subtotal' => number_format($subtotal, 2),
            'discount' => number_format($discount, 2),
            // 'tax'      => number_format($tax),
            'total'    => number_format($total),
        ]);

        return $rthc->price;
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
                    default => null,
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
