<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cargo')->nullable()->after('id_empleado');

            $table->foreign('id_cargo')
                  ->references('id_cargo')
                  ->on('cargos')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['id_cargo']);
            $table->dropColumn('id_cargo');
        });
    }
};
