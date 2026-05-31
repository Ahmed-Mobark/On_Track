<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (in_array($user->role, ['ADMIN', 'SUPER_ADMIN', 'CASHIER'])) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended('/');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // Signup bonus points
        $signupBonus = (int) \App\Models\SiteSetting::get('signup_bonus_points', 50);
        if ($signupBonus > 0) {
            $wallet = $user->getOrCreateWallet();
            $wallet->addPoints($signupBonus, 'مكافأة تسجيل حساب جديد', 'Signup', $user->id);
        }

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
