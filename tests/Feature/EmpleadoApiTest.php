<?php

use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\User;

beforeEach(function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $this->token = $response->json('token');
});

test('listar empleados', function () {
    Cargo::factory()->count(2)->has(Empleado::factory()->count(2))->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson('/api/empleados');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id_empleado', 'nombres', 'apellidos', 'salario', 'estado', 'cargo']
            ],
        ]);
});

test('crear empleado exitosamente', function () {
    $cargo = Cargo::factory()->create();

    $data = [
        'id_cargo' => $cargo->id_cargo,
        'nombres' => 'Juan',
        'apellidos' => 'Perez',
        'fecha_nacimiento' => '1990-05-15',
        'fecha_ingreso' => '2024-01-10',
        'salario' => 2500000.50,
        'estado' => 'activo',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/empleados', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.nombres', 'Juan')
        ->assertJsonPath('data.cargo.id_cargo', $cargo->id_cargo);

    $this->assertDatabaseHas('empleados', [
        'nombres' => 'Juan',
        'apellidos' => 'Perez',
    ]);
});

test('crear empleado falla por validacion', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/empleados', [
            'nombres' => '',
            'salario' => -100,
        ]);

    $response->assertStatus(422);
});

test('mostrar empleado existente', function () {
    $empleado = Empleado::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/empleados/{$empleado->id_empleado}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id_empleado', $empleado->id_empleado);
});

test('mostrar empleado no existente', function () {
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson('/api/empleados/99999');

    $response->assertStatus(404);
});

test('actualizar empleado exitosamente', function () {
    $empleado = Empleado::factory()->create([
        'nombres' => 'Carlos',
        'salario' => 2000000,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/empleados/{$empleado->id_empleado}", [
            'nombres' => 'Carlos Andres',
            'salario' => 3000000,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.nombres', 'Carlos Andres')
        ->assertJsonPath('data.salario', 3000000);
});

test('eliminar empleado exitosamente', function () {
    $empleado = Empleado::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/empleados/{$empleado->id_empleado}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('empleados', [
        'id_empleado' => $empleado->id_empleado,
    ]);
});

test('no puede acceder sin token', function () {
    $response = $this->getJson('/api/empleados');

    $response->assertStatus(401);
});
