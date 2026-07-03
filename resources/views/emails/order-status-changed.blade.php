<x-mail::message>
# Cập nhật đơn hàng #{{ $order->id }}

Xin chào **{{ $order->customer->name ?? 'Quý khách' }}**,

Đơn hàng của bạn vừa được cập nhật trạng thái mới:

## {{ $order->status_label }}

<x-mail::button :url="route('orders.detail', $order->id)">
Xem chi tiết đơn hàng
</x-mail::button>

Trân trọng,
**{{ config('app.name') }}**
</x-mail::message>
