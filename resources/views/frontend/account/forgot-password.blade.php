@extends('frontend.layouts.app')
@section('title', 'Quên mật khẩu - MediaMart')

@section('content')
<div class="auth-card">
    <div class="auth-card-header">
        <h3><i class="fa fa-key me-2"></i>Quên mật khẩu</h3>
        <p>Nhập email để nhận liên kết đặt lại mật khẩu.</p>
    </div>
    <div class="auth-card-body">
        @if(session('success'))
            <div class="auth-errors" style="background:#e7f7ee;color:#1a7a3d;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="auth-errors">
                <i class="fa fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('account.password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                       placeholder="example@gmail.com" required>
            </div>
            <button type="submit" class="btn-auth">Gửi liên kết đặt lại</button>
        </form>
        <div class="auth-divider">Đã nhớ mật khẩu?</div>
        <a href="{{ route('account.login') }}" class="btn-auth" style="display:block;text-align:center;background:transparent;border:2px solid var(--red);color:var(--red);margin-top:0;">
            Quay lại đăng nhập
        </a>
    </div>
</div>
@endsection
