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
        // Load data dengan caseLogs (untuk hitung jumlah tiket & response time)
        $submission = KpiSubmission::with(['details.variable', 'caseLogs'])->findOrFail($id);

        if ($request->status == 'approved') {
            $totalFinalScore = 0;

            // 1. Hitung Statistik dari Tiket (Daily Activity)
            $countTickets = $submission->caseLogs->count();
            $avgResponseTime = $submission->caseLogs->avg('response_time_minutes') ?? 0;

            // 2. Loop setiap variabel KPI untuk memberikan nilai
            foreach ($submission->details as $detail) {
                $variable = $detail->variable;
                if (!$variable) continue;

                $weight = (float) $variable->weight;
                $scoreBase = 0;
                $varName = strtolower($variable->variable_name);

                // LOGIKA A: Jika Variabel tentang JUMLAH CASE / TIKET
                if (str_contains($varName, 'case') || str_contains($varName, 'tiket') || str_contains($varName, 'jumlah')) {
                    // Contoh: Minimal 5 tiket untuk skor 100, jika kurang diproporsikan
                    $scoreBase = $countTickets >= 5 ? 100 : ($countTickets * 20);
                }

                // LOGIKA B: Jika Variabel tentang RESPONSE TIME / WAKTU PENGERJAAN
                elseif (str_contains($varName, 'response') || str_contains($varName, 'waktu pengerjaan')) {
                    // Contoh: Jika rata-rata <= 30 menit skor 100, jika > 30 skor 70
                    $scoreBase = $avgResponseTime <= 30 ? 100 : 70;
                }

                // LOGIKA C: Mengambil input dari FORM MANAGER (Tepat Waktu?)
                elseif (str_contains($varName, 'tepat waktu') || str_contains($varName, 'reporting')) {
                    // Jika manager pilih "Ya (1)", skor 100. Jika "Terlambat (0)", skor 0.
                    $scoreBase = $request->is_on_time == "1" ? 100 : 0;
                }

                // LOGIKA D: Mengambil input dari FORM MANAGER (Revisi?)
                elseif (str_contains($varName, 'revisi') || str_contains($varName, 'kualitas')) {
                    // Jika manager pilih "Tidak (0)", skor 100. Jika "Ada Revisi (1)", skor 50.
                    $scoreBase = $request->needs_revision == "0" ? 100 : 50;
                }

                // Hitung skor akhir untuk variabel ini (Skor Dasar x Bobot / 100)
                $calculatedScore = ($scoreBase * $weight) / 100;

                // Update ke tabel kpi_details agar tersimpan di DB
                $detail->update([
                    'staff_value' => $scoreBase, // Kita simpan angka 100/50/0 ke staff_value
                    'calculated_score' => $calculatedScore
                ]);

                $totalFinalScore += $calculatedScore;
            }

            // 3. Update Submission Utama
            $submission->update([
                'status' => 'approved',
                'total_final_score' => $totalFinalScore,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'manager_feedback' => $request->manager_feedback
            ]);

            return redirect()->route('manager.approval.index')->with('success', 'Laporan Berhasil Dinilai! Skor Akhir: ' . $totalFinalScore);
        } else {
            // Logika Reject...
            $submission->update(['status' => 'rejected', 'manager_feedback' => $request->manager_feedback]);
            return redirect()->route('manager.approval.index')->with('error', 'Laporan Ditolak.');
        }
    }
}
