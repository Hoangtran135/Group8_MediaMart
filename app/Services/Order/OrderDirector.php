<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderDetail;

/**
 * Builder Pattern: OrderBuilder dựng đơn hàng (Order + OrderDetail)
 * qua các bước fluent: forCustomer → withPaymentMethod → withShipping
 * → withVoucher → addItemsFromCart → build().
 * File được đặt tên OrderDirector.php (Director điều phối Builder).
 */
class OrderBuilder
{
    private ?int $customerId = null;

    private string $paymentMethod = 'cod';

    private string $shippingMethod = 'standard';

    private int $shippingFee = 0;

    private ?string $voucherCode = null;

    private int $discountAmount = 0;

    /** @var array<int, array{id:int, number:int, price:float}> */
    private array $items = [];

    public static function new(): self
    {
        return new self();
    }

    public function forCustomer(?int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function withPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function withShipping(string $shippingMethod, int $shippingFee): self
    {
        $this->shippingMethod = $shippingMethod;
        $this->shippingFee    = $shippingFee;

        return $this;
    }

    public function withVoucher(?string $voucherCode, int $discountAmount): self
    {
        $this->voucherCode    = $voucherCode;
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function addItem(int $productId, int $number, float $price): self
    {
        $this->items[] = ['id' => $productId, 'number' => $number, 'price' => $price];

        return $this;
    }

    /** @param array<int|string, array{id:int, number:int, price:float}> $cart */
    public function addItemsFromCart(array $cart): self
    {
        foreach ($cart as $item) {
            $this->addItem((int) $item['id'], (int) $item['number'], (float) $item['price']);
        }

        return $this;
    }

    public function build(): Order
    {
        $order = Order::create([
            'customer_id'     => $this->customerId,
            'status'          => 0,
            'payment_method'  => $this->paymentMethod,
            'payment_status'  => 0,
            'shipping_method' => $this->shippingMethod,
            'shipping_fee'    => $this->shippingFee,
            'voucher_code'    => $this->voucherCode,
            'discount_amount' => $this->discountAmount,
        ]);

        foreach ($this->items as $item) {
            OrderDetail::create([
                'order_id'   => $order->id,
                'product_id' => $item['id'],
                'number'     => $item['number'],
                'price'      => $item['price'],
            ]);
        }

        return $order;
    }
}
