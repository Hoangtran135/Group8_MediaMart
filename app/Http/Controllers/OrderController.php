<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['details.product'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    public function detail(int $id)
    {
        $order = Order::with(['details.product'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($id);

        return view('frontend.orders.detail', compact('order'));
    }
}
