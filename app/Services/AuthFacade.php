<?php

namespace App\Services;

use App\Exceptions\AccountLockedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthFacade
{
    public static function isLoggedIn(): bool
    {
        return Auth::guard('customer')->check();
    }

    public static function login(LoginRequest $request): bool
    {
        $customer = Customer::where('email', $request->email)->first();

        if ($customer && ! $customer->is_active) {
            throw new AccountLockedException;
        }

        if (Auth::guard('customer')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            CartService::getInstance()->mergeSessionCartIntoDb(Auth::guard('customer')->id());

            return true;
        }

        return false;
    }

    public static function register(RegisterRequest $request): Customer
    {
        return Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? '',
            'address' => $request->address ?? '',
            'password' => Hash::make($request->password),
        ]);
    }

    public static function logout(Request $request): void
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
