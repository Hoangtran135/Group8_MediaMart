<?php

namespace App\Events;

use App\Models\Order;

/**
 * Observer Pattern: phát ra khi admin cập nhật trạng thái đơn hàng,
 * để các listener (email/sms...) tự động phản ứng.
 */
class OrderStatusChanged
{
    public function __construct(public Order $order, public int $oldStatus, public int $newStatus) {}
}
