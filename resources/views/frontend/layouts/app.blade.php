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
                    <a href="{{ route('account.profile') }}" class="btn-mm-icon" style="color:var(--gray-700);">
                        <i class="fa fa-user-cog"></i>
                        <span class="d-none d-lg-inline">Tài khoản</span>
                    </a>
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
                    <span class="mm-badge" id="cart-count-badge" style="{{ count(session('cart', [])) == 0 ? 'display:none' : '' }}">{{ count(session('cart', [])) }}</span>
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

{{-- Flash messages: hiển thị qua toast (góc phải trên) thay vì banner inline,
     tránh hiện trùng 2 thông báo cho cùng một session flash. --}}

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

{{-- Toast container --}}
<div id="mm-toast-container" style="position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;"></div>

<style>
.mm-toast {
    background:#fff; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,.15);
    padding:14px 18px; min-width:260px; max-width:340px; display:flex; align-items:flex-start; gap:12px;
    animation: toastIn .3s ease; border-left:4px solid var(--primary);
}
.mm-toast.toast-error { border-left-color:#e74c3c; }
.mm-toast.toast-success { border-left-color:#27ae60; }
.mm-toast-icon { font-size:18px; margin-top:1px; }
.mm-toast-body { flex:1; }
.mm-toast-title { font-weight:600; font-size:13px; }
.mm-toast-msg { font-size:12px; color:#666; margin-top:2px; }
.mm-toast-close { cursor:pointer; color:#999; font-size:16px; }
@keyframes toastIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }

/* Live search dropdown */
#mm-search-dropdown {
    position:absolute; top:100%; left:0; right:0; background:#fff;
    border-radius:0 0 10px 10px; box-shadow:0 8px 24px rgba(0,0,0,.12);
    z-index:1000; max-height:400px; overflow-y:auto; display:none;
}
.mm-search-item { display:flex; align-items:center; gap:12px; padding:10px 16px; text-decoration:none; color:inherit; border-bottom:1px solid #f5f5f5; }
.mm-search-item:hover { background:#f8f8f8; }
.mm-search-item img { width:44px; height:44px; object-fit:contain; border-radius:6px; border:1px solid #eee; }
.mm-search-item-name { font-size:13px; font-weight:500; }
.mm-search-item-price { font-size:12px; color:var(--red); }
.mm-search-more { text-align:center; padding:10px; font-size:13px; color:var(--primary); font-weight:500; cursor:pointer; }
</style>

<script>
// ── Toast system ──────────────────────────────────────────────
function mmToast(msg, type = 'success') {
    const icons = { success: '✅', error: '❌', info: 'ℹ️' };
    const container = document.getElementById('mm-toast-container');
    const el = document.createElement('div');
    el.className = `mm-toast toast-${type}`;
    el.innerHTML = `
        <div class="mm-toast-icon">${icons[type] ?? '🔔'}</div>
        <div class="mm-toast-body"><div class="mm-toast-msg">${msg}</div></div>
        <span class="mm-toast-close" onclick="this.closest('.mm-toast').remove()">×</span>
    `;
    container.appendChild(el);
    setTimeout(() => el.style.cssText += 'opacity:0;transition:opacity .4s', 3000);
    setTimeout(() => el.remove(), 3400);
}

// ── AJAX add to cart ─────────────────────────────────────────
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-ajax-cart]');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('form');
    if (!form) return;

    btn.disabled = true;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: new FormData(form),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            mmToast(data.message, 'success');
            // Cập nhật badge số lượng giỏ hàng
            const badge = document.getElementById('cart-count-badge');
            if (badge) {
                badge.textContent = data.cart_count;
                badge.style.display = data.cart_count > 0 ? 'inline-flex' : 'none';
            }
        }
    })
    .catch(() => mmToast('Có lỗi xảy ra, vui lòng thử lại.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
});

// ── Live search ───────────────────────────────────────────────
(function() {
    const searchForm  = document.querySelector('.mm-search');
    if (!searchForm) return;
    const input       = searchForm.querySelector('input[name="keyword"]');
    const dropdown    = document.createElement('div');
    dropdown.id       = 'mm-search-dropdown';
    searchForm.style.position = 'relative';
    searchForm.appendChild(dropdown);

    let timer;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { dropdown.style.display = 'none'; return; }

        timer = setTimeout(() => {
            fetch(`{{ route('search') }}?keyword=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.results.length) { dropdown.style.display = 'none'; return; }
                dropdown.innerHTML = data.results.map(p => `
                    <a href="${p.url}" class="mm-search-item">
                        <img src="${p.photo_url}" alt="${p.name}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'44\' height=\'44\'%3E%3Crect width=\'44\' height=\'44\' fill=\'%23f5f5f5\'/%3E%3C/svg%3E'">
                        <div>
                            <div class="mm-search-item-name">${p.name}</div>
                            <div class="mm-search-item-price">${p.final_price}</div>
                        </div>
                    </a>
                `).join('') + (data.total > 8 ? `<div class="mm-search-more" onclick="document.querySelector('.mm-search').submit()">Xem tất cả ${data.total} kết quả →</div>` : '');
                dropdown.style.display = 'block';
            });
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!searchForm.contains(e.target)) dropdown.style.display = 'none';
    });
})();

// ── Flash messages → Toast ────────────────────────────────────
@if(session('success')) mmToast('{{ session('success') }}', 'success'); @endif
@if(session('error'))   mmToast('{{ session('error') }}',   'error');   @endif
</script>

@stack('scripts')
</body>
</html>
