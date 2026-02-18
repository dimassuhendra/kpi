<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();

        $users = User::whereIn('role', ['staff', 'manager', 'gm'])
            ->with('divisi')
            ->latest()
            ->get();

        return view('manager.users', compact('users', 'divisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:6',
            'divisi_id'    => 'required',
            'role'         => 'required|in:staff,manager,gm'
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => explode('@', $request->email)[0],
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'divisi_id'    => $request->divisi_id,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'role'      => 'required|in:staff,gm'
        ]);

        $user->update([
            'divisi_id' => $request->divisi_id,
            'role'      => $request->role
        ]);

        return back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        $nama = $user->nama_lengkap;
        $user->delete();

        return back()->with('success', "Staff bernama $nama berhasil dihapus dari sistem.");
    }
}
