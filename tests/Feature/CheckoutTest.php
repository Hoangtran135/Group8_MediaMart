<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $overrides = []): Product
    {
        $category = Category::create([
            'parent_id' => 0,
            'name' => 'Điện thoại',
            'displayhomepage' => 0,
        ]);

        return Product::create(array_merge([
            'name' => 'Sản phẩm test',
            'description' => 'Mô tả',
            'content' => 'Nội dung',
            'hot' => 0,
            'photo' => 'test.jpg',
            'price' => 1000000,
            'discount' => 0,
            'stock' => 10,
            'category_id' => $category->id,
        ], $overrides));
    }

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'name' => 'Nguyễn Test',
            'email' => 'test@example.com',
            'address' => 'Hà Nội',
            'phone' => '0900000000',
            'password' => 'password',
        ]);
    }

    public function test_customer_can_checkout_cart_with_cod(): void
    {
        $customer = $this->makeCustomer();
        $product  = $this->makeProduct();

        $this->actingAs($customer, 'customer');

        $this->post(route('cart.add', $product->id));

        $response = $this->post(route('cart.checkout'), [
            'payment_method'  => 'cod',
            'shipping_method' => 'standard',
        ]);

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'payment_method' => 'cod',
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('order_details', [
            'product_id' => $product->id,
            'number' => 1,
        ]);
    }

    public function test_checkout_blocked_when_cart_empty(): void
    {
        $customer = $this->makeCustomer();
        $this->actingAs($customer, 'customer');

        $response = $this->post(route('cart.checkout'), [
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_adding_to_cart_is_capped_at_available_stock(): void
    {
        $customer = $this->makeCustomer();
        $product  = $this->makeProduct(['stock' => 1]);

        $this->actingAs($customer, 'customer');

        // adding twice should be capped at stock (1) by CartService::add()
        $this->post(route('cart.add', $product->id));
        $this->post(route('cart.add', $product->id));

        $this->assertDatabaseHas('cart_items', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'number' => 1,
        ]);
    }

    public function test_checkout_blocked_when_stock_runs_out_before_checkout(): void
    {
        $customer = $this->makeCustomer();
        $product  = $this->makeProduct(['stock' => 5]);

        $this->actingAs($customer, 'customer');
        $this->post(route('cart.add', $product->id));

        // simulate stock being depleted by another order placed in the meantime
        $product->update(['stock' => 0]);

        $response = $this->post(route('cart.checkout'), [
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_percent_voucher_reduces_order_discount(): void
    {
        $customer = $this->makeCustomer();
        $product  = $this->makeProduct(['price' => 2000000, 'stock' => 5]);

        Voucher::create([
            'code' => 'SALE10',
            'type' => 'percent',
            'value' => 10,
            'min_order' => 0,
            'usage_limit' => 100,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($customer, 'customer');
        $this->post(route('cart.add', $product->id));

        $this->post(route('cart.checkout'), [
            'payment_method'  => 'cod',
            'shipping_method' => 'standard',
            'voucher_code'    => 'SALE10',
        ]);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals('SALE10', $order->voucher_code);
        $this->assertGreaterThan(0, $order->discount_amount);

        $this->assertDatabaseHas('vouchers', [
            'code' => 'SALE10',
            'used_count' => 1,
        ]);
    }

    public function test_cart_persists_across_requests_for_logged_in_customer(): void
    {
        $customer = $this->makeCustomer();
        $product  = $this->makeProduct();

        $this->actingAs($customer, 'customer');
        $this->post(route('cart.add', $product->id));

        // simulate a fresh request cycle (DB-backed cart, not session-dependent)
        $response = $this->get(route('cart.index'));

        $response->assertOk();
        $this->assertDatabaseHas('cart_items', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
        ]);
    }
}
