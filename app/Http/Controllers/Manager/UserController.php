<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StaffPerformanceExport; // Nanti kita buat file exportnya

class UserController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();

        $users = User::where('role', 'staff') // Pastikan ada kutip di 'staff'
            ->with('divisi')
            ->withCount([
                'details as total_case',
                'details as mandiri_count' => function ($q) {
                    $q->where('is_mandiri', 1); // Di SQL lu tipe datanya tinyint, pake 1
                },
                'details as inisiatif_count' => function ($q) {
                    $q->where('temuan_sendiri', 1); // Di SQL lu tipe datanya tinyint, pake 1
                }
            ])
            ->withAvg('dailyReports as avg_kpi', 'total_nilai_harian')
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
            'divisi_id'    => 'required|exists:divisi,id'
        ]);

        // 2. Create User
        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => explode('@', $request->email)[0], // Bikin username otomatis dari email
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'staff',
            'divisi_id'    => $request->divisi_id,
        ]);

        return back()->with('success', 'Staff berhasil ditambahkan ke database.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'divisi_id' => $request->divisi_id
        ]);

        return back()->with('success', 'Divisi staff ' . $user->nama_lengkap . ' berhasil diperbarui.');
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
