<?php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Event;

class OrderService
{
    public function deliver(int $orderId): void
    {
        $this->updateStatus($orderId, Order::STATUS_DELIVERED);
    }

    public function updateStatus(int $orderId, int $newStatus): void
    {
        $order = Order::with('details')->findOrFail($orderId);
        $old   = $order->status;

        if ($old === $newStatus) {
            return;
        }

        $order->update(['status' => $newStatus]);

        // Trừ stock khi xác nhận đơn
        if ($old === Order::STATUS_PENDING && $newStatus === Order::STATUS_CONFIRMED) {
            foreach ($order->details as $detail) {
                Product::where('id', $detail->product_id)
                    ->where('stock', '>', 0)
                    ->decrement('stock', $detail->number);
            }
        }

        // Hoàn stock khi huỷ đơn đã xác nhận
        if (in_array($old, [Order::STATUS_CONFIRMED, Order::STATUS_SHIPPING])
            && $newStatus === Order::STATUS_CANCELLED) {
            foreach ($order->details as $detail) {
                Product::where('id', $detail->product_id)
                    ->increment('stock', $detail->number);
            }
        }

        // Observer: thông báo cho khách hàng qua email
        Event::dispatch(new OrderStatusChanged($order, $old, $newStatus));
    }
}
