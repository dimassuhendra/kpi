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
            'role' => 'required|in:staff,manager,gm'
        ]);

        $loginData = $request->only('email', 'password');

        if (Auth::attempt($loginData)) {
            $user = Auth::user();

            $isManagerGroup = ($credentials['role'] == 'manager' && ($user->role == 'manager' || $user->role == 'gm'));
            $isStaffGroup = ($credentials['role'] == 'staff' && $user->role == 'staff');

            if (!$isManagerGroup && !$isStaffGroup) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses ditolak. Peran Anda tidak sesuai.']);
            }

            $request->session()->regenerate();

            if ($user->role == 'manager' || $user->role == 'gm') {
                return redirect()->intended(route('manager.dashboard'));
            }

            return redirect()->intended(route('staff.input'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->only('email'));
    }
}
