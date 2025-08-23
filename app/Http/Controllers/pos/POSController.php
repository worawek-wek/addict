<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\CardStocks;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderHasProduct;
use App\Models\Product;
use App\Models\Room;
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

        return view('pos.index', compact(
            'products',
            'cart',
            'subtotal',
            'discount',
            'tax',
            'total',
            'roomGroups',
            'storefrontName'
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
        $roomId   = $request->input('room_id');
        $orderId  = $request->input('order_id');
        $method   = $request->input('payment_method');
        $cash     = $request->input('cash_amount');

        $cart = Session::get('cart', []);

        if (!$roomId || !$orderId || !$method || empty($cart)) {
            return redirect()->route('pos.index')
                ->with('error', 'กรุณาเลือกห้อง Order วิธีจ่ายเงิน และมีสินค้าในตะกร้า');
        }

        $order = \App\Models\Order::findOrFail($orderId);

        foreach ($cart as $item) {
            // 1) บันทึกสินค้าใน order_has_products
            $order->products()->create([
                'ref_product_id' => $item['id'],
                'price'          => $item['price'],
                'quantity'       => $item['qty'],
            ]);

            // 2) ลด stock ของสินค้านี้ใน card_stocks (เฉพาะแถวที่ quantity != 0)
            $stock = CardStocks::where('ref_product_id', $item['id'])
                ->where('quantity', '!=', 0)
                ->first();


            if ($stock) {
                $newRemain = max(0, $stock->remain - $item['qty']);
                $stock->remain = $newRemain;
                $stock->save();   // ใช้ save() แทน update()
            }
        }


        // 3) คำนวณยอดรวม
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        $discount = 0;
        $tax      = 0;
        $total    = $subtotal - $discount + $tax;

        $order->update([
            'total_price' => $order->total_price + $total,
            'updated_at'  => now(),
        ]);

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
}
