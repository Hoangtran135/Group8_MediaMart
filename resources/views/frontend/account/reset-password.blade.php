@extends('frontend.layouts.app')
@section('title', 'Đặt lại mật khẩu - MediaMart')

@section('content')
<div class="auth-card">
    <div class="auth-card-header">
        <h3><i class="fa fa-lock me-2"></i>Đặt lại mật khẩu</h3>
        <p>Nhập mật khẩu mới cho tài khoản của bạn.</p>
    </div>
    <div class="auth-card-body">
        @if($errors->any())
            <div class="auth-errors">
                <i class="fa fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('account.password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $email) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-auth">Đặt lại mật khẩu</button>
        </form>
    </div>
</div>
@endsection
