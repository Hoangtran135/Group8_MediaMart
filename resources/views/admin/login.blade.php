<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>Đăng nhập Admin - MediaMart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <style>
        body { background:#1a2226; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .login-box { background:#fff; border-radius:8px; padding:40px; width:380px; box-shadow:0 10px 30px rgba(0,0,0,.3); }
        .login-box h2 { text-align:center; margin-bottom:30px; color:#222; }
        .login-box h2 span { color:#e74c3c; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Media<span>Mart</span> Admin</h2>
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form action="{{ route('admin.login.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-danger btn-block btn-lg">Đăng nhập</button>
    </form>
    <p style="text-align:center;margin-top:15px;">
        <a href="{{ route('admin.password.request') }}">Quên mật khẩu?</a>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
</body>
</html>
