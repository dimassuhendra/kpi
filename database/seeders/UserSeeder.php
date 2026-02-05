<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tac = Division::firstOrCreate(
            ['name' => 'TAC'],
            ['weighting_mode' => 'equal']
        );

        User::create([
            'name' => 'Manager IT TAC',
            'email' => 'jay@mybolo.com',
            'password' => Hash::make('12345678'),
            'role' => 'manager',
            'division_id' => $tac->id,
        ]);

        User::create([
            'name' => 'Staff IT TAC 1',
            'email' => 'staff@mybolo.com',
            'password' => Hash::make('12345678'),
            'role' => 'staff',
            'division_id' => $tac->id,
        ]);

        User::create([
            'name' => 'Staff IT TAC 2',
            'email' => 'staff2@mybolo.com',
            'password' => Hash::make('12345678'),
            'role' => 'staff',
            'division_id' => $tac->id,
        ]);
    }
}