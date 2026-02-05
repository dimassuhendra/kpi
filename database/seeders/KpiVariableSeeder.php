<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiVariable;
use App\Models\Division;

class KpiVariableSeeder extends Seeder
{
    public function run(): void
    {
        $tac = Division::where('name', 'TAC')->first();

        // Variabel 1: Count Case (Input via daftar tiket)
        KpiVariable::create([
            'division_id' => $tac->id,
            'variable_name' => 'Count Case',
            'weight' => 25,
            'input_type' => 'case_list',
        ]);

        // Variabel 2: Response Time (Input via daftar tiket)
        KpiVariable::create([
            'division_id' => $tac->id,
            'variable_name' => 'Response Time',
            'weight' => 25,
            'input_type' => 'case_list',
        ]);

        // Variabel 3: Problem Detection (Input via daftar tiket)
        KpiVariable::create([
            'division_id' => $tac->id,
            'variable_name' => 'Problem Detection',
            'weight' => 25,
            'input_type' => 'case_list',
        ]);

        // Variabel 4: Reporting (Input Dropdown - Perlu ACC Manager)
        KpiVariable::create([
            'division_id' => $tac->id,
            'variable_name' => 'Reporting',
            'weight' => 25,
            'input_type' => 'dropdown',
            'scoring_matrix' => [
                'tepat_waktu' => 100,
                'ada_revisi' => 70,
                'terlambat' => 50,
                'tidak_mengisi' => 0
            ],
        ]);
    }
}