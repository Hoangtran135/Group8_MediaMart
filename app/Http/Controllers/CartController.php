<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutFacade;
use App\Services\Shipping\ShippingFeeCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        // Singleton: lấy instance duy nhất thay vì inject mới
        $this->cartService = CartService::getInstance();
    }

    private CartService $cartService;

    public function index()
    {
        $cart  = $this->cartService->get();
        $total = $this->cartService->total();

        $shippingOptions = array_map(
            fn ($strategy) => [
                'code'  => $strategy->code(),
                'label' => $strategy->label(),
                'fee'   => $strategy->calculate((int) $total),
            ],
            ShippingFeeCalculator::all(),
        );

        return view('frontend.cart', compact('cart', 'total', 'shippingOptions'));
    }

    public function add(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $this->cartService->add($id);
        $cartCount = count($this->cartService->get());

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "Đã thêm \"{$product->name}\" vào giỏ hàng!",
                'cart_count' => $cartCount,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request)
    {
        $this->cartService->update($request->all());

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function remove(int $id)
    {
        $this->cartService->remove($id);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm!');
    }

    public function checkout(Request $request)
    {
        $cart = $this->cartService->get();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $stockErrors = $this->cartService->validateStock();

        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')->with('error', implode(' ', $stockErrors));
        }

        $result = CheckoutFacade::placeOrder(
            cart: $cart,
            customerId: Auth::guard('customer')->id(),
            paymentMethodCode: $request->input('payment_method', 'cod'),
            shippingMethodCode: $request->input('shipping_method', 'standard'),
            voucherCode: $request->input('voucher_code'),
        );

        $this->cartService->clear();

        if ($result['paymentMethod']->requiresQrPayment()) {
            return redirect()->route('payment.show', $result['order']->id);
        }

        return redirect()->route('home')->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ sớm.');
    }
}
