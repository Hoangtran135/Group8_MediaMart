<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Singleton Pattern: đảm bảo chỉ tồn tại một instance CartService
 * trong suốt vòng đời request, tránh khởi tạo lại giỏ hàng nhiều lần.
 *
 * Khách vãng lai: giỏ hàng lưu trong session.
 * Khách đã đăng nhập: giỏ hàng lưu trong bảng cart_items (DB) để giữ
 * được giỏ hàng khi đổi trình duyệt/thiết bị. mergeSessionCartIntoDb()
 * được gọi ngay sau khi đăng nhập để không mất giỏ hàng đang thao tác.
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

    private function customerId(): ?int
    {
        return Auth::guard('customer')->id();
    }

    public function get(): array
    {
        $customerId = $this->customerId();

        if (!$customerId) {
            return Session::get('cart', []);
        }

        $cart = [];

        foreach (CartItem::with('product')->where('customer_id', $customerId)->get() as $row) {
            if (!$row->product) {
                continue;
            }

            $cart[$row->product_id] = [
                'id'     => $row->product->id,
                'name'   => $row->product->name,
                'photo'  => $row->product->photo,
                'price'  => $row->product->final_price,
                'number' => $row->number,
            ];
        }

        return $cart;
    }

    public function add(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $customerId = $this->customerId();

        if ($customerId) {
            $row = CartItem::firstOrNew(['customer_id' => $customerId, 'product_id' => $productId]);

            if ($row->number >= $product->stock) {
                return;
            }

            $row->number = ($row->exists ? $row->number : 0) + 1;
            $row->save();

            return;
        }

        $cart = $this->get();
        $currentQty = $cart[$productId]['number'] ?? 0;

        if ($currentQty >= $product->stock) {
            return;
        }

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

    /**
     * Kiểm tra tồn kho trước khi cho phép đặt hàng.
     * @return string[] danh sách lỗi (rỗng nếu hợp lệ)
     */
    public function validateStock(): array
    {
        $errors = [];

        foreach ($this->get() as $item) {
            $product = Product::find($item['id']);

            if (!$product) {
                $errors[] = "Sản phẩm \"{$item['name']}\" không còn tồn tại.";
                continue;
            }

            if ($product->stock < $item['number']) {
                $errors[] = "\"{$product->name}\" chỉ còn {$product->stock} sản phẩm trong kho.";
            }
        }

        return $errors;
    }

    public function update(array $quantities): void
    {
        $customerId = $this->customerId();
        $cart = $this->get();

        foreach ($cart as $id => $item) {
            $qty = (int) ($quantities["product_{$id}"] ?? 0);

            if ($customerId) {
                if ($qty <= 0) {
                    CartItem::where('customer_id', $customerId)->where('product_id', $id)->delete();
                } else {
                    CartItem::where('customer_id', $customerId)->where('product_id', $id)->update(['number' => $qty]);
                }
                continue;
            }

            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['number'] = $qty;
            }
        }

        if (!$customerId) {
            Session::put('cart', $cart);
        }
    }

    public function remove(int $productId): void
    {
        $customerId = $this->customerId();

        if ($customerId) {
            CartItem::where('customer_id', $customerId)->where('product_id', $productId)->delete();
            return;
        }

        $cart = $this->get();
        unset($cart[$productId]);
        Session::put('cart', $cart);
    }

    public function clear(): void
    {
        $customerId = $this->customerId();

        if ($customerId) {
            CartItem::where('customer_id', $customerId)->delete();
            return;
        }

        Session::forget('cart');
    }

    public function total(): float
    {
        return array_sum(array_map(fn($i) => $i['price'] * $i['number'], $this->get()));
    }

    /**
     * Gộp giỏ hàng session (khách vãng lai) vào giỏ hàng DB ngay sau khi
     * đăng nhập, để không mất sản phẩm đã thêm trước khi login.
     */
    public function mergeSessionCartIntoDb(int $customerId): void
    {
        $sessionCart = Session::get('cart', []);

        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $productId => $item) {
            $product = Product::find($productId);

            if (!$product) {
                continue;
            }

            $row = CartItem::firstOrNew(['customer_id' => $customerId, 'product_id' => $productId]);
            $newQty = ($row->exists ? $row->number : 0) + (int) $item['number'];
            $row->number = min($newQty, max($product->stock, 1));
            $row->save();
        }

        Session::forget('cart');
    }
}
