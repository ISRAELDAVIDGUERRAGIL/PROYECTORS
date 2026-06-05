<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpleadoApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // la creacion del usuario y del token de sactum
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->token = $response->json('token');
    }

    // listar empleados

    public function test_listar_empleados(): void
    {
        Empleado::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/empleados');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id_empleado', 'nombres', 'apellidos', 'salario', 'estado']
                ],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    //crear empleado 

    public function test_crear_empleado_exitosamente(): void
    {
        $data = [
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'fecha_nacimiento' => '1990-05-15',
            'fecha_ingreso' => '2024-01-10',
            'salario' => 2500000.50,
            'estado' => 'activo',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/empleados', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nombres', 'Juan')
            ->assertJsonPath('data.apellidos', 'Pérez')
            ->assertJsonPath('data.salario', 2500000.50);

        $this->assertDatabaseHas('empleados', [
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
        ]);
    }

    public function test_crear_empleado_falla_por_validacion(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/empleados', [
                'nombres' => '', // vacío → debe fallar
                'salario' => -100, // negativo → debe fallar
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombres', 'apellidos', 'fecha_nacimiento', 'fecha_ingreso', 'salario', 'estado']);
    }

    // test mostrar empleados

    public function test_mostrar_empleado_existente(): void
    {
        $empleado = Empleado::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/empleados/{$empleado->id_empleado}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id_empleado', $empleado->id_empleado);
    }

    public function test_mostrar_empleado_no_existente(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/empleados/99999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Empleado no encontrado');
    }

    //test actualizar empleado
    public function test_actualizar_empleado_exitosamente(): void
    {
        $empleado = Empleado::factory()->create([
            'nombres' => 'Carlos',
            'salario' => 2000000,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/empleados/{$empleado->id_empleado}", [
                'nombres' => 'Carlos Andrés',
                'apellidos' => $empleado->apellidos,
                'fecha_nacimiento' => $empleado->fecha_nacimiento->format('Y-m-d'),
                'fecha_ingreso' => $empleado->fecha_ingreso->format('Y-m-d'),
                'salario' => 3000000,
                'estado' => $empleado->estado,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nombres', 'Carlos Andrés')
            ->assertJsonPath('data.salario', 3000000);

        $this->assertDatabaseHas('empleados', [
            'id_empleado' => $empleado->id_empleado,
            'nombres' => 'Carlos Andrés',
            'salario' => 3000000,
        ]);
    }

    //test elminar empleado

    public function test_eliminar_empleado_exitosamente(): void
    {
        $empleado = Empleado::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/empleados/{$empleado->id_empleado}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Empleado eliminado exitosamente');

        $this->assertDatabaseMissing('empleados', [
            'id_empleado' => $empleado->id_empleado,
        ]);
    }

    //test por si no tienen el token que no los deje acceder
    public function test_no_puede_acceder_sin_token(): void
    {
        $response = $this->getJson('/api/empleados');

        $response->assertStatus(401);
    }
}