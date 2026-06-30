<?php

namespace App\Services\Shipping;

use App\Support\SiteSettings;
use InvalidArgumentException;

/**
 * Strategy Pattern: mỗi hình thức vận chuyển (Tiêu chuẩn / Hỏa tốc /
 * Miễn phí) là một Strategy riêng, có thể hoán đổi qua ShippingFeeCalculator.
 * Kết hợp với Adapter Pattern để lấy phí thực tế từ GHN/GHTK.
 */
interface ShippingFeeStrategy
{
    public function code(): string;

    public function label(): string;

    public function calculate(int $subtotal): int;
}

class StandardShippingStrategy implements ShippingFeeStrategy
{
    public function __construct(private ShippingProvider $provider = new GhtkShippingAdapter()) {}

    public function code(): string
    {
        return 'standard';
    }

    public function label(): string
    {
        return 'Giao hàng tiêu chuẩn (' . $this->provider->getName() . ')';
    }

    public function calculate(int $subtotal): int
    {
        return $this->provider->getFee($subtotal);
    }
}

class ExpressShippingStrategy implements ShippingFeeStrategy
{
    public function __construct(private ShippingProvider $provider = new GhnShippingAdapter()) {}

    public function code(): string
    {
        return 'express';
    }

    public function label(): string
    {
        return 'Giao hàng hỏa tốc (' . $this->provider->getName() . ')';
    }

    public function calculate(int $subtotal): int
    {
        $fee = $this->provider->getFee($subtotal);

        // Phụ phí hỏa tốc
        return $fee + 15000;
    }
}

class FreeShippingStrategy implements ShippingFeeStrategy
{
    public function code(): string
    {
        return 'free';
    }

    public function label(): string
    {
        return 'Miễn phí vận chuyển';
    }

    public function calculate(int $subtotal): int
    {
        return 0;
    }
}

class ShippingFeeCalculator
{
    public static function make(string $code): ShippingFeeStrategy
    {
        return match ($code) {
            'standard' => new StandardShippingStrategy(),
            'express'  => new ExpressShippingStrategy(),
            'free'     => new FreeShippingStrategy(),
            default    => throw new InvalidArgumentException("Phương thức vận chuyển không hợp lệ: {$code}"),
        };
    }

    /** @return array<int, ShippingFeeStrategy> */
    public static function all(): array
    {
        return [
            new StandardShippingStrategy(),
            new ExpressShippingStrategy(),
        ];
    }

    /**
     * Nếu đơn hàng đạt ngưỡng freeship (cấu hình trong SiteSettings)
     * thì luôn áp dụng FreeShippingStrategy bất kể lựa chọn ban đầu.
     */
    public static function resolve(string $code, int $subtotal): ShippingFeeStrategy
    {
        if ($subtotal >= SiteSettings::getInstance()->freeshipThreshold()) {
            return new FreeShippingStrategy();
        }

        return self::make($code);
    }
}
