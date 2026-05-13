<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KegiatanDetail;
use App\Models\DailyReport;
use App\Models\CustomerFeedback;
use App\Models\TechnicalAssessment;
use App\Models\LemburReport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class StorageController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua data dari berbagai tabel yang memiliki file
        $data = new Collection();

        // Contoh pengambilan data (ulangi pola ini untuk semua tabel)
        KegiatanDetail::whereNotNull('bukti_respon_time')
            ->orWhereNotNull('bukti_deteksi_dini')
            ->orWhereNotNull('foto_dokumentasi')
            ->with('dailyReport.user')->get()->each(function ($item) use ($data) {
                if ($item->bukti_respon_time) $data->push($this->formatRow($item, 'bukti_respon_time', 'Respon Time', $item->dailyReport->user));
                if ($item->bukti_deteksi_dini) $data->push($this->formatRow($item, 'bukti_deteksi_dini', 'Deteksi Dini', $item->dailyReport->user));
                if ($item->foto_dokumentasi) $data->push($this->formatRow($item, 'foto_dokumentasi', 'Dokumentasi', $item->dailyReport->user));
            });

        // Dari Daily Report (GPS)
        DailyReport::whereNotNull('bukti_report_gps')->with('user')->get()->each(function ($item) use ($data) {
            $data->push($this->formatRow($item, 'bukti_report_gps', 'GPS Report', $item->user));
        });

        // Dari Feedback Pelanggan
        CustomerFeedback::whereNotNull('bukti_survey')->with('user')->get()->each(function ($item) use ($data) {
            $data->push($this->formatRow($item, 'bukti_survey', 'Customer Feedback', $item->user));
        });

        // Dari Technical Assessment
        TechnicalAssessment::whereNotNull('bukti_kuis')->with('user')->get()->each(function ($item) use ($data) {
            $data->push($this->formatRow($item, 'bukti_kuis', 'Assessment', $item->user));
        });

        // Dari Lembur
        LemburReport::whereNotNull('foto_dokumentasi')->with('dailyReport.user')->get()->each(function ($item) use ($data) {
            $data->push($this->formatRow($item, 'foto_dokumentasi', 'Lembur', $item->dailyReport->user));
        });

        $data = $data->filter();

        // 2. Hitung Total Ukuran Seluruh File
        $totalSizeBytes = $data->sum('size_bytes');
        $totalSizeFormatted = $this->formatSizeUnits($totalSizeBytes);

        // 3. Sorting Logic
        $sort = $request->get('sort', 'tanggal');
        $direction = $request->get('direction', 'desc');

        if ($direction == 'asc') {
            $data = $data->sortBy($sort);
        } else {
            $data = $data->sortByDesc($sort);
        }

        // 4. Pagination
        $perPage = $request->get('show', 50);
        if ($perPage == 'all') $perPage = $data->count();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $items = new LengthAwarePaginator($currentItems, $data->count(), $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('manager.storage', compact('items', 'totalSizeFormatted', 'totalSizeBytes'));
    }

    private function formatRow($item, $field, $category, $user)
    {
        $path = $item->$field;

        // 1. Identifikasi Link Cloud
        // Jika path adalah URL valid (http/https), maka itu link cloud.
        // Kita return null karena ini tidak perlu dikelola di Storage Management.
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return null;
        }

        $size = 0;
        try {
            if (!empty($path) && $path !== '.' && Storage::disk('public')->exists($path)) {
                $size = Storage::disk('public')->size($path);

                // 2. Kondisi Tambahan: Ukuran Minimal
                // Jika Anda ingin tetap menerapkan filter ukuran (misal > 0.1 KB)
                if ($size < 100) { // 100 bytes = 0.1 KB
                    return null;
                }
            } else {
                // Jika file fisik tidak ditemukan di server, jangan tampilkan di daftar
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return (object)[
            'id' => $item->id,
            'uid' => uniqid(),
            'type' => get_class($item),
            'field' => $field,
            'nama_pengunggah' => $user->nama_lengkap ?? 'System',
            'folder' => $category,
            'path' => $path,
            'size_bytes' => $size,
            'size_human' => $this->formatSizeUnits($size),
            'tanggal' => $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-',
            'raw_date' => $item->created_at
        ];
    }

    public function bulkDestroy(Request $request)
    {
        $files = json_decode($request->selected_files, true);
        $count = 0;

        foreach ($files as $file) {
            $modelClass = $file['type'];
            $record = $modelClass::find($file['id']);

            if ($record) {
                $field = $file['field'];
                if ($record->$field && Storage::disk('public')->exists($record->$field)) {
                    Storage::disk('public')->delete($record->$field);
                    $record->$field = null;
                    $record->save();
                    $count++;
                }
            }
        }

        return back()->with('success', "$count file berhasil dihapus.");
    }

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }
}
