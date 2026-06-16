<?php

use App\Models\User;

test('login correcto devuelve token', function () {
    User::factory()->create([
        'email' => 'admin@test.com',
        'password' => bcrypt('12345678'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'admin@test.com',
        'password' => '12345678',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['usuario', 'token', 'type'])
        ->assertJsonPath('type', 'Bearer');
});

test('login incorrecto devuelve 422', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'no@existe.com',
        'password' => 'wrong',
    ]);

    $response->assertStatus(422);
});

test('logout cierra sesion correctamente', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $login = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $token = $login->json('token');

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Sesión cerrada exitosamente.');
});
