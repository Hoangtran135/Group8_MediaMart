# Design Patterns trong dự án MediaMart - Phân theo chức năng

Tài liệu này mô tả toàn bộ các Design Pattern (GoF) đã áp dụng trong
dự án **MediaMart**, trình bày theo từng **chức năng nghiệp vụ** thay
vì theo từng pattern riêng lẻ.

> **Thành viên thực hiện:**
> | # | Họ tên | Vai trò |
> |---|---|---|
> | 1 | Trần Xuân Hoàng | Thành Viên 1 |
> | 2 | Trần Văn Hiệp | Thành Viên 2 |
> | 3 | Trần Huy Hoàng | Thành Viên 3 |
> | 4 | Nguyễn Minh Quang | Thành Viên 4 |
>
> **Liên hệ:** 55 Giải Phóng, Hà Nội · 0378106753 · hoangtranxuan04@gmaill.com

---

## 1. Quản lý cấu hình chung của shop

**Chức năng**: Lưu trữ và cung cấp các thông số cấu hình dùng chung
trong toàn hệ thống: ngưỡng miễn phí ship, phí ship mặc định, danh
sách voucher hợp lệ.

**Pattern áp dụng**: `Singleton`

**Định nghĩa**: Đảm bảo một class chỉ có duy nhất một instance trong
suốt vòng đời ứng dụng, và cung cấp một điểm truy cập toàn cục đến
instance đó.

**Lý do chọn**: Cấu hình shop (phí ship, voucher...) là dữ liệu dùng
chung, không cần và không nên khởi tạo nhiều lần ở nhiều nơi. Singleton
giúp mọi thành phần (CheckoutFacade, ShippingFeeCalculator...) đọc
cùng một bộ cấu hình nhất quán.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: mỗi class cần phí ship/ngưỡng freeship/voucher tự khai
  báo `new SiteSettings(...)` hoặc hardcode lại số liệu → dễ lệch dữ
  liệu giữa các nơi.
- **Sau**: chỉ có 1 instance duy nhất qua `SiteSettings::getInstance()`,
  mọi nơi đọc cùng một bộ giá trị.
- **Lợi ích**: muốn đổi ngưỡng freeship hay thêm voucher mới, chỉ sửa
  1 file `SiteSettings.php`, áp dụng ngay cho toàn hệ thống.

**Code minh họa** (`app/Support/SiteSettings.php`):

```php
class SiteSettings
{
    private static ?SiteSettings $instance = null;

    private int $freeshipThreshold = 500000;
    private int $standardShippingFee = 30000;
    private int $expressShippingFee = 60000;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function freeshipThreshold(): int { return $this->freeshipThreshold; }
    public function findVoucher(?string $code): ?array { /* ... */ }
}
```

**Sơ đồ UML**: [`docs/uml/16_singleton.puml`](uml/16_singleton.puml)

---

## 2. Chọn phương thức thanh toán (COD / VNPay / Momo)

**Chức năng**: Khi khách hàng đặt hàng, hệ thống cần tạo ra một đối
tượng đại diện cho phương thức thanh toán đã chọn (COD, VNPay, Momo),
mỗi phương thức có nhãn hiển thị, badge và cách tạo mã QR khác nhau.

**Pattern áp dụng**: `Factory Method`

**Định nghĩa**: Định nghĩa một interface (hoặc phương thức) để tạo đối
tượng, nhưng để cho lớp con (hoặc factory) quyết định lớp cụ thể nào
sẽ được khởi tạo.

**Lý do chọn**: Số lượng/loại phương thức thanh toán có thể mở rộng
trong tương lai (thêm ZaloPay, ShopeePay...). Factory Method giúp
`CartController` và `PaymentController` không cần biết chi tiết từng
loại, chỉ cần gọi `PaymentMethodFactory::make($code)`.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: mỗi nơi (giỏ hàng, trang thanh toán, admin) tự viết
  `if/switch` để biết VNPay/Momo/COD hiển thị gì, có QR hay không →
  lặp code, thêm phương thức mới phải sửa nhiều file.
- **Sau**: mỗi phương thức là 1 class riêng. Mọi nơi chỉ gọi
  `PaymentMethodFactory::make($code)` rồi dùng `->label()`,
  `->buildQrUrl()`...
- **Lợi ích**: thêm phương thức mới (ví dụ ZaloPay) chỉ cần thêm 1
  class + 1 dòng trong `match()`, không sửa Controller/View nào.

**Code minh họa** (`app/Services/Payment/PaymentMethodFactory.php`):

```php
interface PaymentMethod
{
    public function code(): string;
    public function label(): string;
    public function requiresQrPayment(): bool;
    public function buildQrUrl(Order $order): ?string;
}

class VnPayPaymentMethod extends AbstractQrPaymentMethod
{
    public function code(): string { return 'vnpay'; }
    public function label(): string { return 'VNPay'; }
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
```

**Sơ đồ UML**: [`docs/uml/17_factory_method_payment.puml`](uml/17_factory_method_payment.puml)

---

## 3. Tính phí vận chuyển từ các đơn vị vận chuyển (GHN / GHTK)

**Chức năng**: Lấy phí vận chuyển thực tế từ các API/SDK của đơn vị
vận chuyển bên thứ ba (GHN, GHTK) - mỗi bên có cấu trúc tham số/trả về
khác nhau.

**Pattern áp dụng**: `Adapter`

**Định nghĩa**: Chuyển đổi interface của một class thành một interface
khác mà client mong đợi, giúp các class có interface không tương thích
có thể làm việc cùng nhau.

**Lý do chọn**: SDK giả lập của GHN (`GhnApiClient::calculateFee()`)
và GHTK (`GhtkApiSdk::estimateShippingCost()`) có chữ ký phương thức và
kiểu dữ liệu trả về khác nhau. Adapter "bọc" chúng lại thành một
interface chung `ShippingProvider` để phần còn lại của hệ thống dùng
thống nhất.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: GHN trả về mảng lồng nhau, GHTK trả về số `float` trực
  tiếp → nơi tính phí ship phải biết và xử lý riêng cấu trúc dữ liệu
  của từng hãng.
- **Sau**: cả 2 được "bọc" qua `GhnShippingAdapter` /
  `GhtkShippingAdapter`, cùng có 1 method `getFee()` giống nhau.
  `ShippingFeeStrategy` chỉ gọi `getFee()`, không cần biết là GHN hay
  GHTK.
- **Lợi ích**: muốn đổi/thêm hãng vận chuyển mới (ví dụ Viettel Post),
  chỉ cần viết thêm 1 adapter mới, phần còn lại của hệ thống không
  phải sửa.

**Code minh họa** (`app/Services/Shipping/ShippingAdapters.php`):

```php
interface ShippingProvider
{
    public function getName(): string;
    public function getFee(int $orderTotal): int;
}

class GhnShippingAdapter implements ShippingProvider
{
    public function __construct(private GhnApiClient $client) {}

    public function getFee(int $orderTotal): int
    {
        $result = $this->client->calculateFee(['order_total' => $orderTotal]);
        return (int) $result['fee'];
    }
}

class GhtkShippingAdapter implements ShippingProvider
{
    public function __construct(private GhtkApiSdk $client) {}

    public function getFee(int $orderTotal): int
    {
        return (int) $this->client->estimateShippingCost((float) $orderTotal);
    }
}
```

**Sơ đồ UML**: [`docs/uml/18_adapter_shipping.puml`](uml/18_adapter_shipping.puml)

---

## 4. Lựa chọn phương thức vận chuyển (Tiêu chuẩn / Nhanh / Miễn phí)

**Chức năng**: Khách hàng chọn một trong các hình thức vận chuyển khi
checkout; mỗi hình thức có cách tính phí khác nhau, và đơn hàng đạt
ngưỡng miễn phí ship sẽ tự động được áp dụng freeship.

**Pattern áp dụng**: `Strategy`

**Định nghĩa**: Định nghĩa một họ các thuật toán, đóng gói từng thuật
toán vào một class riêng và cho phép thay đổi thuật toán sử dụng độc
lập với client.

**Lý do chọn**: Cách tính phí ship (tiêu chuẩn, hỏa tốc, miễn phí) là
các "thuật toán" có thể thay đổi theo lựa chọn của người dùng hoặc theo
điều kiện đơn hàng (vượt ngưỡng freeship). Strategy giúp thêm/sửa cách
tính phí mà không ảnh hưởng `CheckoutFacade`.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: `CheckoutFacade` phải chứa khối `if/elseif` lớn để tính
  phí theo từng hình thức + kiểm tra ngưỡng freeship → logic tính tiền
  dồn hết vào 1 nơi, sửa gì cũng đụng vào class trung tâm.
- **Sau**: mỗi hình thức (Tiêu chuẩn/Nhanh/Miễn phí) là 1 class
  Strategy riêng. `CheckoutFacade` chỉ gọi
  `ShippingFeeCalculator::resolve(...)->calculate($subtotal)`.
- **Lợi ích**: logic "đạt ngưỡng → tự miễn phí ship" nằm 1 chỗ trong
  `resolve()`. Thêm hình thức vận chuyển mới chỉ cần thêm 1 class
  Strategy, không sửa `CheckoutFacade`.

**Code minh họa** (`app/Services/Shipping/ShippingFeeStrategy.php`):

```php
interface ShippingFeeStrategy
{
    public function code(): string;
    public function label(): string;
    public function calculate(int $subtotal): int;
}

class ExpressShippingStrategy implements ShippingFeeStrategy
{
    public function calculate(int $subtotal): int
    {
        return (new GhnShippingAdapter(new GhnApiClient()))->getFee($subtotal) + 15000;
    }
}

class ShippingFeeCalculator
{
    public static function resolve(string $code, int $subtotal): ShippingFeeStrategy
    {
        if ($subtotal >= SiteSettings::getInstance()->freeshipThreshold()) {
            return new FreeShippingStrategy();
        }
        return self::make($code);
    }
}
```

**Sơ đồ UML**: [`docs/uml/19_strategy_shipping_fee.puml`](uml/19_strategy_shipping_fee.puml)

---

## 5. Áp dụng voucher / giảm giá / freeship vào giỏ hàng

**Chức năng**: Tính lại tổng tiền giỏ hàng (tạm tính, phí ship, giảm
giá, tổng cộng) khi khách hàng nhập mã voucher (giảm %, giảm số tiền
cố định, hoặc miễn phí ship).

**Pattern áp dụng**: `Decorator`

**Định nghĩa**: Cho phép gắn thêm hành vi/trách nhiệm mới vào một đối
tượng một cách linh hoạt bằng cách "bọc" đối tượng đó trong một hoặc
nhiều lớp decorator, thay vì kế thừa tĩnh.

**Lý do chọn**: Mỗi loại voucher chỉ ảnh hưởng đến MỘT phần của phép
tính giá (giảm % tổng tiền, trừ số tiền cố định, hoặc miễn phí ship).
Decorator cho phép "bọc" thêm từng loại giảm giá lên trên giá gốc mà
không phải tạo ra tổ hợp class cho từng trường hợp.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: muốn hỗ trợ nhiều loại giảm giá phải viết `if/elseif` để
  trừ tiền theo từng loại, hoặc tạo class riêng cho mọi tổ hợp (giảm %
  + freeship, giảm tiền + freeship...) → bùng nổ số class.
- **Sau**: có 1 class giá gốc (`BaseCartPrice`) và các decorator
  (`PercentDiscountDecorator`, `AmountDiscountDecorator`,
  `FreeshipDecorator`) "bọc" lên trên, mỗi decorator chỉ lo 1 việc.
- **Lợi ích**: thêm loại voucher mới chỉ cần thêm 1 decorator, có thể
  kết hợp nhiều decorator với nhau mà không cần sửa
  `BaseCartPrice` hay `CheckoutFacade`.

**Code minh họa** (`app/Services/Cart/CartPriceDecorator.php`):

```php
interface CartPriceComponent
{
    public function getSubtotal(): int;
    public function getShippingFee(): int;
    public function getDiscount(): int;
    public function getTotal(): int;
}

class BaseCartPrice implements CartPriceComponent
{
    public function __construct(private int $subtotal, private int $shippingFee) {}

    public function getDiscount(): int { return 0; }
    public function getTotal(): int
    {
        return max(0, $this->getSubtotal() + $this->getShippingFee() - $this->getDiscount());
    }
}

class PercentDiscountDecorator extends CartPriceDecorator
{
    public function __construct(CartPriceComponent $inner, private int $percent, private string $voucherCode)
    {
        parent::__construct($inner);
    }

    public function getDiscount(): int
    {
        return (int) round($this->inner->getSubtotal() * $this->percent / 100);
    }
}
```

**Sơ đồ UML**: [`docs/uml/20_decorator_cart_price.puml`](uml/20_decorator_cart_price.puml)

---

## 6. Tạo đơn hàng và chi tiết đơn hàng

**Chức năng**: Khi khách hàng xác nhận đặt hàng, hệ thống cần tạo bản
ghi `Order` (kèm thông tin khách hàng, phương thức thanh toán, vận
chuyển, voucher) và các bản ghi `OrderDetail` tương ứng với từng sản
phẩm trong giỏ hàng.

**Pattern áp dụng**: `Builder`

**Định nghĩa**: Tách việc xây dựng một đối tượng phức tạp khỏi biểu
diễn của nó, cho phép cùng một quá trình xây dựng tạo ra các biểu diễn
khác nhau, thông qua một chuỗi gọi hàm (fluent interface).

**Lý do chọn**: Một đơn hàng có nhiều thông tin cần thiết lập dần
(khách hàng, thanh toán, vận chuyển, voucher, danh sách sản phẩm).
Builder với cú pháp "fluent" (`->forCustomer()->withPaymentMethod()->...->build()`)
giúp code dễ đọc, dễ mở rộng thêm trường mới mà không phá vỡ các nơi
gọi cũ.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: tạo đơn hàng bằng 1 lệnh `Order::create([...mảng dài...])`
  rồi loop tạo `OrderDetail` ngay trong `CheckoutFacade` → khó đọc, khó
  thêm trường mới (mảng càng dài càng dễ nhầm key).
- **Sau**: dựng đơn hàng theo từng bước rõ nghĩa:
  `OrderBuilder::new()->forCustomer()->withPaymentMethod()->withShipping()->withVoucher()->addItemsFromCart()->build()`.
- **Lợi ích**: thêm trường mới cho đơn hàng chỉ cần thêm 1 method
  `withXxx()` trong `OrderBuilder`, các nơi gọi cũ không bị ảnh hưởng.

**Code minh họa** (`app/Services/Order/OrderBuilder.php`):

```php
class OrderBuilder
{
    public static function new(): self { return new self(); }

    public function forCustomer(?int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function withPaymentMethod(string $method): self
    {
        $this->paymentMethod = $method;
        return $this;
    }

    public function withShipping(string $method, int $fee): self { /* ... */ return $this; }
    public function withVoucher(?string $code, int $discount): self { /* ... */ return $this; }
    public function addItemsFromCart(array $cart): self { /* ... */ return $this; }

    public function build(): Order
    {
        $order = Order::create([/* ... */]);
        foreach ($this->items as $item) {
            OrderDetail::create([/* order_id, product_id, number, price */]);
        }
        return $order;
    }
}
```

**Sơ đồ UML**: [`docs/uml/21_builder_order.puml`](uml/21_builder_order.puml)

---

## 7. Thông báo cho khách hàng sau khi đặt hàng

**Chức năng**: Sau khi đơn hàng được tạo thành công, hệ thống cần gửi
thông báo (email, SMS) xác nhận cho khách hàng. Có thể bổ sung thêm
kênh thông báo khác trong tương lai.

**Pattern áp dụng**: `Observer`

**Định nghĩa**: Định nghĩa quan hệ một-nhiều giữa các đối tượng, sao
cho khi một đối tượng (subject) thay đổi trạng thái, tất cả các đối
tượng phụ thuộc (observer) đều được thông báo và cập nhật tự động.

**Lý do chọn**: Việc gửi email/SMS là các tác vụ "phụ" độc lập với việc
tạo đơn hàng, không nên làm `CheckoutFacade` phình to hoặc phụ thuộc
trực tiếp vào logic gửi thông báo. Dùng Event/Listener của Laravel
(triển khai Observer) cho phép thêm/bớt kênh thông báo chỉ bằng cách
đăng ký thêm listener, không sửa code đặt hàng.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: sau khi tạo đơn, `CheckoutFacade` gọi trực tiếp
  `gửi email(...)`, `gửi SMS(...)` → muốn thêm kênh thông báo (Zalo,
  push notification) phải sửa thêm vào `CheckoutFacade`.
- **Sau**: `CheckoutFacade` chỉ bắn 1 sự kiện
  `Event::dispatch(new OrderPlaced($order))`; các listener
  (`SendOrderEmailNotification`, `SendOrderSmsNotification`) tự lắng
  nghe và xử lý.
- **Lợi ích**: thêm/bớt kênh thông báo chỉ cần thêm/xóa 1 listener và
  1 dòng `Event::listen(...)`, không đụng vào `CheckoutFacade`.

**Code minh họa**:

```php
// app/Events/OrderPlaced.php
class OrderPlaced
{
    public function __construct(public Order $order) {}
}

// app/Listeners/SendOrderEmailNotification.php
class SendOrderEmailNotification
{
    public function handle(OrderPlaced $event): void
    {
        Log::info("[Email] Gửi email xác nhận đơn hàng #{$event->order->id}");
    }
}

// app/Providers/AppServiceProvider.php (boot())
Event::listen(OrderPlaced::class, SendOrderEmailNotification::class);
Event::listen(OrderPlaced::class, SendOrderSmsNotification::class);

// app/Services/CheckoutFacade.php
Event::dispatch(new OrderPlaced($order));
```

**Sơ đồ UML**: [`docs/uml/22_observer_order_placed.puml`](uml/22_observer_order_placed.puml)

---

## 8. Quy trình đặt hàng tổng thể (Checkout)

**Chức năng**: Gộp toàn bộ quy trình thanh toán (tính phí ship, áp
voucher, chọn phương thức thanh toán, tạo đơn hàng, gửi thông báo)
thành một thao tác duy nhất khi khách hàng bấm "Đặt hàng".

**Pattern áp dụng**: `Facade`

**Định nghĩa**: Cung cấp một interface đơn giản, thống nhất cho một tập
hợp các interface phức tạp hơn trong một subsystem, giúp client dễ sử
dụng hơn mà không cần biết chi tiết bên trong.

**Lý do chọn**: Quy trình checkout liên quan đến nhiều pattern/subsystem
khác (Strategy + Adapter để tính ship, Decorator để áp voucher, Factory
Method để tạo payment method, Builder để dựng đơn hàng, Observer để
thông báo). Facade `CheckoutFacade::placeOrder()` gói gọn tất cả, giúp
`CartController::checkout()` chỉ cần gọi một hàm duy nhất.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: `CartController::checkout()` phải tự gọi lần lượt tính
  ship, áp voucher, tạo payment method, tạo đơn, gửi thông báo →
  Controller "biết" quá nhiều về nội bộ hệ thống, khó test/khó đọc.
- **Sau**: `CartController::checkout()` chỉ gọi
  `CheckoutFacade::placeOrder(...)`, mọi bước phức tạp được giấu bên
  trong Facade.
- **Lợi ích**: Controller gọn, tách biệt rõ "giao diện gọi" và "nghiệp
  vụ xử lý"; các bước bên trong Facade có thể thay đổi (đổi thứ tự,
  thêm bước) mà Controller không cần sửa.

**Code minh họa** (`app/Services/CheckoutFacade.php`):

```php
class CheckoutFacade
{
    public static function placeOrder(
        array $cart,
        ?int $customerId,
        string $paymentMethodCode,
        string $shippingMethodCode,
        ?string $voucherCode,
    ): array {
        $subtotal = (int) array_sum(array_map(fn ($i) => $i['price'] * $i['number'], $cart));

        // 1. Strategy + Adapter
        $shippingStrategy = ShippingFeeCalculator::resolve($shippingMethodCode, $subtotal);
        $shippingFee      = $shippingStrategy->calculate($subtotal);

        // 2. Decorator
        $priceBreakdown = self::applyVoucher(new BaseCartPrice($subtotal, $shippingFee), $voucherCode);

        // 3. Factory Method
        $paymentMethod = PaymentMethodFactory::make($paymentMethodCode);

        // 4. Builder
        $order = OrderBuilder::new()
            ->forCustomer($customerId)
            ->withPaymentMethod($paymentMethod->code())
            ->withShipping($shippingStrategy->code(), $priceBreakdown->getShippingFee())
            ->withVoucher($voucherCode, $priceBreakdown->getDiscount())
            ->addItemsFromCart($cart)
            ->build();

        // 5. Observer
        Event::dispatch(new OrderPlaced($order));

        return ['order' => $order, 'paymentMethod' => $paymentMethod, 'priceBreakdown' => $priceBreakdown];
    }
}

// app/Http/Controllers/CartController.php
public function checkout(Request $request)
{
    $result = CheckoutFacade::placeOrder(
        cart: $this->cartService->get(),
        customerId: Auth::guard('customer')->id(),
        paymentMethodCode: $request->input('payment_method', 'cod'),
        shippingMethodCode: $request->input('shipping_method', 'standard'),
        voucherCode: $request->input('voucher_code'),
    );
    // ...
}
```

**Sơ đồ UML**: [`docs/uml/23_facade_checkout.puml`](uml/23_facade_checkout.puml)

---

---

## 9. Quản lý sản phẩm & đơn hàng phía Admin

**Chức năng**: Admin thực hiện CRUD sản phẩm (thêm/sửa/xóa/xem danh sách)
và xem/xử lý đơn hàng mà không điều khướn truy vấn database trực tiếp trong
Controller.

**Pattern áp dụng**: `Repository`

**Định nghĩa**: Tạo một lớp trung gian (Repository) giữa Controller và
Model, đóng gói toàn bộ logic truy vấn dữ liệu, giúp Controller chỉ gọi
các phương thức nghiệp vụ rõ ràng mà không cần biết cách truy vấn.

**Lý do chọn**: `AdminProductController` và `AdminOrderController` cần
nhiều truy vấn phức tạp (join bảng, eager load, phân trang...). Đặt
hết vào Repository giúp Controller gọn hơn, dễ test và tái sử dụng
truy vấn.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: Controller gọi thẳng `Product::with('category')->latest()->paginate()`
  và `Order::with('customer')->findOrFail()` rải rác khắp nơi → nếu
  đổi tên cột hoặc thêm eager load mới phải sử a nhiều file.
- **Sau**: mọi truy vấn tập trung trong `ProductRepository` và
  `OrderRepository`; Controller chỉ gọi
  `$this->productRepo->allWithCategory()`,
  `$this->orderRepo->findWithDetails($id)`...
- **Lợi ích**: đổi ORM/query chỉ sửa 1 chỗ trong Repository;
  Controller dễ đọc, dễ viết unit test (mock Repository).

**Code minh họa**:

```php
// app/Repositories/ProductRepository.php
class ProductRepository
{
    public function allWithCategory(int $perPage = 25): LengthAwarePaginator
    {
        return Product::with('category')->latest()->paginate($perPage);
    }

    public function findWithRelations(int $id): Product
    {
        return Product::with(['ratings', 'category'])->findOrFail($id);
    }

    public function create(array $data): Product  { return Product::create($data); }
    public function update(Product $p, array $data): void { $p->update($data); }
    public function delete(Product $p): void { $p->delete(); }
}

// app/Http/Controllers/Admin/AdminProductController.php
class AdminProductController extends Controller
{
    public function __construct(
        private ProductRepository $productRepo,
        private ImageUploadService $imageService,
    ) {}

    public function index()
    {
        $products = $this->productRepo->allWithCategory(); // không viết query
        return view('admin.products.index', compact('products'));
    }
}
```

---

## 10. Tự động xóa ảnh khi xóa sản phẩm / tin tức

**Chức năng**: Khi admin xóa một sản phẩm hoặc bài tin tức, file ảnh
liên kết trên đĩa củng phải được xóa theo — mà không cần Controller
gọi thủ công.

**Pattern áp dụng**: `Observer` (Model Observer)

**Định nghĩa**: Lắng nghe các sự kiện vòng đời của Model
(`creating`, `updating`, `deleting`...) và tự động thực hiện hành
động phụ mà không cần viết thêm code vào Controller.

**Lý do chọn**: Việc xóa ảnh là tác vụ "phụ" gắn với vòng đời Model,
không nên để Controller biết và tự gọi. Model Observer giúp logic
này tự động chạy mọi khi nào Model bị xóa, dù xóa từ đâu đi nữa.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: `AdminProductController::destroy()` phải tự gọi
  `Storage::delete(...)` rồi mới gọi `$product->delete()` → nếu
  quên hoặc xóa qua Artisan/Seeder thì ảnh vẫn còn nằm trên đĩa.
- **Sau**: `ProductObserver::deleting()` tự chạy mọi khi Model bị
  xóa; Controller chỉ cần gọi `$product->delete()`.
- **Lợi ích**: logic "dọn file" luôn được thực thi nhất quán, không
  phụ thuộc vào ai gọi lệnh xóa.

**Code minh họa**:

```php
// app/Observers/ProductObserver.php
class ProductObserver
{
    public function deleting(Product $product): void
    {
        if ($product->photo) {
            Storage::disk('uploads')->delete('products/' . $product->photo);
        }
    }
}

// app/Observers/NewsArticleObserver.php
class NewsArticleObserver
{
    public function deleting(NewsArticle $article): void
    {
        if ($article->photo) {
            Storage::disk('uploads')->delete('news/' . $article->photo);
        }
    }
}

// app/Providers/AppServiceProvider.php
Product::observe(ProductObserver::class);
NewsArticle::observe(NewsArticleObserver::class);
```

---

## 11. Upload ảnh sản phẩm & tin tức

**Chức năng**: Admin upload ảnh khi thêm/sửa sản phẩm và tin tức;
logic lưu file và xóa file cũ được tái sử dụng cho nhiều module khác nhau.

**Pattern áp dụng**: `Service` (Service Layer / Single Responsibility)

**Định nghĩa**: Tách logic nghiệp vụ ra khỏi Controller vào một class
Service độc lập, giúp Controller chỉ điều phối dướng dẫn request/response
mà không chứa business logic.

**Lý do chọn**: Logic `upload()` (lưu file, đặt tên, chọn disk) và
`delete()` được dùng ở cả `AdminProductController` lẫn
`AdminNewsController`. Tách thành `ImageUploadService` tránh lặp code,
dễ thay thế chiến lược lưu trữ sau này (local → S3...).

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: mỗi Controller tự viết `$file->store(...)` và
  `Storage::delete(...)` → nếu muốn đổi tên folder hoặc đổi sang
  lưu S3 phải sửa tất cả Controller.
- **Sau**: cả 2 Controller dùng `ImageUploadService::upload()` và
  `ImageUploadService::delete()` — chỉ cần sửa 1 file Service.
- **Lợi ích**: dễ thay disk lưu trữ, dễ test, không lặp code.

**Code minh họa** (`app/Services/ImageUploadService.php`):

```php
class ImageUploadService
{
    public function upload(UploadedFile $file, string $folder): string
    {
        $path = $file->store($folder, 'uploads');
        return basename($path);
    }

    public function delete(string $folder, string $filename): void
    {
        Storage::disk('uploads')->delete($folder . '/' . $filename);
    }
}

// Dùng trong AdminProductController và AdminNewsController:
$data['photo'] = $this->imageService->upload($request->file('photo'), 'products');
```

---

## 12. Hiển thị trang Giới thiệu & Chính sách

**Chức năng**: Hiển thị các trang nội dung tĩnh (Giới thiệu, Chính
sách) không cần truy vấn database, không gắn với module nghiệp vụ
chính.

**Pattern áp dụng**: `Service` (Single Responsibility + thin Controller)

**Định nghĩa**: Giữ Controller thật mỏng — chỉ nhận request và
trả về View, không chứa bất kỳ business logic hay query nào.

**Lý do chọn**: `PageController` chỉ định tuyến đến đúng View. Hai
chức năng `about()` và `policy()` được tách khỏi `HomeController` để
tuân thủ Single Responsibility — mỗi Controller chỉ quản lý một nhóm
chức năng.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: nhồi thêm `about()`, `policy()` vào `HomeController` hoặc
  `ContactController` → Controller phình to, khó tìm và bảo trì.
- **Sau**: `PageController` độc lập chỉ xử lý các trang tĩnh;
  dễ thêm trang mới (FAQ, Tuyển dụng...) mà không ảnh hưởng các
  Controller khác.
- **Lợi ích**: code ngắn gọn, dễ bảo trì, tách biệt rõ routes tĩnh
  và routes nghiệp vụ.

**Code minh họa** (`app/Http/Controllers/PageController.php`):

```php
class PageController extends Controller
{
    public function about()
    {
        return view('frontend.about');
    }

    public function policy()
    {
        return view('frontend.policy');
    }
}
```

---

---

## 9. Sắp xếp sản phẩm theo nhiều tiêu chí

**Chức năng**: Người dùng chọn sắp xếp danh sách sản phẩm theo giá
tăng/giảm, tên A→Z, mới nhất... mỗi cách sắp xếp có thể thay đổi
độc lập mà không sửa `ProductService`.

**Pattern áp dụng**: `Strategy`

**Định nghĩa**: Mỗi chiến lược sắp xếp (“thuật toán”) được đóng gói
vào một class riêng implement `ProductSortStrategy`, có thể hoán đổi
qua `ProductSortStrategyFactory::make($order)`.

**Lý do chọn**: Trước đây `ProductService` dùng `match()` dài trong
loop — thêm tiêu chí sắp xếp mới phải sửa thẳng vào Service. Nay
mỗi cách sắp xếp là 1 class Strategy riêng, `ProductService` chỉ
nhận strategy và gọi `apply()`.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: `ProductService::listByCategory()` chứa khối `match` dài →
  thêm tiêu chí mới (như sắp theo rating) phải sửa trong Service.
- **Sau**: mỗi tiêu chí là 1 class (`PriceAscSortStrategy`,
  `NameDescSortStrategy`...); `ProductSortStrategyFactory::make($order)`
  trả về strategy đúng, `ProductService` chỉ gọi `.apply($query)`.
- **Lợi ích**: thêm tiêu chí mới chỉ thêm 1 class + 1 dòng `match`,
  không đụng vào `ProductService`.

**Code minh họa** (`app/Services/ProductService.php`):

```php
interface ProductSortStrategy
{
    public function apply(Builder $query): Builder;
    public function code(): string;
    public function label(): string;
}

class PriceAscSortStrategy implements ProductSortStrategy
{
    public function apply(Builder $query): Builder { return $query->orderBy('price', 'asc'); }
    public function code(): string  { return 'priceAsc'; }
    public function label(): string { return 'Giá tăng dần'; }
}

class ProductSortStrategyFactory
{
    public static function make(string $order): ProductSortStrategy
    {
        return match ($order) {
            'priceAsc'  => new PriceAscSortStrategy(),
            'priceDesc' => new PriceDescSortStrategy(),
            'nameAsc'   => new NameAscSortStrategy(),
            'nameDesc'  => new NameDescSortStrategy(),
            default     => new NewestSortStrategy(),
        };
    }
}

// ProductService - chỉ gọi strategy, không viết logic sắp xếp
$query = ProductSortStrategyFactory::make($order)->apply($query);
```

---

## 10. Đăng nhập / Đăng ký / Đăng xuất khách hàng

**Chức năng**: Xử lý toàn bộ luồng xác thực khách hàng (kiểm tra
đã đăng nhập, đăng nhập, đăng ký, đăng xuất) mà `AccountController`
không cần biết chi tiết về guard, session, hash password.

**Pattern áp dụng**: `Facade`

**Định nghĩa**: `AuthFacade` cung cấp một interface đơn giản
(`isLoggedIn()`, `login()`, `register()`, `logout()`) che giấu toàn
bộ sự phức tạp của Laravel Auth (guard, session regenerate,
hash password...).

**Lý do chọn**: `AccountController` ban đầu gọi trực tiếp
`Auth::guard('customer')->attempt()`, `Hash::make()`, `session()->invalidate()`...
— có quá nhiều chi tiết kỹ thuật. `AuthFacade` ”giấu” chúng,
Controller chỉ gọi phương thức nghiệp vụ.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: `AccountController` gọi trực tiếp `Auth::guard(...)`,
  `Hash::make()`, `session()->regenerateToken()`... → Controller
  biết quá nhiều chi tiết nội tại.
- **Sau**: `AccountController` chỉ gọi `AuthFacade::login($request)`,
  `AuthFacade::register($request)`, `AuthFacade::logout($request)`.
- **Lợi ích**: đổi guard hoặc cơ chế xác thực chỉ sửa trong
  `AuthFacade`, Controller không đổi gì.

**Code minh họa** (`app/Services/AuthFacade.php`):

```php
class AuthFacade
{
    public static function isLoggedIn(): bool
    {
        return Auth::guard('customer')->check();
    }

    public static function login(LoginRequest $request): bool
    {
        if (Auth::guard('customer')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();
            return true;
        }
        return false;
    }

    public static function register(RegisterRequest $request): Customer
    {
        return Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public static function logout(Request $request): void
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

// AccountController - chỉ gọi Facade
public function login(LoginRequest $request)
{
    if (AuthFacade::login($request)) {
        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }
    return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
}
```

---

## 11. Quản lý giỏ hàng & Wishlist trong session

**Chức năng**: `CartService` và `WishlistService` đọc/ghi dữ liệu
giỏ hàng và danh sách yêu thích từ Session, chỉ nên có một instance
duy nhất trong suốt vòng đời request.

**Pattern áp dụng**: `Singleton`

**Định nghĩa**: Đảm bảo chỉ tồn tại một instance duy nhất của
`CartService` và `WishlistService` trong mỗi request, tránh đọc/ghi
session nhiều lần không cần thiết.

**Lý do chọn**: Nếu nhiều nơi khởi tạo `new CartService()` có thể dẫn
đến đọc session nhiều lần trong cùng 1 request. Singleton đảm bảo
mọi nơi dùng chung 1 instance.

**Thay đổi cho hệ thống khi áp dụng pattern**:

- **Trước**: `CartController` inject `new CartService()` mỗi request
  → constructor chạy nhiều lần, không kiểm soát được instance.
- **Sau**: `CartService::getInstance()` / `WishlistService::getInstance()`
  trả về cùng 1 instance; constructor được khai báo `private`.
- **Lợi ích**: kiểm soát số lượng instance, nhất quán trạng thái
  giỏ hàng trong toàn request.

**Code minh họa** (`app/Services/CartService.php`):

```php
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

    public function get(): array  { return Session::get('cart', []); }
    public function add(int $productId): void { /* ... */ }
    public function remove(int $productId): void { /* ... */ }
    public function total(): float { /* ... */ }
}

// CartController
$this->cartService = CartService::getInstance();
```

---

## 12. Quản lý sản phẩm & đơn hàng phía Admin

**Chức năng**: Admin thực hiện CRUD sản phẩm và xem/xử lý đơn hàng
mà không điều khướn truy vấn database trực tiếp trong Controller.

**Pattern áp dụng**: `Repository`

**Định nghĩa**: Tạo một lớp trung gian (Repository) giữa Controller
và Model, đóng gói toàn bộ logic truy vấn dữ liệu.

**Lý do chọn**: `AdminProductController` và `AdminOrderController` cần
nhiều truy vấn phức tạp (join bảng, eager load, phân trang). Đặt
hết vào Repository giúp Controller gọn hơn, dễ test.

**Code minh họa**:

```php
// app/Repositories/ProductRepository.php
class ProductRepository
{
    public function allWithCategory(int $perPage = 25): LengthAwarePaginator
    {
        return Product::with('category')->latest()->paginate($perPage);
    }
    public function findWithRelations(int $id): Product
    {
        return Product::with(['ratings', 'category'])->findOrFail($id);
    }
}

// AdminProductController - không viết query trực tiếp
$products = $this->productRepo->allWithCategory();
```

---

## 13. Tự động xóa ảnh khi xóa sản phẩm / tin tức

**Chức năng**: Khi admin xóa sản phẩm hoặc bài tin tức, file ảnh
trên đĩa tự động bị xóa mà không cần Controller can thiệp.

**Pattern áp dụng**: `Observer` (Model Observer)

**Code minh họa**:

```php
// app/Observers/ProductObserver.php
class ProductObserver
{
    public function deleting(Product $product): void
    {
        if ($product->photo) {
            Storage::disk('uploads')->delete('products/' . $product->photo);
        }
    }
}
// AppServiceProvider:
Product::observe(ProductObserver::class);
NewsArticle::observe(NewsArticleObserver::class);
```

---

## Tổng kết - bảng tổng hợp

| # | Chức năng | Pattern | File chính |
|---|---|---|---|
| 1 | Cấu hình chung của shop | **Singleton** | `app/Support/SiteSettings.php` |
| 2 | Giỏ hàng & Wishlist trong session | **Singleton** | `app/Services/CartService.php`, `app/Services/WishlistService.php` |
| 3 | Chọn phương thức thanh toán (COD/VNPay/Momo) | **Factory Method** | `app/Services/Payment/PaymentMethod.php` |
| 4 | Tính phí ship từ GHN/GHTK | **Adapter** | `app/Services/Shipping/ShippingProvider.php` |
| 5 | Lựa chọn hình thức vận chuyển | **Strategy** | `app/Services/Shipping/ShippingStrategy.php` |
| 6 | Sắp xếp sản phẩm theo nhiều tiêu chí | **Strategy** | `app/Services/ProductService.php` |
| 7 | Áp voucher/giảm giá vào giỏ hàng | **Decorator** | `app/Services/Cart/CartPrice.php` |
| 8 | Tạo đơn hàng & chi tiết đơn hàng | **Builder** | `app/Services/Order/OrderDirector.php` |
| 9 | Quy trình checkout tổng thể | **Facade** | `app/Services/CheckoutFacade.php` |
| 10 | Đăng nhập / Đăng ký / Đăng xuất | **Facade** | `app/Services/AuthFacade.php` |
| 11 | Thông báo (Email/SMS) sau khi đặt hàng | **Observer** (Event/Listener) | `app/Events/OrderPlaced.php`, `app/Listeners/*` |
| 12 | Tự động xóa ảnh khi xóa sản phẩm/tin tức | **Observer** (Model Observer) | `app/Observers/ProductObserver.php`, `app/Observers/NewsArticleObserver.php` |
| 13 | Quản lý sản phẩm & đơn hàng (Admin) | **Repository** | `app/Repositories/ProductRepository.php`, `app/Repositories/OrderRepository.php` |
