<?php

use App\Models\Cargo;
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

test('crear cargo exitosamente', function () {
    $data = [
        'nombre_cargo' => 'Gerente de Ventas',
        'descripcion' => 'Responsable del equipo de ventas',
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->postJson('/api/cargos', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.nombre_cargo', 'Gerente de Ventas');

    $this->assertDatabaseHas('cargos', [
        'nombre_cargo' => 'Gerente de Ventas',
    ]);
});

test('listar cargos', function () {
    Cargo::factory()->count(5)->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson('/api/cargos');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('mostrar cargo existente', function () {
    $cargo = Cargo::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->getJson("/api/cargos/{$cargo->id_cargo}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id_cargo', $cargo->id_cargo);
});

test('actualizar cargo exitosamente', function () {
    $cargo = Cargo::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->putJson("/api/cargos/{$cargo->id_cargo}", [
            'nombre_cargo' => 'Cargo Actualizado',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.nombre_cargo', 'Cargo Actualizado');

    $this->assertDatabaseHas('cargos', [
        'id_cargo' => $cargo->id_cargo,
        'nombre_cargo' => 'Cargo Actualizado',
    ]);
});

test('eliminar cargo exitosamente', function () {
    $cargo = Cargo::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
        ->deleteJson("/api/cargos/{$cargo->id_cargo}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('cargos', [
        'id_cargo' => $cargo->id_cargo,
    ]);
});
