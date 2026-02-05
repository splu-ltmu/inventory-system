<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // ✅ TRIM to avoid "admin@gmail.com " issues
        $email = trim($request->email);
        $password = $request->password;

        if (!Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            return back()->with('error', 'Invalid email or password.')->withInput();
        }

        $request->session()->regenerate();

        // ✅ MUST be admin
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', 'This account is not an admin.');
        }

        return redirect()->route('admin.dashboard');
    }
}
