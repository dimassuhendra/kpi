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

            // 1. Kalkulasi Skor Teknis
            $caseLogs = $submission->caseLogs;
            $avgTechnicalScore = 0;
            if ($caseLogs->count() > 0) {
                $totalTicketScore = 0;
                foreach ($caseLogs as $log) {
                    // Gunakan angka murni untuk perhitungan
                    if ($log->response_time_minutes <= 15) $totalTicketScore += 100;
                    elseif ($log->response_time_minutes <= 30) $totalTicketScore += 80;
                    elseif ($log->response_time_minutes <= 60) $totalTicketScore += 60;
                    else $totalTicketScore += 40;
                }
                $avgTechnicalScore = $totalTicketScore / $caseLogs->count();
            }

            // 2. Loop Detail
            foreach ($submission->details as $detail) {
                $variable = $detail->variable;
                if (!$variable || !$variable->is_active) continue;

                $weight = (float) $variable->weight;
                $varName = strtolower($variable->variable_name);
                $scoreBase = 0;
                $selectedOption = null;

                // LOGIKA A: Case List
                if ($variable->input_type === 'case_list') {
                    $scoreBase = $avgTechnicalScore;
                    $selectedOption = 'system_calculated'; // Pastikan ini diisi
                }
                // LOGIKA B: Dropdown
                elseif ($variable->input_type === 'dropdown') {
                    $matrix = is_array($variable->scoring_matrix)
                        ? $variable->scoring_matrix
                        : json_decode($variable->scoring_matrix, true) ?? [];

                    if (str_contains($varName, 'waktu') || str_contains($varName, 'time')) {
                        $val = $request->input('is_on_time');
                        $selectedOption = ($val == "1") ? "tepat_waktu" : "terlambat";
                        $scoreBase = $matrix[$selectedOption] ?? ($val == "1" ? 100 : 50);
                    } elseif (str_contains($varName, 'revisi') || str_contains($varName, 'perbaikan')) {
                        $val = $request->input('needs_revision');
                        if ($val == "0") {
                            $selectedOption = "tidak_ada_revisi";
                            $scoreBase = 100;
                        } else {
                            $selectedOption = "ada_revisi";
                            $scoreBase = $matrix['ada_revisi'] ?? 70;
                        }
                    } else {
                        $selectedOption = $detail->staff_value;
                        $scoreBase = $matrix[$selectedOption] ?? 0;
                    }
                }

                // HITUNG DAN SIMPAN PER BARIS
                $calculatedScore = ($scoreBase * $weight) / 100;

                $detail->update([
                    'calculated_score' => (float)$calculatedScore,
                    'manager_correction' => $selectedOption
                ]);

                $totalFinalScore += $calculatedScore;
            }

            // 3. Simpan Hasil Akhir ke Tabel Submissions
            $submission->update([
                'status' => 'approved',
                'total_final_score' => (float)$totalFinalScore,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'manager_feedback' => $request->manager_feedback
            ]);

            return redirect()->route('manager.approval.index')
                ->with('success', 'KPI Berhasil Disetujui! Skor: ' . number_format($totalFinalScore, 1) . '%');
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
