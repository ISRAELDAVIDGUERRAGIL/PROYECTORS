<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CargoController;
use App\Http\Controllers\Api\EmpleadoController;
use App\Http\Controllers\Api\FuncionCargoController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('cargos', CargoController::class)
        ->parameters(['cargos' => 'cargo']);

    Route::apiResource('empleados', EmpleadoController::class)
        ->parameters(['empleados' => 'empleado']);

    Route::apiResource('funciones-cargo', FuncionCargoController::class)
        ->parameters(['funciones-cargo' => 'funcionCargo']);
});
