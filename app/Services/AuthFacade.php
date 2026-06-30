<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Facade Pattern: gói gọn toàn bộ quy trình xác thực khách hàng
 * (kiểm tra đã đăng nhập, đăng nhập, đăng ký, đăng xuất) đằng sau
 * một class duy nhất.
 *
 * AccountController chỉ gọi AuthFacade mà không cần biết chi tiết
 * về guard name, session regenerate, hash password...
 */
class AuthFacade
{
    public static function isLoggedIn(): bool
    {
        return Auth::guard('customer')->check();
    }

    /**
     * Thử đăng nhập, trả về true nếu thành công.
     */
    public static function login(LoginRequest $request): bool
    {
        if (Auth::guard('customer')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            return true;
        }

        return false;
    }

    /**
     * Tạo tài khoản khách hàng mới.
     */
    public static function register(RegisterRequest $request): Customer
    {
        return Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone ?? '',
            'address'  => $request->address ?? '',
            'password' => Hash::make($request->password),
        ]);
    }

    /**
     * Đăng xuất và huỷ session.
     */
    public static function logout(Request $request): void
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
