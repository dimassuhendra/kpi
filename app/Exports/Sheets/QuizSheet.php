<?php

namespace App\Exports\Sheets;

use App\Models\TechnicalAssessment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuizSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    public function title(): string
    {
        return 'Data Quiz';
    }

    public function view(): View
    {
        $query = TechnicalAssessment::with('user')
            ->whereBetween('created_at', [$this->filters['start_date'] . ' 00:00:00', $this->filters['end_date'] . ' 23:59:59']);

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return view('manager.exports.sheet_quiz', [
            'quizzes' => $query->get()
        ]);
    }
}
