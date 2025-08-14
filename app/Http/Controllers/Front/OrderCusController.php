<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderCusController extends Controller
{
    public function index()
    {
        // สมมติว่าคุณใช้ Auth::user() แล้วมี customer id ใน user object
        $customerId = Auth::user()->id;

        $orders = Order::with(['user', 'branch', 'room', 'status', 'addons'])
        ->where('ref_customer_id', $customerId)
        ->latest()
        ->get();
        return view('frontend.orders.index', compact('orders'));
    }
}
