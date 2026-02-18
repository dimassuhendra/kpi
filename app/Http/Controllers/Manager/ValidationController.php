<?php

namespace App\Http\Controllers\Manager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DailyReport;


class ValidationController extends Controller
{
    public function validationIndex(Request $request)
    {
        $pendingReports = DailyReport::with('user')
            ->where('status', 'pending')
            ->latest('tanggal')
            ->get();

        return view('manager.validation', compact('pendingReports'));
    }

    public function validationShow($id)
    {
        try {
            $report = DailyReport::with(['user', 'details.variabelKpi'])->findOrFail($id);

            $cases = $report->details->where('tipe_kegiatan', 'case');
            $activities = $report->details->where('tipe_kegiatan', 'activity');

            return view('manager.partials.validation-detail', compact('report', 'cases', 'activities'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function validationUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan_manager' => 'nullable|string' // Opsional: alasan reject
        ]);

        $report = DailyReport::findOrFail($id);
        $report->update([
            'status' => $request->status,
            'keterangan_manager' => $request->keterangan_manager,
            'validated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function validationStore(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:daily_reports,id',
            'status'    => 'required|in:approved,rejected',
            'catatan'   => 'nullable|string'
        ]);

        $report = DailyReport::with(['user', 'details'])->findOrFail($request->report_id);

        $report->update([
            'status'           => $request->status,
            'catatan_manager'  => $request->catatan,
        ]);

        return redirect()->route('manager.approval.index')->with('success', 'Status laporan berhasil diperbarui.');
    }

}
