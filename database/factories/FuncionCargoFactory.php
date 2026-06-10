<?php

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\FuncionCargo;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuncionCargoFactory extends Factory
{
    protected $model = FuncionCargo::class;

    public function definition(): array
    {
        return [
            'id_cargo' => Cargo::factory(),
            'descripcion_funcion' => fake()->sentence(8),
            'estado' => 'activo',
        ];
    }
}
