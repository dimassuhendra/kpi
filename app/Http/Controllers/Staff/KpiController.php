<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KpiVariable;
use App\Models\KpiSubmission;
use App\Models\KpiDetail;
use App\Models\KpiCaseLog;
use Illuminate\Support\Facades\DB;
use Auth;

class KpiController extends Controller
{
    public function create()
    {
        // Ambil variabel KPI khusus divisi staff yang login (misal: TAC)
        $variables = KpiVariable::where('division_id', Auth::user()->division_id)->get();
        return view('staff.kpi_input', compact('variables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tickets.*.number' => 'required',
            'tickets.*.time' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Header Penilaian
            $submission = KpiSubmission::create([
                'user_id' => Auth::id(),
                'assessment_date' => now(),
                'status' => 'pending',
                'total_final_score' => 0, // Akan dihitung setelah di-ACC Manager
            ]);

            // 2. Simpan Log Tiket (Jika ada input tiket)
            if ($request->has('tickets')) {
                foreach ($request->tickets as $ticket) {
                    KpiCaseLog::create([
                        'kpi_submission_id' => $submission->id,
                        'ticket_number' => $ticket['number'],
                        'response_time_minutes' => $ticket['time'],
                        'is_problem_detected_by_staff' => isset($ticket['detected']) ? 1 : 0,
                    ]);
                }
            }

            // 3. Simpan Detail Variabel (Dropdown seperti Reporting, dsb)
            if ($request->has('vars')) {
                foreach ($request->vars as $varId => $value) {
                    KpiDetail::create([
                        'kpi_submission_id' => $submission->id,
                        'kpi_variable_id' => $varId,
                        'staff_value' => $value, // Menyimpan kunci dari scoring matrix
                        'calculated_score' => 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('staff.dashboard')->with('success', 'Laporan berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}