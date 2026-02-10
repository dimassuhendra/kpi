<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:staff,manager'
        ]);

        $loginData = $request->only('email', 'password');

        if (Auth::attempt($loginData)) {
            $user = Auth::user();

            if ($user->role !== $credentials['role']) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses ditolak. Peran Anda tidak sesuai.']);
            }

            $request->session()->regenerate();

            return redirect()->intended($user->role == 'manager' ? route('manager.dashboard') : route('staff.input'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->only('email'));
    }
}
