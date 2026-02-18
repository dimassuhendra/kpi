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

        // Mengambil user dengan relasi divisi dan report terbaru
        $users = User::whereIn('role', ['staff', 'manager', 'gm'])
            ->with(['divisi', 'latestReport'])
            ->latest()
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

        // Validasi diperluas agar manager bisa diupdate role-nya jika perlu
        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'role'      => 'required|in:staff,manager,gm'
        ]);

        $user->update([
            'divisi_id' => $request->divisi_id,
            'role'      => $request->role
        ]);

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

    /**
     * Fungsi Export PDF untuk satu User (Laporan Bulanan)
     */
    public function exportPdf(Request $request)
    {
        $user = User::with('divisi')->findOrFail($request->user_id);
        return $this->generateSinglePdf($user);
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        // Ambil data detail kegiatan user bulan ini
        $allDetails = KegiatanDetail::whereHas('dailyReport', function ($q) use ($user, $start, $end) {
            $q->where('user_id', $user->id)
                ->whereBetween('tanggal', [$start, $end]);
        })->get();

        $data = [
            'user' => $user,
            'date' => date('d F Y'),
            'periode' => $start->format('F Y'),
            'divisi_id' => $user->divisi_id
        ];

        if ($user->divisi_id == 2) {
            // --- LOGIKA KHUSUS INFRA --- [cite: 1]
            // Hitung count per kategori: Network, CCTV, GPS, Lainnya 
            $infraStats = [
                'Network' => $allDetails->where('kategori', 'Network')->count(),
                'CCTV'    => $allDetails->where('kategori', 'CCTV')->count(),
                'GPS'     => $allDetails->where('kategori', 'GPS')->count(),
                'Lainnya' => $allDetails->where('kategori', 'Lainnya')->count(),
            ];

            $data['infraStats'] = $infraStats;
            $data['total_case'] = array_sum($infraStats); // Total akumulasi [cite: 41]

        } else {
            // --- LOGIKA KHUSUS TAC --- [cite: 71]
            $caseDetails = $allDetails->where('tipe_kegiatan', 'case');

            $data['tacStats'] = [
                'total_case'     => $caseDetails->count(),
                'total_activity' => $allDetails->where('tipe_kegiatan', 'activity')->count(),
                'temuan_sendiri' => $caseDetails->where('temuan_sendiri', 1)->count(),
                'mandiri_count'  => $caseDetails->where('is_mandiri', 1)->count(),
                'avg_time'       => round($caseDetails->avg('value_raw') ?? 0, 1),
            ];
        }

        $pdf = Pdf::loadView('manager.exports.user-pdf', $data);
        return $pdf->stream("Performance_{$user->nama_lengkap}.pdf");
    }

    public function exportAll()
    {
        // Mengambil semua user dengan role staff/manager/gm
        $users = User::whereIn('role', ['staff'])->with('divisi')->get();

        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $allUserData = [];

        foreach ($users as $user) {
            $allUserData[] = $this->prepareUserData($user, $start, $end);
        }

        $pdf = Pdf::loadView('manager.exports.all-staff-pdf', [
            'allUserData' => $allUserData,
            'date' => date('d F Y'),
            'periode' => $start->format('F Y')
        ])->setPaper('a5', 'portrait'); // Set ukuran A5

        return $pdf->stream("Laporan_Seluruh_Staff.pdf");
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

    private function generateSinglePdf($user)
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $data = $this->prepareUserData($user, $start, $end);
        $data['date'] = date('d F Y');
        $data['periode'] = $start->format('F Y');

        return Pdf::loadView('manager.exports.user-pdf', $data)
            ->setPaper('a5', 'portrait') // Set ukuran A5
            ->stream("Performance_{$user->nama_lengkap}.pdf");
    }
}
