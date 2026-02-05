<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KpiSubmission;
use App\Models\KpiVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $divisionId = Auth::user()->division_id;
        $staffMembers = User::where('division_id', $divisionId)->where('role', 'staff')->get();

        $staffA = $request->get('staff_a', $staffMembers->first()->id ?? null);
        $staffB = $request->get('staff_b', $staffMembers->last()->id ?? null);

        $performanceTrend = $this->getPerformanceTrend($staffA, $staffB);

        $variableStrength = $this->getVariableStrength($divisionId);

        $busyHours = $this->getBusyHours($divisionId);

        return view('manager.analytics', compact('staffMembers', 'performanceTrend', 'variableStrength', 'busyHours'));
    }

    private function getPerformanceTrend($idA, $idB)
    {
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $getData = function ($id) use ($dates) {
            if (!$id) return [];
            return $dates->map(function ($date) use ($id) {
                return KpiSubmission::where('user_id', $id)
                    ->whereDate('created_at', $date)
                    ->avg('total_final_score') ?? 0;
            });
        };

        return [
            'labels' => $dates->map(fn($d) => date('d M', strtotime($d))),
            'staffA' => $getData($idA),
            'staffB' => $getData($idB),
            'nameA' => User::find($idA)->name ?? 'Staff A',
            'nameB' => User::find($idB)->name ?? 'Staff B',
        ];
    }

    private function getVariableStrength($divId)
    {
        $vars = KpiVariable::where('division_id', $divId)->get();
        return [
            'labels' => $vars->pluck('name'),
            'values' => $vars->map(function ($v) {
                return DB::table('kpi_details')->where('kpi_variable_id', $v->id)->avg('staff_value') ?? 0;
            })
        ];
    }

    private function getBusyHours($divId)
    {
        return KpiSubmission::select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as total'))
            ->whereHas('user', fn($q) => $q->where('division_id', $divId))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour')->all();
    }
}
