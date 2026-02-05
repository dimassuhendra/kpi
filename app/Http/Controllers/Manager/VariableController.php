<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KpiVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VariableController extends Controller
{
    public function index(Request $request)
    {
        $selectedDivisionId = $request->get('division_id', Auth::user()->division_id);

        $allDivisions = \App\Models\Division::all();

        $variables = KpiVariable::where('division_id', $selectedDivisionId)->get();
        $totalWeight = $variables->sum('weight');

        return view('manager.variables', compact('variables', 'totalWeight', 'allDivisions', 'selectedDivisionId'));
    }

    public function store(Request $request)
    {
        KpiVariable::create([
            'division_id' => $request->division_id,
            'variable_name' => $request->variable_name,
            'weight' => 0,
        ]);

        return redirect()->back()->with('success', 'Variabel baru berhasil ditambahkan!');
    }

    public function updateWeights(Request $request)
    {
        foreach ($request->weights as $id => $weight) {
            KpiVariable::where('id', $id)->update(['weight' => $weight]);
        }

        return redirect()->back()->with('success', 'Bobot penilaian berhasil diperbarui!');
    }

    public function autoAverage(Request $request)
    {
        $divisionId = $request->division_id;
        $variables = KpiVariable::where('division_id', $divisionId)->get();

        if ($variables->count() > 0) {
            $avgWeight = 100 / $variables->count();
            KpiVariable::where('division_id', $divisionId)->update(['weight' => $avgWeight]);
        }

        return redirect()->back()->with('success', 'Bobot divisi berhasil dibagi rata!');
    }

    public function destroy($id)
    {
        KpiVariable::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Variabel berhasil dihapus.');
    }
}
