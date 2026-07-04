@extends('admin.layouts.app')
@section('title', 'Quản lý khách hàng')

@section('content')
<div class="card">
    <div class="card-header">
        Danh sách khách hàng
    </div>
    <div class="card-body" style="padding:12px 20px;border-bottom:1px solid #eee;">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:10px;">{{ session('success') }}</div>
        @endif
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" style="max-width:300px;"
                   placeholder="Tìm theo tên, email, sđt...">
            <button type="submit" class="btn btn-default">Tìm</button>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="table table-hover" style="margin:0;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Số đơn đã đặt</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td>
                        @if($customer->is_active)
                            <span class="label label-success">Hoạt động</span>
                        @else
                            <span class="label label-danger">Đã khóa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-xs btn-info">Chi tiết</a>
                        <form action="{{ route('admin.customers.toggle-active', $customer->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('{{ $customer->is_active ? 'Khóa' : 'Mở khóa' }} tài khoản này?')">
                            @csrf
                            <button class="btn btn-xs {{ $customer->is_active ? 'btn-danger' : 'btn-success' }}">
                                {{ $customer->is_active ? 'Khóa' : 'Mở khóa' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">Không có khách hàng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $customers->links() }}</div>
</div>
@endsection
