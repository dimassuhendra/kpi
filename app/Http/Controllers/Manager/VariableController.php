<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\KpiVariable;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VariableController extends Controller
{
    public function index(Request $request)
    {
        $selectedDivisionId = $request->get('division_id', Auth::user()->division_id);
        $allDivisions = Division::all();

        // Hanya mengambil variabel yang aktif (is_active = true) untuk ditampilkan di manajemen
        $variables = KpiVariable::where('division_id', $selectedDivisionId)
            ->where('is_active', true)
            ->get();

        $totalWeight = $variables->sum('weight');

        return view('manager.variables', compact('variables', 'totalWeight', 'allDivisions', 'selectedDivisionId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required',
            'variable_name' => 'required|string|max:255',
            'input_type' => 'required|in:case_list,dropdown,number,boolean'
        ]);

        $defaultMatrix = null;
        if ($request->input_type === 'dropdown') {
            $defaultMatrix = json_encode([
                'tepat_waktu' => 100,
                'terlambat' => 50,
                'tidak_mengisi' => 0
            ]);
        }

        KpiVariable::create([
            'division_id' => $request->division_id,
            'variable_name' => $request->variable_name,
            'input_type' => $request->input_type,
            'weight' => 0,
            'is_active' => true,
            'scoring_matrix' => $defaultMatrix,
        ]);

        return redirect()->back()->with('success', 'Variabel baru berhasil ditambahkan!');
    }

    public function updateWeights(Request $request)
    {
        // Validasi agar total bobot tidak melebihi 100 bisa ditambahkan di sini
        foreach ($request->weights as $id => $weight) {
            KpiVariable::where('id', $id)->update(['weight' => $weight]);
        }

        return redirect()->back()->with('success', 'Bobot penilaian berhasil diperbarui!');
    }

    public function autoAverage(Request $request)
    {
        $divisionId = $request->division_id;

        // Perubahan: Hanya menghitung variabel yang is_active = true
        $activeVariables = KpiVariable::where('division_id', $divisionId)
            ->where('is_active', true)
            ->get();

        $count = $activeVariables->count();

        if ($count > 0) {
            $avgWeight = 100 / $count;

            // Set bobot variabel yang aktif menjadi rata
            KpiVariable::where('division_id', $divisionId)
                ->where('is_active', true)
                ->update(['weight' => $avgWeight]);

            // Opsional: Set bobot variabel yang tidak aktif menjadi 0 agar tidak mengganggu total
            KpiVariable::where('division_id', $divisionId)
                ->where('is_active', false)
                ->update(['weight' => 0]);
        }

        return redirect()->back()->with('success', 'Bobot variabel aktif berhasil dibagi rata!');
    }

    public function destroy($id)
    {
        // Perubahan: Bukan menghapus baris (delete), tapi mengubah status is_active (Hide)
        $variable = KpiVariable::findOrFail($id);
        $variable->update([
            'is_active' => false,
            'weight' => 0 // Set bobot ke 0 agar total bobot divisi tetap valid
        ]);

        return redirect()->back()->with('success', 'Variabel berhasil disembunyikan dan dinonaktifkan.');
    }
}
