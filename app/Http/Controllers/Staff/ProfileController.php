<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

    public function editProfile()
    {
        $user = Auth::user();
        return view('staff.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email_prefix' => 'required|string|max:100',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update data dasar
        $user->nama_lengkap = $request->nama_lengkap;
        $user->username = $request->username;

        // Gabungkan prefix dengan domain tetap
        $user->email = $request->email_prefix . '@mybolo.com';

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
