<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Services\Cart\AmountDiscountDecorator;
use App\Services\Cart\BaseCartPrice;
use App\Services\Cart\CartPriceComponent;
use App\Services\Cart\FreeshipDecorator;
use App\Services\Cart\PercentDiscountDecorator;
use App\Services\Order\OrderBuilder;       
use App\Services\Payment\PaymentMethod;    
use App\Services\Payment\PaymentMethodFactory; // PaymentMethod.php
use App\Services\Shipping\ShippingFeeCalculator; // ShippingStrategy.php
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Event;

/**
 * Facade Pattern: gói gọn toàn bộ quy trình checkout (tính phí ship
 * theo Strategy/Adapter, áp voucher theo Decorator, dựng đơn hàng theo
 * Builder, chọn phương thức thanh toán theo Factory Method, bắn sự kiện
 * theo Observer) đằng sau một phương thức duy nhất: placeOrder().
 *
 * Controller chỉ cần gọi CheckoutFacade::placeOrder(...) mà không cần
 * biết các bước xử lý phức tạp bên trong.
 */
class CheckoutFacade
{
    /**
     * @param array<int|string, array{id:int, number:int, price:float}> $cart
     * @return array{order: Order, paymentMethod: PaymentMethod, priceBreakdown: CartPriceComponent}
     */
    public static function placeOrder(
        array $cart,
        ?int $customerId,
        string $paymentMethodCode,
        string $shippingMethodCode,
        ?string $voucherCode,
    ): array {
        $subtotal = (int) array_sum(array_map(fn ($i) => $i['price'] * $i['number'], $cart));

        // 1. Strategy + Adapter: chọn chiến lược & nhà vận chuyển để tính phí ship
        $shippingStrategy = ShippingFeeCalculator::resolve($shippingMethodCode, $subtotal);
        $shippingFee      = $shippingStrategy->calculate($subtotal);

        // 2. Decorator: áp voucher (nếu có) lên tổng tiền
        $priceBreakdown = self::applyVoucher(new BaseCartPrice($subtotal, $shippingFee), $voucherCode);

        // 3. Factory Method: tạo đối tượng phương thức thanh toán
        $paymentMethod = PaymentMethodFactory::make($paymentMethodCode);

        // 4. Builder: dựng đơn hàng + chi tiết đơn hàng
        $order = OrderBuilder::new()
            ->forCustomer($customerId)
            ->withPaymentMethod($paymentMethod->code())
            ->withShipping($shippingStrategy->code(), $priceBreakdown->getShippingFee())
            ->withVoucher($voucherCode, $priceBreakdown->getDiscount())
            ->addItemsFromCart($cart)
            ->build();

        // 5. Observer: thông báo cho các listener (gửi email/sms...)
        Event::dispatch(new OrderPlaced($order));

        return [
            'order'          => $order,
            'paymentMethod'  => $paymentMethod,
            'priceBreakdown' => $priceBreakdown,
        ];
    }

    private static function applyVoucher(CartPriceComponent $price, ?string $voucherCode): CartPriceComponent
    {
        $voucher = SiteSettings::getInstance()->findVoucher($voucherCode);

        if ($voucher === null || $voucherCode === null) {
            return $price;
        }

        $code = strtoupper(trim($voucherCode));

        return match ($voucher['type']) {
            'percent'  => new PercentDiscountDecorator($price, $voucher['value'], $code),
            'amount'   => new AmountDiscountDecorator($price, $voucher['value'], $code),
            'freeship' => new FreeshipDecorator($price, $code),
            default    => $price,
        };
    }
}
