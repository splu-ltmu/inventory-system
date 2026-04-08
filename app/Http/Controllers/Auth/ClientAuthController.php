<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Client\ClientSubaccountController;
use App\Models\ClientSubaccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.client-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->with('error', 'Invalid email or password.');
        }

        $request->session()->regenerate();

        if (!in_array(Auth::user()->role, ['client', 'subaccount'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->with('error', 'This account is not allowed to access the client area.');
        }

        if (Auth::user()->role === 'subaccount') {
            $subaccount = ClientSubaccount::where('user_id', Auth::id())->first();
            if ($subaccount) {
                return redirect()->route('client.account.subaccounts.show', $subaccount);
            }
        }

        return redirect()->route('client.dashboard');
    }
}
