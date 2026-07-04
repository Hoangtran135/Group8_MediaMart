<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');

        $customers = Customer::withCount('orders')
            ->when($keyword, fn ($q) => $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%"))
            ->latest()
            ->paginate(25);

        return view('admin.customers.index', compact('customers', 'keyword'));
    }

    public function show(int $id)
    {
        $customer = Customer::with(['orders' => fn ($q) => $q->latest()])->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggleActive(int $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['is_active' => ! $customer->is_active]);

        $message = $customer->is_active
            ? 'Đã mở khóa tài khoản khách hàng.'
            : 'Đã khóa tài khoản khách hàng.';

        return back()->with('success', $message);
    }
}
