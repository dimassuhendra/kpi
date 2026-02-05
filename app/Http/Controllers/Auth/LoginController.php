<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:staff,manager'
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            if ($user->role !== $request->role) {
                Auth::logout();
                return back()->withErrors(['role' => 'Akun tidak terdaftar sebagai ' . $request->role]);
            }

            $request->session()->regenerate();
            return redirect()->intended($user->role == 'manager' ? '/dashboard/manager' : '/dashboard/staff');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }
}