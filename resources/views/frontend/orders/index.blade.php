@extends('frontend.layouts.app')
@section('title', 'Đơn hàng của tôi - MediaMart')

@section('content')

<div class="page-title-bar">
    <h1><i class="fa fa-box" style="color:var(--red);margin-right:8px;"></i>Đơn hàng của tôi</h1>
    <span class="bc ms-auto">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        Đơn hàng
    </span>
</div>

@if($orders->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <p>Bạn chưa có đơn hàng nào.</p>
        <a href="{{ route('products.index') }}" class="btn-continue">
            <i class="fa fa-shopping-bag me-2"></i>Mua sắm ngay
        </a>
    </div>
@else
    <div class="myorder-list">
        @foreach($orders as $order)
        <div class="myorder-card">
            {{-- Header --}}
            <div class="myorder-card-header">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="myorder-id">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                    <span class="myorder-date">
                        <i class="fa fa-calendar me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="myorder-status-badge myorder-status-{{ $order->status }}">
                        <i class="fa fa-{{ $order->status ? 'check-circle' : 'clock' }} me-1"></i>
                        {{ $order->status_label }}
                    </span>
                    <span class="myorder-pay-badge myorder-pay-{{ $order->payment_status }}">
                        <i class="fa fa-{{ $order->payment_status ? 'check' : 'hourglass-half' }} me-1"></i>
                        {{ $order->payment_status_label }}
                    </span>
                </div>
            </div>

            {{-- Products preview --}}
            <div class="myorder-products">
                @foreach($order->details->take(3) as $detail)
                <div class="myorder-product-item">
                    <img src="{{ $detail->product->photo_url ?? asset('images/no-image.png') }}"
                         alt="{{ $detail->product->name ?? '' }}"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\'%3E%3Crect width=\'60\' height=\'60\' fill=\'%23f5f5f5\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ccc\' font-size=\'24\'%3E📦%3C/text%3E%3C/svg%3E'">
                    <div class="myorder-product-info">
                        <span class="myorder-product-name">
                            {{ $detail->product->name ?? 'Sản phẩm đã xóa' }}
                        </span>
                        <span class="myorder-product-qty">x{{ $detail->number }}</span>
                    </div>
                    <span class="myorder-product-price">{{ number_format($detail->price * $detail->number) }}₫</span>
                </div>
                @endforeach

                @if($order->details->count() > 3)
                    <div class="myorder-more">
                        + {{ $order->details->count() - 3 }} sản phẩm khác
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="myorder-card-footer">
                <div class="myorder-meta">
                    <span><i class="fa fa-truck me-1" style="color:var(--gray-500);"></i>{{ $order->shipping_method_label }}</span>
                    <span><i class="fa fa-credit-card me-1" style="color:var(--gray-500);"></i>{{ $order->payment_method_label }}</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="myorder-total">
                        Tổng: <strong>{{ number_format($order->total) }}₫</strong>
                    </div>
                    <a href="{{ route('orders.detail', $order->id) }}" class="btn-myorder-detail">
                        <i class="fa fa-eye me-1"></i>Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
@endif

@endsection
