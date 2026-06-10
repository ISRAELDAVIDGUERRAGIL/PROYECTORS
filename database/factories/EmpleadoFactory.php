<?php

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpleadoFactory extends Factory
{
    protected $model = Empleado::class;

    public function definition(): array
    {
        return [
            'id_cargo' => Cargo::factory(),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'fecha_nacimiento' => fake()->date('Y-m-d', '-18 years'),
            'fecha_ingreso' => fake()->date('Y-m-d', 'now'),
            'salario' => fake()->randomFloat(2, 1000000, 5000000),
            'estado' => fake()->randomElement(['activo', 'inactivo']),
        ];
    }
}
