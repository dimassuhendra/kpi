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
            // 'time' tidak lagi required strictly numeric jika dipaksa 0 di backend
        ]);

        DB::beginTransaction();
        try {
            $submission = KpiSubmission::create([
                'user_id' => Auth::id(),
                'assessment_date' => now(),
                'status' => 'pending',
                'total_final_score' => 0,
            ]);

            if ($request->has('tickets')) {
                foreach ($request->tickets as $ticket) {
                    // Logika: Jika problem detected, paksa time ke 0
                    $isDetected = isset($ticket['detected']) && $ticket['detected'] == true;

                    KpiCaseLog::create([
                        'kpi_submission_id' => $submission->id,
                        'ticket_number' => $ticket['number'],
                        'response_time_minutes' => $isDetected ? 0 : $ticket['time'],
                        'is_problem_detected_by_staff' => $isDetected ? 1 : 0,
                    ]);
                }
            }

            // Simpan Detail Variabel (Hanya yang diinput staff)
            if ($request->has('vars')) {
                foreach ($request->vars as $varId => $value) {
                    KpiDetail::create([
                        'kpi_submission_id' => $submission->id,
                        'kpi_variable_id' => $varId,
                        'staff_value' => $value,
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

    public function history()
    {
        $submissions = KpiSubmission::where('user_id', Auth::id())
            ->orderBy('assessment_date', 'desc')
            ->paginate(10); // Menggunakan pagination agar rapi

        return view('staff.kpi_history', compact('submissions'));
    }
}
