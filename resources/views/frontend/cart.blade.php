@extends('frontend.layouts.app')
@section('title', 'Giỏ hàng - MediaMart')

@section('content')
<div class="page-title-bar">
    <h1><i class="fa fa-shopping-cart" style="color:var(--red);margin-right:8px;"></i>Giỏ hàng của bạn</h1>
    @if(!empty($cart))
        <span class="bc ms-auto">{{ count($cart) }} sản phẩm</span>
    @endif
</div>

@if(empty($cart))
    <div class="cart-wrap empty-state">
        <div class="empty-icon">🛒</div>
        <p>Giỏ hàng của bạn đang trống.</p>
        <a href="{{ route('home') }}" class="btn-continue">Tiếp tục mua sắm</a>
    </div>
@else
<div class="cart-wrap">
    <form action="{{ route('cart.update') }}" method="POST">
        @csrf
        <div style="overflow-x:auto;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $id => $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('uploads/products/' . $item['photo']) }}" class="cart-thumb" onerror="this.src='https://via.placeholder.com/56'">
                                <a href="{{ route('products.detail', $id) }}" style="font-weight:500;font-size:14px;">{{ $item['name'] }}</a>
                            </div>
                        </td>
                        <td style="color:var(--red);font-weight:600;">{{ number_format($item['price']) }}₫</td>
                        <td>
                            <input type="number" name="product_{{ $id }}" value="{{ $item['number'] }}"
                                   min="0" class="cart-qty">
                        </td>
                        <td style="font-weight:700;">{{ number_format($item['price'] * $item['number']) }}₫</td>
                        <td>
                            {{-- Nút submit form="remove-form-{{ $id }}" bên ngoài, tránh lồng
                                 <form> trong <form> (HTML không hợp lệ, gây submit sai method). --}}
                            <button type="submit" form="remove-form-{{ $id }}" class="btn-remove" title="Xóa">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="cart-total-row">
                        <td colspan="3" style="text-align:right;padding-right:20px;">Tổng cộng:</td>
                        <td colspan="2"><span class="cart-total-price">{{ number_format($total) }}₫</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
            <div class="d-flex gap-2">
                <button type="submit" class="btn-cart-update">
                    <i class="fa fa-sync-alt me-1"></i> Cập nhật giỏ
                </button>
                <a href="{{ route('home') }}" class="btn-cart-update" style="text-decoration:none;">
                    <i class="fa fa-arrow-left me-1"></i> Tiếp tục mua
                </a>
            </div>
        </div>
    </form>

    @foreach($cart as $id => $item)
        <form id="remove-form-{{ $id }}" action="{{ route('cart.remove', $id) }}" method="POST" class="d-none">
            @csrf @method('DELETE')
        </form>
    @endforeach

    {{-- Phương thức vận chuyển + thanh toán + voucher --}}
    <div class="payment-method-box mt-4">
        <form action="{{ route('cart.checkout') }}" method="POST">
            @csrf

            <h5 style="font-weight:700;margin-bottom:14px;">Phương thức vận chuyển</h5>
            <div class="payment-method-list mb-3">
                @foreach($shippingOptions as $i => $option)
                <label class="payment-method-item">
                    <input type="radio" name="shipping_method" value="{{ $option['code'] }}" {{ $i === 0 ? 'checked' : '' }}>
                    <span class="payment-method-icon"><i class="fa fa-truck"></i></span>
                    <span>
                        <strong>{{ $option['label'] }}</strong>
                        <small>Phí: {{ number_format($option['fee']) }}₫ (miễn phí nếu đơn từ 500.000₫)</small>
                    </span>
                </label>
                @endforeach
            </div>

            <h5 style="font-weight:700;margin-bottom:14px;">Mã giảm giá</h5>
            <div class="mb-3">
                <input type="text" name="voucher_code" class="form-control" placeholder="Nhập mã voucher (VD: GIAM10, GIAM50K, FREESHIP)">
            </div>

            <h5 style="font-weight:700;margin-bottom:14px;">Phương thức thanh toán</h5>
            <div class="payment-method-list">
                <label class="payment-method-item">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span class="payment-method-icon"><i class="fa fa-money-bill-wave"></i></span>
                    <span>
                        <strong>Thanh toán khi nhận hàng (COD)</strong>
                        <small>Thanh toán bằng tiền mặt khi nhận sản phẩm</small>
                    </span>
                </label>
                <label class="payment-method-item">
                    <input type="radio" name="payment_method" value="vnpay">
                    <span class="payment-method-icon" style="color:#0066b3;"><i class="fa fa-qrcode"></i></span>
                    <span>
                        <strong>VNPay</strong>
                        <small>Quét mã QR để thanh toán qua VNPay (demo)</small>
                    </span>
                </label>
                <label class="payment-method-item">
                    <input type="radio" name="payment_method" value="momo">
                    <span class="payment-method-icon" style="color:#a50064;"><i class="fa fa-qrcode"></i></span>
                    <span>
                        <strong>Ví Momo</strong>
                        <small>Quét mã QR để thanh toán qua Momo (demo)</small>
                    </span>
                </label>
            </div>

            <button type="submit" class="btn-checkout mt-3">
                <i class="fa fa-credit-card me-1"></i> Đặt hàng ngay
            </button>
        </form>
    </div>
</div>
@endif
@endsection
