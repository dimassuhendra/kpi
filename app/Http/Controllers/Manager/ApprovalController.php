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

        $pendingSubmissions = KpiSubmission::with(['user', 'details.variable', 'caseLogs'])
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
        $submission = KpiSubmission::with(['details.variable', 'caseLogs'])->findOrFail($id);

        if ($request->status == 'approved') {
            $totalFinalScore = 0;

            // 1. Pra-kalkulasi Skor Teknis Berdasarkan Case Logs (Per Baris Tiket)
            $caseLogs = $submission->caseLogs;
            $totalTicketScore = 0;
            $avgTechnicalScore = 0;

            if ($caseLogs->count() > 0) {
                foreach ($caseLogs as $log) {
                    // Implementasi Threshold 15 Menit & Klasifikasi Skor
                    if ($log->response_time_minutes <= 15) {
                        $totalTicketScore += 100; // Sangat Baik (Aman)
                    } elseif ($log->response_time_minutes <= 30) {
                        $totalTicketScore += 80;  // Cukup
                    } elseif ($log->response_time_minutes <= 60) {
                        $totalTicketScore += 60;  // Lambat
                    } else {
                        $totalTicketScore += 40;  // Sangat Lambat
                    }
                }
                $avgTechnicalScore = $totalTicketScore / $caseLogs->count();
            } else {
                // Jika tidak ada tiket (kasus langka), berikan skor default agar tidak div zero
                $avgTechnicalScore = 0;
            }

            // 2. Loop Setiap Variabel KPI untuk Menentukan Nilai Akhir
            foreach ($submission->details as $detail) {
                $variable = $detail->variable;
                if (!$variable || !$variable->is_active) continue;

                $weight = (float) $variable->weight;
                $scoreBase = 0;
                $varName = strtolower($variable->variable_name);

                // LOGIKA A: Variabel Teknis (Input dari Case List)
                if ($variable->input_type === 'case_list') {
                    $scoreBase = $avgTechnicalScore;
                }

                // LOGIKA B: Variabel Managerial (Input Dropdown: Tepat Waktu / Revisi)
                elseif ($variable->input_type === 'dropdown') {
                    // Cek nama variabel untuk menentukan sumber input dari request
                    if (str_contains($varName, 'waktu')) {
                        // Mengambil input name="is_on_time", default "1" (Ya)
                        $selectedOption = $request->input('is_on_time', "1");
                    } elseif (str_contains($varName, 'revisi')) {
                        // Mengambil input name="needs_revision", default "0" (Tidak)
                        $selectedOption = $request->input('needs_revision', "0");
                    } else {
                        // Untuk dropdown custom lainnya jika menggunakan ID
                        $selectedOption = $request->input("var_" . $variable->id, "0");
                    }

                    // Penanganan Auto-Casting Scoring Matrix (Mencegah TypeError)
                    $matrix = is_array($variable->scoring_matrix)
                        ? $variable->scoring_matrix
                        : json_decode($variable->scoring_matrix, true) ?? [];

                    // Mengambil nilai angka dari matrix (misal pilihan "1" bernilai 100)
                    $scoreBase = $matrix[$selectedOption] ?? 0;

                    // Simpan pilihan manager (0/1) ke database
                    $detail->manager_correction = $selectedOption;
                }

                // Hitung skor akhir untuk variabel ini: (Skor Dasar * Bobot) / 100
                $calculatedScore = ($scoreBase * $weight) / 100;

                // Update per baris detail KPI
                $detail->update([
                    'calculated_score' => $calculatedScore,
                    'manager_correction' => $detail->manager_correction ?? $selectedOption ?? null
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

            return redirect()->route('manager.approval.index')
                ->with('success', 'KPI Berhasil Disetujui! Skor Akhir: ' . number_format($totalFinalScore, 1) . '%');
        } else {
            // Logika jika Manager menolak (Rejected)
            $submission->update([
                'status' => 'rejected',
                'manager_feedback' => $request->manager_feedback
            ]);
            return redirect()->route('manager.approval.index')->with('error', 'Laporan KPI telah dikembalikan/ditolak.');
        }
    }
}
