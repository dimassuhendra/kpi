<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Divisi;
use App\Models\User;
use App\Models\VariabelKpi;
use Illuminate\Support\Facades\Hash;

class KpiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seeder Divisi
        $tac = Divisi::create(['nama_divisi' => 'TAC']);

        // 2. Seeder User
        // Manager
        User::create([
            'nama_lengkap' => 'Jay Manager',
            'email' => 'jay@mybolo.com',
            'username' => 'jay_manager',
            'password' => Hash::make('12345678'),
            'role' => 'manager',
            'divisi_id' => $tac->id,
        ]);

        // Staff
        User::create([
            'nama_lengkap' => 'Staff Satu',
            'email' => 'staff-1@mybolo.com',
            'username' => 'staff1',
            'password' => Hash::make('12345678'),
            'role' => 'staff',
            'divisi_id' => $tac->id,
        ]);

        // 3. Seeder Variabel Penilaian Divisi TAC

        // Kasus: Jumlah Case (Input string/deskripsi, sistem hitung count)
        VariabelKpi::create([
            'divisi_id' => $tac->id,
            'nama_variabel' => 'Jumlah Case Harian',
            'input_type' => 'string',
            'bobot' => 10, // Misal per case dapat 10 poin
        ]);

        // Kasus: Durasi Response (Number/Menit)
        VariabelKpi::create([
            'divisi_id' => $tac->id,
            'nama_variabel' => 'Durasi Response (Ambang Batas 15 Menit)',
            'input_type' => 'number',
            'bobot' => 20,
        ]);

        // Kasus: Penemuan Mandiri (Boolean)
        VariabelKpi::create([
            'divisi_id' => $tac->id,
            'nama_variabel' => 'Case Ditemukan Sendiri',
            'input_type' => 'boolean',
            'bobot' => 5,
        ]);

        // Kasus: Penyelesaian Mandiri (Nilai Tambah/Bonus)
        VariabelKpi::create([
            'divisi_id' => $tac->id,
            'nama_variabel' => 'Penyelesaian Mandiri (Bonus)',
            'input_type' => 'boolean',
            'bobot' => 15,
        ]);
    }
}
