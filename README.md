# MediaMart – Hệ thống bán điện tử & công nghệ

Nền tảng thương mại điện tử bán thiết bị điện tử, xây dựng bằng **Laravel 12** (PHP 8.2+).

Dự án bao gồm đầy đủ luồng nghiệp vụ của một website bán hàng: duyệt
sản phẩm (lọc theo danh mục/khoảng giá/sắp xếp), giỏ hàng (lưu DB cho
khách đã đăng nhập), đặt hàng, thanh toán (COD/VNPay/Momo mô phỏng
QR), voucher giảm giá, vận chuyển, đánh giá sản phẩm, yêu thích, quên
mật khẩu, và quản trị (sản phẩm, danh mục, đơn hàng, tin tức, khách
hàng, voucher). Phần lõi giỏ hàng - thanh toán - đặt hàng được thiết
kế theo các **Design Pattern (GoF)** để dễ mở rộng và bảo trì (xem mục
bên dưới).

---

## Yêu cầu hệ thống

| Phần mềm | Phiên bản tối thiểu |
|----------|---------------------|
| PHP | 8.2+ (extensions: `mysqli`, `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`, `zip`) |
| MySQL | 8.0+ / MariaDB 10.4+ |
| Composer | 2.x |
| Node.js | 18+ |
| XAMPP (hoặc tương đương) | 8.2.x |

> **Lưu ý PHP version:** dự án dùng Laravel 12 (yêu cầu PHP ^8.2), chạy
> tốt trên XAMPP 8.2.x. Nếu máy có nhiều bản PHP cài song song (ví dụ
> qua Task Scheduler/PATH khác), hãy chắc chắn `composer install` và
> Apache đều dùng đúng PHP đi kèm XAMPP, không phải bản PHP khác trong
> `PATH` hệ thống.

---

## Hướng dẫn cài đặt & chạy dự án (từ clone đến lúc chạy được)

### Bước 1 - Clone dự án vào thư mục `htdocs` của XAMPP

```bash
cd C:\xampp\htdocs
git clone <repository-url> mediamart-laravel
cd mediamart-laravel
```

### Bước 2 - Khởi động Apache & MySQL

Mở **XAMPP Control Panel** → Start **Apache** và **MySQL**.

### Bước 3 - Tạo database

Truy cập [http://localhost/phpmyadmin](http://localhost/phpmyadmin) → tạo
database mới tên `mediamart_laravel` (collation `utf8mb4_unicode_ci`).

### Bước 4 - Cài dependencies PHP & JS

```bash
composer install
npm install
```

### Bước 5 - Tạo file cấu hình `.env`

```bash
copy .env.example .env
php artisan key:generate
```

Mở `.env` và kiểm tra/cập nhật thông tin kết nối database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mediamart_laravel
DB_USERNAME=root
DB_PASSWORD=
```

Nếu muốn gửi email thật (xác nhận đơn hàng, quên mật khẩu), đổi
`MAIL_MAILER=log` thành `smtp` và điền `MAIL_HOST`/`MAIL_USERNAME`/
`MAIL_PASSWORD` (Gmail App Password, Mailtrap...). Mặc định
(`MAIL_MAILER=log`) email vẫn được tạo đầy đủ, chỉ ghi vào
`storage/logs/laravel.log` thay vì gửi đi — tiện để xem nội dung khi
phát triển.

### Bước 6 - Chạy migration + seed dữ liệu mẫu

```bash
php artisan migrate:fresh --seed
```

Lệnh này sẽ:
- Tạo toàn bộ bảng theo migrations
- Seed dữ liệu mẫu: tài khoản admin/khách hàng, danh mục, sản phẩm, tin tức, đơn hàng mẫu

### Bước 7 - Build giao diện (Vite)

```bash
npm run build
```

> Nếu muốn vừa code vừa xem thay đổi tức thì (hot reload), dùng
> `npm run dev` ở một terminal riêng thay vì `npm run build`.

### Bước 8 - Truy cập website

Mở trình duyệt và vào:

```
http://localhost/mediamart-laravel/public
```

- Trang quản trị: `http://localhost/mediamart-laravel/public/admin`
- Tài khoản đăng nhập mặc định: xem mục [Tài khoản mặc định](#tài-khoản-mặc-định)

---

## Các cách chạy dự án khác

### Dùng Laravel artisan serve (không cần XAMPP/Apache)

```bash
php artisan serve
```

Truy cập: [http://localhost:8000](http://localhost:8000)

### Chế độ development (đầy đủ)

```bash
composer run dev
```

Khởi động đồng thời: Laravel server · Queue listener · Log viewer (Pail) · Vite (hot reload)

> **Windows:** Lệnh `composer run dev` có thể lỗi do thiếu extension `pcntl` (không hỗ trợ trên Windows). Chạy riêng từng lệnh thay thế:
>
> ```bash
> php artisan serve        # Terminal 1
> npm run dev              # Terminal 2
> php artisan queue:listen # Terminal 3 (nếu cần)
> ```

---

## Tài khoản mặc định

| Loại | Email | Mật khẩu |
|------|-------|-----------|
| Admin | `admin@gmail.com` | `admin` |
| Khách hàng | `user@gmail.com` | `user` |

Quên mật khẩu? Dùng link "Quên mật khẩu?" ở trang đăng nhập (khách
hàng: `/account/forgot-password`, admin: `/admin/forgot-password`).

---

## Cấu trúc dự án

```
mediamart-laravel/
├── app/
│   ├── Events/                   # OrderPlaced, OrderStatusChanged (Observer)
│   ├── Http/
│   │   ├── Controllers/          # Frontend controllers
│   │   │   └── Admin/            # Admin controllers
│   │   └── Middleware/           # customer.auth, admin.auth
│   ├── Listeners/                # Gửi email/SMS khi có sự kiện đơn hàng
│   ├── Mail/                     # Mailable: xác nhận đơn, đổi trạng thái đơn
│   ├── Models/                   # Eloquent models
│   ├── Notifications/            # Reset password (customer/admin)
│   └── Services/                 # CartService, CheckoutFacade, OrderService...
├── database/
│   ├── migrations/               # Schema definitions
│   └── seeders/                  # Dữ liệu mẫu
├── public/
│   ├── css/mediamart.css         # Custom CSS
│   └── uploads/                  # Ảnh sản phẩm & tin tức
├── resources/views/
│   ├── frontend/                 # Giao diện khách hàng
│   ├── admin/                    # Giao diện quản trị
│   └── emails/                   # Markdown mail templates
├── tests/Feature/                # Feature test: checkout, voucher, giỏ hàng, tồn kho
└── routes/web.php                # Toàn bộ routes
```

---

## Các trang chính

### Khách hàng (`/`)
| Đường dẫn | Mô tả |
|-----------|-------|
| `/` | Trang chủ |
| `/products` | Danh sách sản phẩm (lọc danh mục/giá, sắp xếp) |
| `/products/{id}` | Chi tiết sản phẩm + đánh giá |
| `/search` | Tìm kiếm |
| `/cart` | Giỏ hàng & checkout |
| `/wishlist` | Yêu thích |
| `/news` | Tin tức |
| `/contact` | Liên hệ |
| `/account/login` · `/account/register` | Đăng nhập / Đăng ký |
| `/account/forgot-password` | Quên mật khẩu |
| `/orders` | Đơn hàng của tôi |

### Admin (`/admin`)
| Đường dẫn | Mô tả |
|-----------|-------|
| `/admin` | Dashboard |
| `/admin/products` | Quản lý sản phẩm |
| `/admin/categories` | Quản lý danh mục |
| `/admin/orders` | Quản lý đơn hàng (đổi trạng thái, xuất CSV) |
| `/admin/news` | Quản lý tin tức |
| `/admin/users` | Quản lý khách hàng |
| `/admin/vouchers` | Quản lý voucher |
| `/admin/forgot-password` | Quên mật khẩu admin |

---

## Lệnh hữu ích

```bash
# Reset và seed lại database
php artisan migrate:fresh --seed

# Xóa cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Chạy tests
php artisan test

# Format code (Laravel Pint)
./vendor/bin/pint
```

---

## Design Patterns đã áp dụng

Module **Giỏ hàng - Thanh toán - Đặt hàng** được tổ chức lại theo 8
pattern GoF, mỗi pattern gắn với một chức năng nghiệp vụ cụ thể:

| # | Chức năng | Pattern | File chính |
|---|---|---|---|
| 1 | Cấu hình chung của shop (ngưỡng freeship, phí ship, voucher) | Singleton | `app/Support/SiteSettings.php` |
| 2 | Chọn phương thức thanh toán (COD/VNPay/Momo) | Factory Method | `app/Services/Payment/PaymentMethod.php` |
| 3 | Tính phí ship từ đơn vị vận chuyển | Strategy | `app/Services/Shipping/ShippingStrategy.php` |
| 4 | Sắp xếp danh sách sản phẩm (mới nhất/giá/tên) | Strategy | `app/Services/ProductService.php` |
| 5 | Áp voucher/giảm giá/freeship vào giỏ hàng | Decorator | `app/Services/Cart/CartPrice.php` |
| 6 | Tạo đơn hàng & chi tiết đơn hàng | Builder | `app/Services/Order/OrderDirector.php` |
| 7 | Thông báo (email/SMS) sau khi đặt hàng & khi đổi trạng thái đơn | Observer | `app/Events/OrderPlaced.php`, `app/Events/OrderStatusChanged.php`, `app/Listeners/*` |
| 8 | Quy trình checkout tổng thể | Facade | `app/Services/CheckoutFacade.php` |
| 9 | Giỏ hàng dùng chung 1 instance trong request | Singleton | `app/Services/CartService.php` |
| 10 | Xác thực khách hàng (login/register/logout) | Facade | `app/Services/AuthFacade.php` |

---

## Bảo mật & vận hành

- **Throttle đăng nhập:** giới hạn 5 lần/phút cho các route login/register (customer + admin), chống brute-force.
- **Quên mật khẩu:** dùng Password Broker chuẩn của Laravel, token hết hạn sau 60 phút.
- **Giỏ hàng bền vững:** khách đã đăng nhập lưu giỏ hàng ở bảng `cart_items` (DB), không mất khi đổi thiết bị/trình duyệt. Giỏ hàng session của khách vãng lai được tự động gộp vào DB ngay khi đăng nhập.
- **Kiểm tra tồn kho:** chặn thêm vào giỏ/đặt hàng vượt quá số lượng tồn kho thực tế; tự động trừ/hoàn kho khi admin xác nhận/huỷ đơn.
- **Queue:** giữ `QUEUE_CONNECTION=sync` (xử lý đồng bộ ngay trong request) vì môi trường XAMPP không có sẵn tiến trình `queue:work` chạy nền. Nếu deploy lên server có worker riêng, có thể đổi sang `database`/`redis` để tăng hiệu năng.
- **Thanh toán VNPay/Momo:** hiện là **bản demo** (tạo mã QR minh hoạ, tự xác nhận thủ công) — chưa tích hợp API thanh toán thật, không dùng cho môi trường production.

---

## Công nghệ sử dụng

- **Backend:** Laravel 12, PHP 8.2+, MySQL
- **Frontend:** Bootstrap 5, Font Awesome 6, Be Vietnam Pro (Google Fonts)
- **Build tool:** Vite 8, Tailwind CSS v4
- **Editor:** CKEditor (admin)
- **Auth:** Laravel custom guards (`customer`, `admin`) + Password Broker cho reset password
- **Testing:** PHPUnit (Feature test chạy trên SQLite in-memory)
