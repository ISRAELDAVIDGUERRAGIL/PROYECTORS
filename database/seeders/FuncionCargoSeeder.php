<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\FuncionCargo;
use Illuminate\Database\Seeder;

class FuncionCargoSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = Cargo::all();

        foreach ($cargos as $cargo) {
            FuncionCargo::factory()->count(5)->create([
                'id_cargo' => $cargo->id_cargo,
            ]);
        }
    }
}
