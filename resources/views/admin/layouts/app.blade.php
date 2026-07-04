<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Admin') - MediaMart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { background: #f4f6f9; }
        .sidebar { min-height:100vh; background:#222d32; padding:0; }
        .sidebar .brand { background:#1a2226; padding:15px 20px; color:#fff; font-size:18px; font-weight:bold; display:block; text-decoration:none; }
        .sidebar .brand span { color:#e74c3c; }
        .sidebar ul { list-style:none; padding:0; margin:0; }
        .sidebar ul li a { display:block; padding:12px 20px; color:#b8c7ce; text-decoration:none; font-size:14px; transition:.2s; border-left:3px solid transparent; }
        .sidebar ul li a:hover, .sidebar ul li.active a { background:#1e282c; color:#fff; border-left-color:#e74c3c; }
        .sidebar ul li a i { margin-right:8px; width:16px; }
        .main-content { padding:20px; }
        .topbar { background:#fff; padding:12px 20px; margin-bottom:20px; border-bottom:1px solid #dee2e6; display:flex; justify-content:space-between; align-items:center; }
        .card { background:#fff; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,.1); margin-bottom:20px; }
        .card-header { padding:14px 20px; border-bottom:1px solid #eee; font-weight:600; display:flex; justify-content:space-between; align-items:center; }
        .card-body { padding:20px; }
        .stat-card { border-radius:6px; color:#fff; padding:20px; text-align:center; }
    </style>
    @stack('styles')
</head>
<body>
<div class="row" style="margin:0;">

    <div class="col-md-2 col-sm-3 sidebar" style="padding:0;">
        <a href="{{ route('admin.dashboard') }}" class="brand">Media<span>Mart</span><br><small style="font-size:11px;opacity:.6;">Admin Panel</small></a>
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}"><i class="fa fa-box"></i> Sản phẩm</a>
            </li>
            <li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <a href="{{ route('admin.categories.index') }}"><i class="fa fa-list"></i> Danh mục</a>
            </li>
            <li class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                <a href="{{ route('admin.news.index') }}"><i class="fa fa-newspaper-o"></i> Tin tức</a>
            </li>
            <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}"><i class="fa fa-shopping-cart"></i> Đơn hàng</a>
            </li>
            <li class="{{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.vouchers.index') }}"><i class="fa fa-tag"></i> Voucher</a>
            </li>
            <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.customers.index') }}"><i class="fa fa-user"></i> Khách hàng</a>
            </li>
            <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}"><i class="fa fa-users"></i> Quản trị viên</a>
            </li>
            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width:100%;text-align:left;background:none;border:none;padding:12px 20px;color:#b8c7ce;font-size:14px;cursor:pointer;">
                        <i class="fa fa-sign-out"></i> Đăng xuất
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <div class="col-md-10 col-sm-9" style="padding:0;">
        <div class="topbar">
            <strong>@yield('title', 'Dashboard')</strong>
            <span><i class="fa fa-user-circle"></i> {{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
        </div>
        <div class="main-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
@stack('scripts')
</body>
</html>
