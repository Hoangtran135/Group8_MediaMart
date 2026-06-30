<?php

namespace App\Services\Payment;

use App\Models\Order;
use InvalidArgumentException;

/**
 * Factory Method Pattern: định nghĩa interface PaymentMethod và factory
 * PaymentMethodFactory để tạo đối tượng thanh toán (COD/VNPay/Momo)
 * mà không cần client biết class cụ thể nào được khởi tạo.
 */
interface PaymentMethod
{
    public function code(): string;

    public function label(): string;

    public function badgeClass(): string;

    public function requiresQrPayment(): bool;

    public function buildQrUrl(Order $order): ?string;
}

abstract class AbstractQrPaymentMethod implements PaymentMethod
{
    public function requiresQrPayment(): bool
    {
        return true;
    }

    public function buildQrUrl(Order $order): ?string
    {
        $content = sprintf(
            '%s|MEDIAMART|DH%06d|%s',
            strtoupper($this->code()),
            $order->id,
            number_format($order->total, 0, '', '')
        );

        return 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' . urlencode($content);
    }
}

class CodPaymentMethod implements PaymentMethod
{
    public function code(): string
    {
        return 'cod';
    }

    public function label(): string
    {
        return 'Thanh toán khi nhận hàng (COD)';
    }

    public function badgeClass(): string
    {
        return 'payment-method-badge-cod';
    }

    public function requiresQrPayment(): bool
    {
        return false;
    }

    public function buildQrUrl(Order $order): ?string
    {
        return null;
    }
}

class VnPayPaymentMethod extends AbstractQrPaymentMethod
{
    public function code(): string
    {
        return 'vnpay';
    }

    public function label(): string
    {
        return 'VNPay';
    }

    public function badgeClass(): string
    {
        return 'payment-method-badge-vnpay';
    }
}

class MomoPaymentMethod extends AbstractQrPaymentMethod
{
    public function code(): string
    {
        return 'momo';
    }

    public function label(): string
    {
        return 'Momo';
    }

    public function badgeClass(): string
    {
        return 'payment-method-badge-momo';
    }
}

class PaymentMethodFactory
{
    public static function make(string $code): PaymentMethod
    {
        return match ($code) {
            'cod'   => new CodPaymentMethod(),
            'vnpay' => new VnPayPaymentMethod(),
            'momo'  => new MomoPaymentMethod(),
            default => throw new InvalidArgumentException("Phương thức thanh toán không hợp lệ: {$code}"),
        };
    }
}
