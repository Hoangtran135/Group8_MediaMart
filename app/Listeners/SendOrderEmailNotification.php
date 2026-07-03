<?php

namespace App\Listeners;
use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Observer: gửi email xác nhận đơn hàng sau khi đặt hàng thành công.
 */
class SendOrderEmailNotification
{
    public function handle(OrderPlaced $event): void
    {
        $order    = $event->order->load(['customer', 'details.product']);
        $customer = $order->customer;

        if (!$customer?->email) {
            return;
        }

        try {
            Mail::to($customer->email)->send(new OrderConfirmationMail($order));
            Log::info("[Email] Đã gửi xác nhận đơn #{$order->id} tới {$customer->email}");
        } catch (\Exception $e) {
            Log::error("[Email] Lỗi gửi email đơn #{$order->id}: " . $e->getMessage());
        }
    }
}
