<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $totalCustomers = Customer::count();
        $totalRevenue = Order::where('status', Order::STATUS_DELIVERED)->get()->sum('total');
        $recentOrders = Order::with('customer')->latest()->take(10)->get();

        $revenueChartData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('m/Y');
            $amount = Order::where('status', Order::STATUS_DELIVERED)
                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])
                ->get()
                ->sum('total');
            $revenueChartData->push(['label' => $label, 'amount' => $amount]);
        }

        $topProducts = OrderDetail::select('product_id', DB::raw('SUM(number) as total_sold'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $statusStats = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.dashboard', compact(
            'totalProducts', 'totalOrders', 'pendingOrders', 'totalCustomers',
            'totalRevenue', 'recentOrders', 'revenueChartData', 'topProducts', 'statusStats'
        ));
    }
}
