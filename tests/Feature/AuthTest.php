<?php

use App\Models\User;

test('registro exitoso devuelve usuario y token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Nuevo Usuario',
        'email' => 'nuevo@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['usuario', 'token', 'type'])
        ->assertJsonPath('type', 'Bearer');

    $this->assertDatabaseHas('users', [
        'email' => 'nuevo@test.com',
        'name' => 'Nuevo Usuario',
    ]);
});

test('registro con email duplicado devuelve 422', function () {
    User::factory()->create(['email' => 'existe@test.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Otro Usuario',
        'email' => 'existe@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registro con datos invalidos devuelve 422', function () {
    $response = $this->postJson('/api/register', [
        'name' => '',
        'email' => 'no-es-email',
        'password' => '123',
        'password_confirmation' => '456',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

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
