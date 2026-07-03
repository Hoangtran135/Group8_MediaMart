<?php

namespace App\Events;
use App\Models\Order;

/**
 * Observer Pattern: OrderPlaced là "subject" được phát ra khi một đơn
 * hàng được tạo thành công. Các listener (observer) đăng ký lắng nghe
 * sự kiện này sẽ tự động được gọi để gửi Email/SMS/thông báo.
 */
class OrderPlaced
{
    public function __construct(public Order $order) {}
}
