<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // โหลด products + stock รวมคงเหลือ
        $products = Product::with('latestStock')->get();

        $cart = Session::get('cart', []);

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        $discount = 0;
        $tax = 0;
        $total = $subtotal - $discount + $tax;

        return view('pos.index', compact('products', 'cart', 'subtotal', 'discount', 'tax', 'total'));
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
    public function updateCart(Request $request, $id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            $qty = $request->input('qty');
            if ($qty > 0) {
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
