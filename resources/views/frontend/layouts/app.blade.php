<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'MediaMart - Điện tử & Công nghệ')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/mediamart.css') }}">
    @stack('styles')
</head>
<body>

{{-- ==================== HEADER ==================== --}}
<div id="mm-header">
    <div class="container">
        <div class="header-inner">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="logo d-flex align-items-center gap-2 text-decoration-none">
                <span style="background:var(--red);color:#fff;width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;">M</span>
                <span>MediaMart</span>
            </a>

            {{-- Search --}}
            <form action="{{ route('search') }}" method="GET" class="mm-search">
                <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm, thương hiệu..." value="{{ request('keyword') }}">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>

            {{-- Actions --}}
            <div class="mm-header-actions">
                @auth('customer')
                    <span class="d-none d-md-flex align-items-center gap-1" style="font-size:13px;color:var(--gray-700);">
                        <i class="fa fa-user-circle" style="color:var(--red);"></i>
                        <strong>{{ Auth::guard('customer')->user()->name }}</strong>
                    </span>
                    <a href="{{ route('orders.index') }}" class="btn-mm-icon btn-mm-orders">
                        <i class="fa fa-box"></i>
                        <span class="d-none d-lg-inline">Đơn hàng</span>
                    </a>
                    <form action="{{ route('account.logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-mm-icon btn-mm-logout">
                            <i class="fa fa-sign-out-alt"></i>
                            <span class="d-none d-lg-inline">Đăng xuất</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('account.login') }}" class="btn-mm-icon btn-mm-login">
                        <i class="fa fa-sign-in-alt"></i>
                        <span class="d-none d-sm-inline">Đăng nhập</span>
                    </a>
                    <a href="{{ route('account.register') }}" class="btn-mm-icon btn-mm-register">
                        <i class="fa fa-user-plus"></i>
                        <span class="d-none d-sm-inline">Đăng ký</span>
                    </a>
                @endauth

                <a href="{{ route('wishlist.index') }}" class="btn-mm-icon btn-mm-wish">
                    <i class="fa fa-heart"></i>
                    <span class="d-none d-sm-inline">Yêu thích</span>
                    <span class="mm-badge">{{ count(session('wishlist', [])) }}</span>
                </a>
                <a href="{{ route('cart.index') }}" class="btn-mm-icon btn-mm-cart">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="d-none d-sm-inline">Giỏ hàng</span>
                    <span class="mm-badge">{{ count(session('cart', [])) }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Navbar --}}
    <nav id="mm-navbar">
        <div class="container">
            <div class="nav-inner">
                <div class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="fa fa-house"></i> Trang chủ
                    </a>
                </div>
                @foreach(\App\Models\Category::root()->orderBy('id')->get() as $cat)
                    <div class="nav-item">
                        <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="nav-link">
                            {{ $cat->name }}
                            @if($cat->children->count())
                                <i class="fa fa-chevron-down caret-icon"></i>
                            @endif
                        </a>
                        @if($cat->children->count())
                            <div class="dropdown-menu">
                                @foreach($cat->children as $child)
                                    <a href="{{ route('products.index', ['category_id' => $child->id]) }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                <div class="nav-item">
                    <a href="{{ route('news.index') }}" class="nav-link">
                        <i class="fa fa-newspaper"></i> Tin tức
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('about') }}" class="nav-link">
                        <i class="fa fa-building"></i> Giới thiệu
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('policy') }}" class="nav-link">
                        <i class="fa fa-shield-halved"></i> Chính sách
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('contact') }}" class="nav-link">
                        <i class="fa fa-phone"></i> Liên hệ
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>

{{-- Flash messages --}}
<div class="container" style="padding-top:14px;">
    @if(session('success'))
        <div class="mm-alert mm-alert-success">
            <span><i class="fa fa-check-circle me-2"></i>{{ session('success') }}</span>
            <button class="mm-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mm-alert mm-alert-error">
            <span><i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}</span>
            <button class="mm-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
</div>

{{-- Main content --}}
<div id="mm-content">
    <div class="container">
        @yield('content')
    </div>
</div>

{{-- Footer --}}
<footer id="mm-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-logo">
                    <span style="background:var(--red);color:#fff;padding:3px 10px;border-radius:6px;margin-right:6px;">M</span>MediaMart
                </div>
                <p>Hệ thống bán thiết bị điện tử & công nghệ uy tín hàng đầu Việt Nam. Cam kết chính hãng, giá tốt nhất.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" style="width:34px;height:34px;background:rgba(255,255,255,.1);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#9e9e9e;transition:all .2s;" onmouseover="this.style.background='var(--red)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='#9e9e9e'"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="width:34px;height:34px;background:rgba(255,255,255,.1);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#9e9e9e;transition:all .2s;" onmouseover="this.style.background='var(--red)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='#9e9e9e'"><i class="fab fa-youtube"></i></a>
                    <a href="#" style="width:34px;height:34px;background:rgba(255,255,255,.1);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#9e9e9e;transition:all .2s;" onmouseover="this.style.background='var(--red)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='#9e9e9e'"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <h5>Danh mục</h5>
                @foreach(\App\Models\Category::root()->orderBy('id')->get() as $cat)
                    <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="footer-link">{{ $cat->name }}</a>
                @endforeach
            </div>
            <div class="col-md-3 col-6">
                <h5>Liên kết</h5>
                <a href="{{ route('home') }}" class="footer-link">Trang chủ</a>
                <a href="{{ route('news.index') }}" class="footer-link">Tin tức công nghệ</a>
                <a href="{{ route('about') }}" class="footer-link">Giới thiệu</a>
                <a href="{{ route('policy') }}" class="footer-link">Chính sách</a>
                <a href="{{ route('contact') }}" class="footer-link">Liên hệ</a>
                <a href="{{ route('orders.index') }}" class="footer-link">Đơn hàng của tôi</a>
                <a href="{{ route('account.login') }}" class="footer-link">Tài khoản</a>
            </div>
            <div class="col-md-3">
                <h5>Liên hệ</h5>
                <div class="footer-contact-item"><i class="fa fa-map-marker-alt"></i> 55 Giải Phóng, Hà Nội</div>
                <div class="footer-contact-item"><i class="fa fa-phone"></i> 0378106753</div>
                <div class="footer-contact-item"><i class="fa fa-envelope"></i> hoangtranxuan04@gmaill.com</div>
                <div class="footer-contact-item"><i class="fa fa-clock"></i> T2–T7: 8:00 – 21:00</div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            © {{ date('Y') }} MediaMart. All rights reserved. Thiết kế bởi MediaMart Team.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
