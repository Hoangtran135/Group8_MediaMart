<?php

namespace App\Services\Shipping;

/**
 * Adapter Pattern: GhnShippingAdapter và GhtkShippingAdapter "bọc"
 * các API/SDK có interface khác nhau (GHN trả mảng, GHTK trả float)
 * thành một interface chung ShippingProvider::getFee().
 */
interface ShippingProvider
{
    public function getName(): string;

    public function getFee(int $orderTotal): int;
}

class GhnApiClient
{
    public function calculateFee(array $payload): array
    {
        $orderTotal = $payload['order_value'] ?? 0;

        return [
            'code' => 200,
            'data' => [
                'shipping_fee' => $orderTotal >= 500000 ? 0 : 35000,
            ],
        ];
    }
}

class GhtkApiSdk
{
    public function estimateShippingCost(float $orderAmount): float
    {
        return $orderAmount >= 500000 ? 0.0 : 25000.0;
    }
}

class GhnShippingAdapter implements ShippingProvider
{
    public function __construct(private GhnApiClient $client = new GhnApiClient()) {}

    public function getName(): string
    {
        return 'GHN (Giao hàng nhanh)';
    }

    public function getFee(int $orderTotal): int
    {
        $result = $this->client->calculateFee(['order_value' => $orderTotal]);

        return (int) $result['data']['shipping_fee'];
    }
}

class GhtkShippingAdapter implements ShippingProvider
{
    public function __construct(private GhtkApiSdk $client = new GhtkApiSdk()) {}

    public function getName(): string
    {
        return 'GHTK (Giao hàng tiết kiệm)';
    }

    public function getFee(int $orderTotal): int
    {
        return (int) $this->client->estimateShippingCost((float) $orderTotal);
    }
}
