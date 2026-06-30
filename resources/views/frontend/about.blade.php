@extends('frontend.layouts.app')
@section('title', 'Giới thiệu - MediaMart')

@section('content')

{{-- Page Title --}}
<div class="page-title-bar">
    <h1><i class="fa fa-building" style="color:var(--red);margin-right:8px;"></i>Giới thiệu về MediaMart</h1>
    <span class="bc ms-auto">
        <a href="{{ route('home') }}">Trang chủ</a>
        <i class="fa fa-chevron-right mx-1" style="font-size:10px;"></i>
        Giới thiệu
    </span>
</div>

{{-- Hero Banner --}}
<div class="about-hero">
    <div class="about-hero-content">
        <div class="about-hero-badge"><i class="fa fa-award me-2"></i>Thương hiệu uy tín hàng đầu</div>
        <h2>Hơn 10 năm đồng hành<br>cùng công nghệ Việt</h2>
        <p>MediaMart – hệ thống bán lẻ thiết bị điện tử & công nghệ với hàng nghìn sản phẩm chính hãng, giá tốt nhất thị trường và dịch vụ hậu mãi tận tâm.</p>
        <a href="{{ route('products.index') }}" class="btn-about-hero">
            <i class="fa fa-shopping-bag me-2"></i>Khám phá sản phẩm
        </a>
    </div>
    <div class="about-hero-img">
        <div class="about-hero-circle">
            <span style="font-size:80px;">🏬</span>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="about-stats">
    <div class="about-stat-item">
        <div class="about-stat-number">10+</div>
        <div class="about-stat-label">Năm hoạt động</div>
    </div>
    <div class="about-stat-item">
        <div class="about-stat-number">500K+</div>
        <div class="about-stat-label">Khách hàng tin tưởng</div>
    </div>
    <div class="about-stat-item">
        <div class="about-stat-number">50+</div>
        <div class="about-stat-label">Chi nhánh toàn quốc</div>
    </div>
    <div class="about-stat-item">
        <div class="about-stat-number">10K+</div>
        <div class="about-stat-label">Sản phẩm đang bán</div>
    </div>
</div>

{{-- Story --}}
<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="about-card h-100">
            <div class="about-card-icon"><i class="fa fa-flag"></i></div>
            <h4>Câu chuyện thành lập</h4>
            <p>MediaMart được thành lập năm 2014 tại Hà Nội bởi những người trẻ đam mê công nghệ với sứ mệnh mang đến cho người tiêu dùng Việt Nam những sản phẩm điện tử chính hãng với mức giá phải chăng nhất.</p>
            <p>Từ một cửa hàng nhỏ ở 55 Giải Phóng, MediaMart đã không ngừng phát triển và mở rộng ra hơn 50 chi nhánh trên khắp cả nước, trở thành một trong những chuỗi bán lẻ điện tử được tin dùng nhất Việt Nam.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="about-card h-100">
            <div class="about-card-icon"><i class="fa fa-eye"></i></div>
            <h4>Tầm nhìn & Sứ mệnh</h4>
            <p><strong>Tầm nhìn:</strong> Trở thành hệ thống bán lẻ công nghệ số 1 Việt Nam, nơi mọi người có thể tiếp cận công nghệ hiện đại một cách dễ dàng và tin cậy.</p>
            <p><strong>Sứ mệnh:</strong> Cung cấp sản phẩm công nghệ chính hãng, dịch vụ tư vấn chuyên nghiệp và trải nghiệm mua sắm tuyệt vời cho mọi khách hàng trên toàn quốc.</p>
        </div>
    </div>
</div>

{{-- Core Values --}}
<div class="mt-4">
    <h3 class="about-section-title"><i class="fa fa-star me-2" style="color:var(--red);"></i>Giá trị cốt lõi</h3>
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div class="about-value-card">
                <div class="about-value-icon">✅</div>
                <h6>Chính hãng 100%</h6>
                <p>Tất cả sản phẩm đều được nhập khẩu và phân phối chính thức từ các hãng uy tín trên thế giới.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="about-value-card">
                <div class="about-value-icon">💰</div>
                <h6>Giá tốt nhất</h6>
                <p>Cam kết hoàn tiền nếu khách hàng tìm thấy nơi bán cùng sản phẩm với giá rẻ hơn trong vòng 7 ngày.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="about-value-card">
                <div class="about-value-icon">🛡️</div>
                <h6>Bảo hành chính hãng</h6>
                <p>Hỗ trợ bảo hành tại tất cả các trung tâm bảo hành chính hãng trên toàn quốc, không phát sinh chi phí.</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="about-value-card">
                <div class="about-value-icon">🚀</div>
                <h6>Giao hàng nhanh</h6>
                <p>Giao hàng toàn quốc trong 24h, nội thành Hà Nội & TP.HCM giao trong 2–4 giờ làm việc.</p>
            </div>
        </div>
    </div>
</div>

{{-- Why Choose Us --}}
<div class="about-why mt-4">
    <h3 class="about-section-title"><i class="fa fa-thumbs-up me-2" style="color:var(--red);"></i>Tại sao chọn MediaMart?</h3>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="about-why-item">
                <div class="about-why-icon"><i class="fa fa-headset"></i></div>
                <div>
                    <h6>Hỗ trợ 24/7</h6>
                    <p>Đội ngũ tư vấn viên luôn sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi qua hotline, chat trực tuyến và email.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="about-why-item">
                <div class="about-why-icon"><i class="fa fa-rotate-left"></i></div>
                <div>
                    <h6>Đổi trả trong 30 ngày</h6>
                    <p>Miễn phí đổi trả trong vòng 30 ngày nếu sản phẩm bị lỗi do nhà sản xuất, không cần lý do.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="about-why-item">
                <div class="about-why-icon"><i class="fa fa-credit-card"></i></div>
                <div>
                    <h6>Thanh toán đa dạng</h6>
                    <p>Hỗ trợ thanh toán COD, thẻ ngân hàng, ví điện tử (VNPay, Momo) và trả góp 0% lãi suất.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="about-why-item">
                <div class="about-why-icon"><i class="fa fa-store"></i></div>
                <div>
                    <h6>Hệ thống showroom rộng lớn</h6>
                    <p>Hơn 50 showroom trên toàn quốc để khách hàng trải nghiệm sản phẩm trực tiếp trước khi mua.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Team --}}
<div class="mt-4">
    <h3 class="about-section-title"><i class="fa fa-users me-2" style="color:var(--red);"></i>Ban lãnh đạo</h3>
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div class="about-team-card">
                <div class="about-team-avatar">👨‍💼</div>
                <h6>Trần Xuân Hoàng</h6>
                <span>Thành Viên 1</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="about-team-card">
                <div class="about-team-avatar">👨‍💼</div>
                <h6>Trần Văn Hiệp</h6>
                <span>Thành Viên 2</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="about-team-card">
                <div class="about-team-avatar">👨‍💻</div>
                <h6>Trần Huy Hoàng</h6>
                <span>Thành Viên 3</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="about-team-card">
                <div class="about-team-avatar">👨‍🎨</div>
                <h6>Nguyễn Minh Quang</h6>
                <span>Thành Viên 4</span>
            </div>
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="about-cta">
    <h3>Sẵn sàng trải nghiệm mua sắm cùng MediaMart?</h3>
    <p>Khám phá hàng nghìn sản phẩm công nghệ chính hãng với giá tốt nhất thị trường.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('products.index') }}" class="btn-about-cta-primary">
            <i class="fa fa-shopping-bag me-2"></i>Mua sắm ngay
        </a>
        <a href="{{ route('contact') }}" class="btn-about-cta-outline">
            <i class="fa fa-phone me-2"></i>Liên hệ chúng tôi
        </a>
    </div>
</div>

@endsection
