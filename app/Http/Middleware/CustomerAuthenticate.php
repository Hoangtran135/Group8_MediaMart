<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('customer')->check()) {
            return redirect()->route('account.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        if (! Auth::guard('customer')->user()->is_active) {
            Auth::guard('customer')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('account.login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        return $next($request);
    }
}
