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

            // 1. Pra-kalkulasi Skor Teknis Berdasarkan Case Logs (Per Baris)
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
            }

            // 2. Loop Setiap Variabel KPI untuk Menentukan Nilai Akhir
            foreach ($submission->details as $detail) {
                $variable = $detail->variable;
                if (!$variable || !$variable->is_active) continue;

                $weight = (float) $variable->weight;
                $scoreBase = 0;
                $varName = strtolower($variable->variable_name);

                // LOGIKA A: Variabel Teknis (Response Time / Case Detection)
                // Kita menggunakan rata-rata skor per baris yang sudah dihitung di atas
                if ($variable->input_type === 'case_list') {
                    // Semua variabel berbasis case_list mendapatkan skor rata-rata teknis yang sama
                    $scoreBase = $avgTechnicalScore;
                }

                // LOGIKA B: Variabel Managerial (Input Manual Manager)
                // Mengambil nilai dari dropdown scoring_matrix yang dipilih manager
                elseif ($variable->input_type === 'dropdown') {
                    // Manager memilih key (misal: 'tepat_waktu') melalui request
                    // Request key disesuaikan dengan ID variabel agar unik
                    $inputKey = "var_" . $variable->id;
                    $selectedOption = $request->input($inputKey);

                    $matrix = json_decode($variable->scoring_matrix, true) ?? [];
                    $scoreBase = $matrix[$selectedOption] ?? 0;

                    // Simpan pilihan manager ke correction
                    $detail->manager_correction = $selectedOption;
                }

                // Hitung skor akhir untuk variabel ini (Skor x Bobot / 100)
                $calculatedScore = ($scoreBase * $weight) / 100;

                // Update detail KPI
                $detail->update([
                    'calculated_score' => $calculatedScore,
                    'manager_correction' => $detail->manager_correction ?? $scoreBase
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
                ->with('success', 'KPI Disetujui! Skor Akhir: ' . number_format($totalFinalScore, 2));
        } else {
            $submission->update([
                'status' => 'rejected',
                'manager_feedback' => $request->manager_feedback
            ]);
            return redirect()->route('manager.approval.index')->with('error', 'Laporan KPI Ditolak.');
        }
    }
}
