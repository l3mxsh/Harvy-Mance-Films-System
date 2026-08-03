<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class Login extends Controller
{
    public function __invoke()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'admin' || $user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This account is not active or does not have admin access.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            $user->update(['last_login_at' => now()]);
            ActivityLog::log('auth.login', "Admin {$user->name} logged in.");
            return redirect()->route('admin.dashboard');
        }

        $maxAttempts = 5;
        $key = md5('login' . $request->ip());
        $remaining = RateLimiter::retriesLeft($key, $maxAttempts);
        $attempts = $maxAttempts - $remaining;

        return back()->withErrors([
            'email' => "Invalid credentials. You have {$remaining} attempts remaining.",
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        ActivityLog::log('auth.logout', $user ? "Admin {$user->name} logged out." : 'Session ended.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
