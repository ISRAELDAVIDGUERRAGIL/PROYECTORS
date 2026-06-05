<?php

namespace Database\Factories;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empleado>
 */
class EmpleadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Empleado::class;
    public function definition(): array
    {
        return [
            'nombres' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '-18 years'),
            'fecha_ingreso' => $this->faker->date('Y-m-d', 'now'),
            'salario' => $this->faker->randomFloat(2, 1000000, 5000000),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
