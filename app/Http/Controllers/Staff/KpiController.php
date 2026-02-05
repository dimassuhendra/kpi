<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KpiVariable;
use App\Models\KpiSubmission;
use App\Models\KpiDetail;
use App\Models\KpiCaseLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KpiController extends Controller
{
    public function create()
    {
        $variables = KpiVariable::where('division_id', Auth::user()->division_id)->get();
        return view('staff.kpi_input', compact('variables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tickets' => 'required|array|min:1',
            'tickets.*.number' => 'required|string',
            'tickets.*.time' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Submission Header
            $submission = KpiSubmission::create([
                'user_id'           => Auth::id(),
                'assessment_date'   => now()->format('Y-m-d'),
                'status'            => 'pending',
                'total_final_score' => 0,
                'manager_feedback'  => '', // Penting: Kolom ini NOT NULL di DB
            ]);

            // 2. Simpan Log Tiket
            foreach ($request->tickets as $ticket) {
                KpiCaseLog::create([
                    'kpi_submission_id'            => $submission->id,
                    'ticket_number'                => $ticket['number'],
                    'response_time_minutes'        => $ticket['time'],
                    'is_problem_detected_by_staff' => isset($ticket['detected']) ? 1 : 0,
                ]);
            }

            // 3. Inisialisasi Detail Variabel KPI
            $variables = KpiVariable::where('division_id', Auth::user()->division_id)->get();
            foreach ($variables as $var) {
                KpiDetail::create([
                    'kpi_submission_id' => $submission->id,
                    'kpi_variable_id'   => $var->id,
                    'staff_value'       => '0',
                    'calculated_score'  => 0,
                ]);
            }

            DB::commit();
            return redirect()->route('staff.dashboard')->with('success', 'Laporan berhasil terkirim!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    
    public function history()
    {
        $submissions = KpiSubmission::where('user_id', Auth::id())
            ->orderBy('assessment_date', 'desc')
            ->paginate(10);

        return view('staff.kpi_history', compact('submissions'));
    }
}
