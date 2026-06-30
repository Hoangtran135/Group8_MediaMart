<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthFacade;
use Illuminate\Http\Request;

/**
 * Facade Pattern: AccountController chỉ điều phối request/response,
 * mọi logic xác thực được ứy thác toàn bộ cho AuthFacade.
 */
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
        if (AuthFacade::login($request)) {
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
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
}
