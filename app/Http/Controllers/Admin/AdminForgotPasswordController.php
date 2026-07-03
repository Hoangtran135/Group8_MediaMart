<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AdminForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.forgot-password');
    }

    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Đã gửi liên kết đặt lại mật khẩu tới email của bạn.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('admin.reset-password', [
            'token' => $token,
            'email' => $request->get('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                // Admin model casts 'password' => 'hashed', so assigning the
                // plain value here lets Eloquent hash it automatically.
                $admin->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($admin));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.')
            : back()->withErrors(['email' => __($status)]);
    }
}
