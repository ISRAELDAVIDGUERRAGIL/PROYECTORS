<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funciones_cargo', function (Blueprint $table) {
            $table->id('id_funcion');
            $table->unsignedBigInteger('id_cargo');
            $table->text('descripcion_funcion');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();

            $table->foreign('id_cargo')
                  ->references('id_cargo')
                  ->on('cargos')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funciones_cargo');
    }
};
