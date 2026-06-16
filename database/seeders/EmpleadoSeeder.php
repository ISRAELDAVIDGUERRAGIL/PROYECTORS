<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Empleado;
use Illuminate\Database\Seeder;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $cargosIds = Cargo::pluck('id_cargo')->toArray();

        Empleado::factory()->count(30)->create([
            'id_cargo' => fn () => $cargosIds[array_rand($cargosIds)],
        ]);
    }
}
