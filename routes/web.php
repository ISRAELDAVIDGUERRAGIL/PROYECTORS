<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/cargos', 'cargos.index')->name('cargos');
    Route::view('/empleados', 'empleados.index')->name('empleados');
    Route::view('/funciones-cargo', 'funciones.index')->name('funciones');
});

Route::get('/error/{code}', function (string $code) {
    if (!view()->exists("errors.{$code}")) {
        $code = '500';
    }
    return response()->view("errors.{$code}", [], (int) $code);
})->name('error');

require __DIR__.'/auth.php';
