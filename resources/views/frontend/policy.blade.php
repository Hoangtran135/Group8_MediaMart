@extends('frontend.layouts.app')
@section('title', 'Chính sách - MediaMart')

@section('content')

{{-- Page Title --}}
<div class="page-title-bar">
    <h1><i class="fa fa-shield-halved" style="color:var(--red);margin-right:8px;"></i>Chính sách của MediaMart</h1>
    <span class="bc ms-auto">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        Chính sách
    </span>
</div>

<div class="row g-4">
    {{-- Sidebar nav --}}
    <div class="col-md-3">
        <div class="policy-sidebar">
            <h6 class="policy-sidebar-title"><i class="fa fa-list me-2"></i>Danh mục chính sách</h6>
            <nav class="policy-nav">
                <a href="#baohanh"  class="policy-nav-link active"><i class="fa fa-shield-alt"></i> Bảo hành</a>
                <a href="#doitra"   class="policy-nav-link"><i class="fa fa-rotate-left"></i> Đổi trả</a>
                <a href="#vanchuyen" class="policy-nav-link"><i class="fa fa-truck"></i> Vận chuyển</a>
                <a href="#thanhtoan" class="policy-nav-link"><i class="fa fa-credit-card"></i> Thanh toán</a>
                <a href="#baomat"   class="policy-nav-link"><i class="fa fa-lock"></i> Bảo mật</a>
            </nav>
        </div>
    </div>

    {{-- Content --}}
    <div class="col-md-9">

        {{-- 1. Bảo hành --}}
        <div id="baohanh" class="policy-section">
            <div class="policy-section-header">
                <div class="policy-header-icon"><i class="fa fa-shield-alt"></i></div>
                <div>
                    <h4>Chính sách bảo hành</h4>
                    <span>Cam kết bảo hành chính hãng 100%</span>
                </div>
            </div>
            <div class="policy-body">
                <div class="policy-highlight">
                    <i class="fa fa-circle-check me-2" style="color:#2e7d32;"></i>
                    Tất cả sản phẩm tại MediaMart đều được bảo hành chính hãng theo tiêu chuẩn của nhà sản xuất.
                </div>
                <h6>Thời gian bảo hành</h6>
                <ul>
                    <li>Điện thoại, máy tính bảng: <strong>12 tháng</strong></li>
                    <li>Laptop, máy tính: <strong>12 – 24 tháng</strong> tùy hãng</li>
                    <li>Tivi, tủ lạnh, máy giặt: <strong>24 tháng</strong></li>
                    <li>Máy lạnh, điều hòa: <strong>24 tháng (bao gồm cả máy nén)</strong></li>
                    <li>Phụ kiện (tai nghe, sạc, chuột...): <strong>3 – 6 tháng</strong></li>
                </ul>
                <h6>Điều kiện bảo hành</h6>
                <ul>
                    <li>Sản phẩm còn trong thời hạn bảo hành.</li>
                    <li>Sản phẩm bị lỗi do nhà sản xuất, không phải do tác động ngoại lực.</li>
                    <li>Còn tem bảo hành, không bị rách hoặc cố tình tháo ra.</li>
                    <li>Có hóa đơn mua hàng tại MediaMart.</li>
                </ul>
                <h6>Không áp dụng bảo hành khi</h6>
                <ul>
                    <li>Sản phẩm bị vỡ, bể, cong vênh do va đập mạnh.</li>
                    <li>Bị ngấm nước, chập điện, cháy nổ do lỗi người dùng.</li>
                    <li>Đã được sửa chữa bởi bên thứ ba ngoài trung tâm bảo hành.</li>
                </ul>
            </div>
        </div>

        {{-- 2. Đổi trả --}}
        <div id="doitra" class="policy-section">
            <div class="policy-section-header">
                <div class="policy-header-icon" style="background:#e3f2fd;color:#1565c0;"><i class="fa fa-rotate-left"></i></div>
                <div>
                    <h4>Chính sách đổi trả</h4>
                    <span>Đổi trả dễ dàng trong vòng 30 ngày</span>
                </div>
            </div>
            <div class="policy-body">
                <div class="policy-highlight" style="background:#e3f2fd;border-color:#1565c0;color:#1565c0;">
                    <i class="fa fa-circle-check me-2"></i>
                    MediaMart hỗ trợ đổi trả miễn phí trong 30 ngày nếu sản phẩm có lỗi từ nhà sản xuất.
                </div>
                <h6>Thời hạn đổi trả</h6>
                <ul>
                    <li><strong>1 đổi 1 trong 7 ngày</strong> nếu sản phẩm bị lỗi kỹ thuật do nhà sản xuất.</li>
                    <li><strong>Đổi sản phẩm trong 30 ngày</strong> nếu sản phẩm gặp lỗi phần cứng.</li>
                    <li>Hàng đổi là sản phẩm cùng loại, cùng màu sắc và cấu hình (nếu còn hàng).</li>
                </ul>
                <h6>Điều kiện đổi trả</h6>
                <ul>
                    <li>Sản phẩm còn nguyên hộp, đầy đủ phụ kiện, hóa đơn mua hàng.</li>
                    <li>Không có dấu hiệu đã qua sử dụng, trầy xước, vỡ bể.</li>
                    <li>Lỗi thuộc về nhà sản xuất, có biên bản xác nhận từ trung tâm bảo hành.</li>
                </ul>
                <h6>Quy trình đổi trả</h6>
                <ol>
                    <li>Liên hệ hotline <strong>0378106753</strong> hoặc email <strong>hoangtranxuan04@gmaill.com</strong>.</li>
                    <li>Mang sản phẩm cùng hóa đơn đến cửa hàng MediaMart gần nhất.</li>
                    <li>Nhân viên kiểm tra và xác nhận lỗi trong vòng 30 phút.</li>
                    <li>Đổi sản phẩm mới ngay tại cửa hàng (nếu còn hàng) hoặc giao trong 24h.</li>
                </ol>
            </div>
        </div>

        {{-- 3. Vận chuyển --}}
        <div id="vanchuyen" class="policy-section">
            <div class="policy-section-header">
                <div class="policy-header-icon" style="background:#fff3e0;color:#e65100;"><i class="fa fa-truck"></i></div>
                <div>
                    <h4>Chính sách vận chuyển</h4>
                    <span>Giao hàng toàn quốc, nhanh chóng & an toàn</span>
                </div>
            </div>
            <div class="policy-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="policy-ship-card">
                            <div class="policy-ship-icon">🚀</div>
                            <h6>Hỏa tốc</h6>
                            <p>2–4 giờ (nội thành HN & HCM)</p>
                            <strong class="text-danger">35.000₫</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="policy-ship-card">
                            <div class="policy-ship-icon">📦</div>
                            <h6>Tiêu chuẩn</h6>
                            <p>1–3 ngày (toàn quốc)</p>
                            <strong class="text-danger">25.000₫</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="policy-ship-card" style="border-color:#2e7d32;">
                            <div class="policy-ship-icon">🎁</div>
                            <h6>Miễn phí ship</h6>
                            <p>Đơn hàng từ 500.000₫</p>
                            <strong style="color:#2e7d32;">0₫</strong>
                        </div>
                    </div>
                </div>
                <h6>Lưu ý khi nhận hàng</h6>
                <ul>
                    <li>Kiểm tra sản phẩm kỹ trước khi ký nhận từ nhân viên giao hàng.</li>
                    <li>Nếu hàng bị hư hỏng do vận chuyển, từ chối nhận và liên hệ ngay với MediaMart.</li>
                    <li>Thời gian giao hàng có thể thay đổi vào các dịp lễ, Tết.</li>
                </ul>
            </div>
        </div>

        {{-- 4. Thanh toán --}}
        <div id="thanhtoan" class="policy-section">
            <div class="policy-section-header">
                <div class="policy-header-icon" style="background:#f3e5f5;color:#6a1b9a;"><i class="fa fa-credit-card"></i></div>
                <div>
                    <h4>Chính sách thanh toán</h4>
                    <span>Đa dạng phương thức, an toàn & bảo mật</span>
                </div>
            </div>
            <div class="policy-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="policy-payment-card">
                            <i class="fa fa-money-bill-wave" style="font-size:26px;color:#2e7d32;"></i>
                            <h6>COD</h6>
                            <p>Thanh toán khi nhận hàng – không cần đặt cọc</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="policy-payment-card">
                            <i class="fa fa-qrcode" style="font-size:26px;color:#0066b3;"></i>
                            <h6>VNPay</h6>
                            <p>Quét mã QR hoặc chuyển khoản ngân hàng</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="policy-payment-card">
                            <i class="fa fa-wallet" style="font-size:26px;color:#a50064;"></i>
                            <h6>Momo</h6>
                            <p>Thanh toán qua ví điện tử Momo</p>
                        </div>
                    </div>
                </div>
                <h6>Hóa đơn & Chứng từ</h6>
                <ul>
                    <li>Hóa đơn VAT được xuất theo yêu cầu trong vòng 24h sau khi đặt hàng.</li>
                    <li>Gửi email xác nhận đơn hàng ngay sau khi thanh toán thành công.</li>
                    <li>Lịch sử đơn hàng được lưu trữ trong tài khoản của bạn.</li>
                </ul>
            </div>
        </div>

        {{-- 5. Bảo mật --}}
        <div id="baomat" class="policy-section">
            <div class="policy-section-header">
                <div class="policy-header-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="fa fa-lock"></i></div>
                <div>
                    <h4>Chính sách bảo mật</h4>
                    <span>Bảo vệ thông tin cá nhân của bạn</span>
                </div>
            </div>
            <div class="policy-body">
                <div class="policy-highlight" style="background:#e8f5e9;border-color:#2e7d32;color:#2e7d32;">
                    <i class="fa fa-circle-check me-2"></i>
                    MediaMart cam kết bảo mật tuyệt đối thông tin cá nhân và dữ liệu thanh toán của khách hàng.
                </div>
                <h6>Thông tin chúng tôi thu thập</h6>
                <ul>
                    <li>Họ tên, địa chỉ, số điện thoại, email để phục vụ đơn hàng.</li>
                    <li>Lịch sử mua sắm để cải thiện trải nghiệm cá nhân hóa.</li>
                    <li>Thông tin thiết bị, trình duyệt để phân tích và nâng cấp hệ thống.</li>
                </ul>
                <h6>Chúng tôi KHÔNG</h6>
                <ul>
                    <li>Bán hoặc cho thuê thông tin cá nhân của bạn cho bên thứ ba.</li>
                    <li>Lưu trữ thông tin thẻ tín dụng/thẻ ngân hàng trên hệ thống.</li>
                    <li>Gửi spam hoặc thư quảng cáo khi không có sự đồng ý của bạn.</li>
                </ul>
                <h6>Quyền của bạn</h6>
                <ul>
                    <li>Yêu cầu xem, chỉnh sửa hoặc xóa thông tin cá nhân bất kỳ lúc nào.</li>
                    <li>Hủy đăng ký nhận thông báo marketing qua email hoặc SMS.</li>
                    <li>Liên hệ <strong>hoangtranxuan04@gmaill.com</strong> để thực hiện các quyền trên.</li>
                </ul>
                <p style="font-size:13px;color:var(--gray-500);margin-top:12px;">
                    <i class="fa fa-calendar me-1"></i> Chính sách có hiệu lực từ ngày 01/01/2024. Chúng tôi có thể cập nhật chính sách này và sẽ thông báo qua email đến khách hàng.
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Highlight active policy nav link khi scroll
const sections = document.querySelectorAll('.policy-section');
const navLinks = document.querySelectorAll('.policy-nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const top = section.getBoundingClientRect().top;
        if (top <= 120) current = section.getAttribute('id');
    });
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) link.classList.add('active');
    });
});

// Smooth scroll khi click
navLinks.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(link.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
</script>
@endpush

@endsection
