<?php

use App\Models\Cargo;
use App\Models\FuncionCargo;
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

test('crear funcion de cargo exitosamente', function () {
    $cargo = Cargo::factory()->create();

    $data = [
        'id_cargo' => $cargo->id_cargo,
        'descripcion_funcion' => 'Supervisar el equipo de trabajo',
        'estado' => 'activo',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/funciones-cargo', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.descripcion_funcion', 'Supervisar el equipo de trabajo');

    $this->assertDatabaseHas('funciones_cargo', [
        'descripcion_funcion' => 'Supervisar el equipo de trabajo',
    ]);
});

test('listar funciones de cargo', function () {
    FuncionCargo::factory()->count(3)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson('/api/funciones-cargo');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('mostrar funcion de cargo existente', function () {
    $funcion = FuncionCargo::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/funciones-cargo/{$funcion->id_funcion}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id_funcion', $funcion->id_funcion);
});

test('actualizar funcion de cargo exitosamente', function () {
    $funcion = FuncionCargo::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/funciones-cargo/{$funcion->id_funcion}", [
            'descripcion_funcion' => 'Funcion actualizada',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.descripcion_funcion', 'Funcion actualizada');
});

test('eliminar funcion de cargo exitosamente', function () {
    $funcion = FuncionCargo::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/funciones-cargo/{$funcion->id_funcion}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('funciones_cargo', [
        'id_funcion' => $funcion->id_funcion,
    ]);
});
