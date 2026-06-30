<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

/**
 * Singleton Pattern: đảm bảo chỉ tồn tại một instance CartService
 * trong suốt vòng đời request, tránh khởi tạo lại giỏ hàng nhiều lần.
 */
class CartService
{
    private static ?CartService $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get(): array
    {
        return Session::get('cart', []);
    }

    public function add(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $cart    = $this->get();

        if (isset($cart[$productId])) {
            $cart[$productId]['number']++;
        } else {
            $cart[$productId] = [
                'id'     => $product->id,
                'name'   => $product->name,
                'photo'  => $product->photo,
                'price'  => $product->final_price,
                'number' => 1,
            ];
        }

        Session::put('cart', $cart);
    }

    public function update(array $quantities): void
    {
        $cart = $this->get();

        foreach ($cart as $id => $item) {
            $qty = (int) ($quantities["product_{$id}"] ?? 0);

            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['number'] = $qty;
            }
        }

        Session::put('cart', $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->get();
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    public function total(): float
    {
        return array_sum(array_map(fn($i) => $i['price'] * $i['number'], $this->get()));
    }
}
