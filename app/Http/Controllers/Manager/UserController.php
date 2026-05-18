<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Divisi;
use App\Models\KegiatanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user
     */
    public function index()
    {
        $divisis = Divisi::all();

        $users = User::with(['divisi', 'latestReport'])
            ->whereIn('role', ['staff', 'manager', 'gm'])
            ->where('users.id', '!=', 22)
            // Mengambil created_at laporan terakhir sebagai virtual column 'last_report_date'
            ->addSelect([
                'last_report_date' => \App\Models\DailyReport::select('created_at')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->take(1)
            ])
            // 1. Urutan Role (GM > Manager > Staff)
            ->orderByRaw("CASE 
            WHEN role = 'gm' THEN 1 
            WHEN role = 'manager' THEN 2 
            ELSE 3 
            END ASC")
            // 2. Jika GM/Manager, urut Nama A-Z
            ->orderBy('nama_lengkap', 'asc')
            // 3. Jika Staff, urut berdasarkan laporan terbaru (yang baru kita ambil di atas)
            ->orderBy('last_report_date', 'desc')
            ->get();

        return view('manager.users', compact('users', 'divisis'));
    }

    /**
     * Menyimpan user baru (Staff/Managerial)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'divisi_id'    => 'required|exists:divisi,id',
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

        return back()->with('success', 'User berhasil ditambahkan ke sistem.');
    }

    /**
     * Memperbarui data user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi diperluas agar bisa update nama, email, dan password opsional
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $id,
            'password'     => 'nullable|min:6',
            'divisi_id'    => 'required|exists:divisi,id',
            'role'         => 'required|in:staff,manager,gm'
        ]);

        $dataUpdate = [
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'divisi_id'    => $request->divisi_id,
            'role'         => $request->role
        ];

        // Jika form password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataUpdate);

        return back()->with('success', "Data {$user->nama_lengkap} berhasil diperbarui.");
    }

    /**
     * Menghapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Proteksi agar tidak menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun yang sedang digunakan!');
        }

        $nama = $user->nama_lengkap;
        $user->delete();

        return back()->with('success', "Staff bernama $nama berhasil dihapus.");
    }

    // Helper untuk menyiapkan data agar kode tidak duplikat
    private function prepareUserData($user, $start, $end)
    {
        $allDetails = \App\Models\KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $start, $end) {
            $q->where('user_id', $user->id)->whereBetween('tanggal', [$start, $end]);
        })->get();

        $data = [
            'user' => $user,
            'divisi_id' => $user->divisi_id,
        ];

        if ($user->divisi_id == 2) {
            $infraStats = [
                'Network' => $allDetails->where('kategori', 'Network')->count(),
                'CCTV'    => $allDetails->where('kategori', 'CCTV')->count(),
                'GPS'     => $allDetails->where('kategori', 'GPS')->count(),
                'Lainnya' => $allDetails->where('kategori', 'Lainnya')->count(),
            ];
            $data['infraStats'] = $infraStats;
            $data['total_case'] = array_sum($infraStats);
        } else {
            $caseDetails = $allDetails->where('tipe_kegiatan', 'case');
            $data['tacStats'] = [
                'total_case'     => $caseDetails->count(),
                'total_activity' => $allDetails->where('tipe_kegiatan', 'activity')->count(),
                'temuan_sendiri' => $caseDetails->where('temuan_sendiri', 1)->count(),
                'mandiri_count'  => $caseDetails->where('is_mandiri', 1)->count(),
                'avg_time'       => round($caseDetails->avg('value_raw') ?? 0, 1),
            ];
        }
        return $data;
    }

    public function show($id)
    {
        $user = User::with('divisi')->findOrFail($id);

        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        // Gunakan helper yang sudah ada untuk mengambil data statistik
        $stats = $this->prepareUserData($user, $start, $end);

        return response()->json([
            'user' => $user,
            'stats' => $stats
        ]);
    }
}
