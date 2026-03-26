<?php

namespace App\Exports\Sheets;

use App\Models\CustomerFeedback;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RatingSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function title(): string
    {
        return 'Data Rating';
    }

    public function view(): View
    {
        $query = CustomerFeedback::with('user')
            ->whereBetween('tanggal_survey', [$this->filters['start_date'], $this->filters['end_date']]);

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        // Tambahan filter divisi (lewat relasi user -> divisi)
        if (!empty($this->filters['divisi_name'])) {
            $query->whereHas('user.divisi', function ($q) {
                $q->where('nama_divisi', $this->filters['divisi_name']);
            });
        }

        return view('manager.exports.sheet_rating', [
            'ratings' => $query->get()
        ]);
    }
}
