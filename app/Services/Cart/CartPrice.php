<?php

namespace App\Services\Cart;

/**
 * Decorator Pattern: BaseCartPrice là thành phần gốc (giá thuần).
 * Các Decorator (PercentDiscount, AmountDiscount, Freeship) “bọc”
 * thêm từng loại khuyến mãi mà không sửa class gốc, có thể
 * chồng nhiều lớp lên nhau.
 */
interface CartPriceComponent
{
    public function getSubtotal(): int;

    public function getShippingFee(): int;

    public function getDiscount(): int;

    public function getTotal(): int;

    public function getDescription(): string;
}

/**
 * Thành phần gốc: tổng tiền hàng + phí vận chuyển, chưa áp khuyến mãi.
 */
class BaseCartPrice implements CartPriceComponent
{
    public function __construct(
        private int $subtotal,
        private int $shippingFee,
    ) {}

    public function getSubtotal(): int
    {
        return $this->subtotal;
    }

    public function getShippingFee(): int
    {
        return $this->shippingFee;
    }

    public function getDiscount(): int
    {
        return 0;
    }

    public function getTotal(): int
    {
        return $this->subtotal + $this->shippingFee;
    }

    public function getDescription(): string
    {
        return 'Tổng tiền hàng + phí vận chuyển';
    }
}

abstract class CartPriceDecorator implements CartPriceComponent
{
    public function __construct(protected CartPriceComponent $inner) {}

    public function getSubtotal(): int
    {
        return $this->inner->getSubtotal();
    }

    public function getShippingFee(): int
    {
        return $this->inner->getShippingFee();
    }

    public function getDiscount(): int
    {
        return $this->inner->getDiscount();
    }

    public function getTotal(): int
    {
        return max(0, $this->getSubtotal() + $this->getShippingFee() - $this->getDiscount());
    }
}

/**
 * Giảm theo phần trăm trên tổng tiền hàng. Ví dụ: voucher GIAM10 -> -10%.
 */
class PercentDiscountDecorator extends CartPriceDecorator
{
    public function __construct(CartPriceComponent $inner, private int $percent, private string $voucherCode)
    {
        parent::__construct($inner);
    }

    public function getDiscount(): int
    {
        $extra = (int) round($this->getSubtotal() * $this->percent / 100);

        return $this->inner->getDiscount() + $extra;
    }

    public function getDescription(): string
    {
        return "Voucher {$this->voucherCode}: giảm {$this->percent}% tổng đơn";
    }
}

/**
 * Giảm một số tiền cố định. Ví dụ: voucher GIAM50K -> -50.000đ.
 */
class AmountDiscountDecorator extends CartPriceDecorator
{
    public function __construct(CartPriceComponent $inner, private int $amount, private string $voucherCode)
    {
        parent::__construct($inner);
    }

    public function getDiscount(): int
    {
        return $this->inner->getDiscount() + $this->amount;
    }

    public function getDescription(): string
    {
        return "Voucher {$this->voucherCode}: giảm " . number_format($this->amount) . 'đ';
    }
}

/**
 * Miễn phí vận chuyển: phí ship được "che" thành 0.
 */
class FreeshipDecorator extends CartPriceDecorator
{
    public function __construct(CartPriceComponent $inner, private string $voucherCode)
    {
        parent::__construct($inner);
    }

    public function getShippingFee(): int
    {
        return 0;
    }

    public function getDescription(): string
    {
        return "Voucher {$this->voucherCode}: miễn phí vận chuyển";
    }
}
