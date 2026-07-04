<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountLockedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function loginForm()
    {
        if (AuthFacade::isLoggedIn()) {
            return redirect()->route('home');
        }

        return view('frontend.account.login');
    }

    public function login(LoginRequest $request)
    {
        try {
            if (AuthFacade::login($request)) {
                return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
            }
        } catch (AccountLockedException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->onlyInput('email');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
    }

    public function registerForm()
    {
        if (AuthFacade::isLoggedIn()) {
            return redirect()->route('home');
        }

        return view('frontend.account.register');
    }

    public function register(RegisterRequest $request)
    {
        AuthFacade::register($request);

        return redirect()->route('account.login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    public function logout(Request $request)
    {
        AuthFacade::logout($request);

        return redirect()->route('home');
    }

    public function profile()
    {
        $customer = Auth::guard('customer')->user();

        return view('frontend.account.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($request->only('name', 'phone', 'address'));

        return back()->with('success', 'Đã cập nhật thông tin thành công!');
    }

    public function changePassword(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $customer->update(['password' => $request->password]);

        return back()->with('success', 'Đã đổi mật khẩu thành công!');
    }
}
