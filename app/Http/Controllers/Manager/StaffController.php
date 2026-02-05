<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $team = User::where('role', 'staff')
            ->with('division')
            ->latest()
            ->get();

        $divisions = Division::all();

        return view('manager.staff_index', compact('team', 'divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'division_id' => 'required|exists:divisions,id', 
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'division_id' => $request->division_id,
        ]);

        return redirect()->back()->with('success', 'Akun staff berhasil dibuat dan ditempatkan di divisi.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'division_id' => 'required|exists:divisions,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'division_id' => $request->division_id,
        ]);

        return redirect()->back()->with('success', 'Data staff berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun staff telah dihapus dari sistem.');
    }
}
