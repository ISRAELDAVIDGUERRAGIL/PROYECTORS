<?php

use App\Http\Controllers\Api\EmpleadoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Las credenciales son incorrectas.'],
        ]);
    }

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken,
    ]);
});

Route::middleware('auth:sanctum')->apiResource('empleados', EmpleadoController::class);