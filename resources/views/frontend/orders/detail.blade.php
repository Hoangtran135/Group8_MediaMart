@extends('frontend.layouts.app')
@section('title', 'Chi tiết đơn hàng #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' - MediaMart')

@section('content')

<div class="page-title-bar">
    <h1><i class="fa fa-box" style="color:var(--red);margin-right:8px;"></i>
        Chi tiết đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
    </h1>
    <span class="bc ms-auto">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        <a href="{{ route('orders.index') }}">Đơn hàng</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
    </span>
</div>

<div class="row g-4">

    {{-- Cột trái: thông tin đơn + khách --}}
    <div class="col-md-4">

        {{-- Trạng thái --}}
        <div class="myorder-info-card mb-3">
            <h6 class="myorder-info-title"><i class="fa fa-info-circle me-2"></i>Thông tin đơn hàng</h6>
            <div class="myorder-info-row">
                <span>Mã đơn hàng</span>
                <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
            </div>
            <div class="myorder-info-row">
                <span>Ngày đặt</span>
                <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
            </div>
            <div class="myorder-info-row">
                <span>Trạng thái</span>
                <span class="myorder-status-badge myorder-status-{{ $order->status }}">
                    <i class="fa fa-{{ $order->status ? 'check-circle' : 'clock' }} me-1"></i>
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="myorder-info-row">
                <span>Thanh toán</span>
                <span class="myorder-pay-badge myorder-pay-{{ $order->payment_status }}">
                    <i class="fa fa-{{ $order->payment_status ? 'check' : 'hourglass-half' }} me-1"></i>
                    {{ $order->payment_status_label }}
                </span>
            </div>
        </div>

        {{-- Vận chuyển & Thanh toán --}}
        <div class="myorder-info-card mb-3">
            <h6 class="myorder-info-title"><i class="fa fa-truck me-2"></i>Vận chuyển & Thanh toán</h6>
            <div class="myorder-info-row">
                <span>Phương thức ship</span>
                <strong>{{ $order->shipping_method_label }}</strong>
            </div>
            <div class="myorder-info-row">
                <span>Phương thức TT</span>
                <strong>{{ $order->payment_method_label }}</strong>
            </div>
            @if($order->voucher_code)
            <div class="myorder-info-row">
                <span>Voucher</span>
                <strong style="color:var(--red);">{{ $order->voucher_code }}</strong>
            </div>
            @endif
        </div>

        {{-- Thông tin khách --}}
        <div class="myorder-info-card">
            <h6 class="myorder-info-title"><i class="fa fa-user me-2"></i>Thông tin giao hàng</h6>
            <div class="myorder-info-row">
                <span>Họ tên</span>
                <strong>{{ $order->customer->name }}</strong>
            </div>
            <div class="myorder-info-row">
                <span>Email</span>
                <strong>{{ $order->customer->email }}</strong>
            </div>
            <div class="myorder-info-row">
                <span>Điện thoại</span>
                <strong>{{ $order->customer->phone ?: '—' }}</strong>
            </div>
            <div class="myorder-info-row">
                <span>Địa chỉ</span>
                <strong>{{ $order->customer->address ?: '—' }}</strong>
            </div>
        </div>

    </div>

    {{-- Cột phải: danh sách sản phẩm + tổng tiền --}}
    <div class="col-md-8">
        <div class="myorder-info-card">
            <h6 class="myorder-info-title"><i class="fa fa-shopping-cart me-2"></i>Sản phẩm đã đặt</h6>

            @foreach($order->details as $detail)
            <div class="myorder-detail-item">
                <img src="{{ $detail->product->photo_url ?? asset('images/no-image.png') }}"
                     alt="{{ $detail->product->name ?? '' }}"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'70\' height=\'70\'%3E%3Crect width=\'70\' height=\'70\' fill=\'%23f5f5f5\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%23ccc\' font-size=\'28\'%3E📦%3C/text%3E%3C/svg%3E'">
                <div class="myorder-detail-info">
                    <div class="myorder-detail-name">
                        @if($detail->product)
                            <a href="{{ route('products.detail', $detail->product->id) }}">
                                {{ $detail->product->name }}
                            </a>
                        @else
                            <em style="color:var(--gray-500);">Sản phẩm đã xóa</em>
                        @endif
                    </div>
                    <div class="myorder-detail-price">
                        {{ number_format($detail->price) }}₫ × {{ $detail->number }}
                    </div>
                </div>
                <div class="myorder-detail-subtotal">
                    {{ number_format($detail->price * $detail->number) }}₫
                </div>
            </div>
            @endforeach

            {{-- Tổng kết --}}
            <div class="myorder-summary">
                <div class="myorder-summary-row">
                    <span>Tạm tính</span>
                    <span>{{ number_format($order->subtotal) }}₫</span>
                </div>
                <div class="myorder-summary-row">
                    <span>Phí vận chuyển</span>
                    <span>
                        @if($order->shipping_fee > 0)
                            {{ number_format($order->shipping_fee) }}₫
                        @else
                            <span style="color:#2e7d32;font-weight:600;">Miễn phí</span>
                        @endif
                    </span>
                </div>
                @if($order->discount_amount > 0)
                <div class="myorder-summary-row" style="color:var(--red);">
                    <span>Giảm giá ({{ $order->voucher_code }})</span>
                    <span>-{{ number_format($order->discount_amount) }}₫</span>
                </div>
                @endif
                <div class="myorder-summary-total">
                    <span>Tổng cộng</span>
                    <span>{{ number_format($order->total) }}₫</span>
                </div>
            </div>
        </div>

        {{-- Nút hành động --}}
        <div class="d-flex gap-3 mt-3 flex-wrap">
            <a href="{{ route('orders.index') }}" class="btn-myorder-back">
                <i class="fa fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
            @if($order->payment_status === 0 && $order->payment_method !== 'cod')
                <a href="{{ route('payment.show', $order->id) }}" class="btn-myorder-pay">
                    <i class="fa fa-qrcode me-2"></i>Thanh toán ngay
                </a>
            @endif
            <a href="{{ route('products.index') }}" class="btn-myorder-continue">
                <i class="fa fa-shopping-bag me-2"></i>Tiếp tục mua sắm
            </a>
        </div>
    </div>

</div>
@endsection
