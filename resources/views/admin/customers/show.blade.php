@extends('admin.layouts.app')
@section('title', 'Chi tiết khách hàng #' . $customer->id)

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Thông tin khách hàng</div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $customer->name }}</p>
                <p><strong>Email:</strong> {{ $customer->email }}</p>
                <p><strong>Điện thoại:</strong> {{ $customer->phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $customer->address }}</p>
                <p><strong>Ngày đăng ký:</strong> {{ $customer->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Trạng thái:</strong>
                    @if($customer->is_active)
                        <span class="label label-success">Hoạt động</span>
                    @else
                        <span class="label label-danger">Đã khóa</span>
                    @endif
                </p>
                <form action="{{ route('admin.customers.toggle-active', $customer->id) }}" method="POST"
                      onsubmit="return confirm('{{ $customer->is_active ? 'Khóa' : 'Mở khóa' }} tài khoản này?')">
                    @csrf
                    <button class="btn btn-block {{ $customer->is_active ? 'btn-danger' : 'btn-success' }}">
                        {{ $customer->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}
                    </button>
                </form>
            </div>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-default" style="margin-top:10px;">← Quay lại</a>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Lịch sử đơn hàng ({{ $customer->orders->count() }})</div>
            <div class="card-body" style="padding:0;">
                <table class="table table-hover" style="margin:0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($order->total) }}₫</td>
                            <td><span class="label label-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td><a href="{{ route('admin.orders.detail', $order->id) }}" class="btn btn-xs btn-info">Xem</a></td>
                        </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Khách hàng chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
