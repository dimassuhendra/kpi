<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KpiSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $managerDivId = Auth::user()->division_id;

        $pendingSubmissions = KpiSubmission::with(['user', 'details.variable', 'caseLogs']) // Tambahkan caseLogs
            ->whereHas('user', function ($q) use ($managerDivId) {
                $q->where('division_id', $managerDivId);
            })->where('status', 'pending')
            ->orderBy('created_at', 'desc')->get();

        $historySubmissions = KpiSubmission::with('user')
            ->whereHas('user', function ($q) use ($managerDivId) {
                $q->where('division_id', $managerDivId);
            })->whereIn('status', ['approved', 'rejected'])
            ->orderBy('approved_at', 'desc')->take(10)->get();

        return view('manager.approval', compact('pendingSubmissions', 'historySubmissions'));
    }

    public function process(Request $request, $id)
    {
        $submission = KpiSubmission::findOrFail($id);

        if ($request->status == 'approved') {
            $totalFinalScore = 0;

            // Ambil semua detail input staff untuk laporan ini
            foreach ($submission->details as $detail) {
                // Ambil bobot variabel yang berlaku saat ini
                // Jika variabel dihapus atau bobot 0, kita beri default 0
                $weight = $detail->variable->weight ?? 0;

                // RUMUS: (Nilai Staff * Bobot) / 100
                // Contoh: Nilai 90, Bobot 20% -> (90 * 20) / 100 = 18
                $calculatedScore = ($detail->staff_value * $weight) / 100;

                // Update skor per item
                $detail->update([
                    'calculated_score' => $calculatedScore
                ]);

                // Tambahkan ke total
                $totalFinalScore += $calculatedScore;
            }

            // Simpan hasil akhir ke submission
            $submission->update([
                'status' => 'approved',
                'total_final_score' => $totalFinalScore,
                'is_on_time' => $request->is_on_time,
                'manager_feedback' => $request->manager_feedback,
                'approved_at' => now()
            ]);

            return redirect()->route('manager.approval.index')->with('success', 'Laporan disetujui dengan skor akhir: ' . $totalFinalScore);
        } else {
            // Jika ditolak
            $submission->update([
                'status' => 'rejected',
                'manager_feedback' => $request->manager_feedback
            ]);

            return redirect()->route('manager.approval.index')->with('error', 'Laporan telah ditolak.');
        }
    }
}
