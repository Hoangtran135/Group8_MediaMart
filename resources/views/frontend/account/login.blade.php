@extends('frontend.layouts.app')
@section('title', 'Đăng nhập - MediaMart')

@section('content')
<div class="auth-card">
    <div class="auth-card-header">
        <h3><i class="fa fa-sign-in-alt me-2"></i>Đăng nhập</h3>
        <p>Chào mừng bạn trở lại MediaMart!</p>
    </div>
    <div class="auth-card-body">
        @if($errors->any())
            <div class="auth-errors">
                <i class="fa fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('account.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       value="{{ old('email') }}" placeholder="example@gmail.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <div class="form-group" style="text-align:right;margin-top:-8px;">
                <a href="{{ route('account.password.request') }}" style="font-size:13px;color:var(--red);">Quên mật khẩu?</a>
            </div>
            <div class="form-group d-flex align-items-center gap-2">
                <input type="checkbox" name="remember" id="remember" style="width:15px;height:15px;accent-color:var(--red);">
                <label for="remember" style="font-size:13px;color:var(--gray-700);cursor:pointer;">Ghi nhớ đăng nhập</label>
            </div>
            <button type="submit" class="btn-auth">Đăng nhập</button>
        </form>
        <div class="auth-divider">Chưa có tài khoản?</div>
        <a href="{{ route('account.register') }}" class="btn-auth" style="display:block;text-align:center;background:transparent;border:2px solid var(--red);color:var(--red);margin-top:0;">
            Đăng ký ngay
        </a>
    </div>
</div>
@endsection
