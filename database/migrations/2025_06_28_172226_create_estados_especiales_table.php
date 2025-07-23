<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estados_especiales', function (Blueprint $table) {
            $table->id();
            $table->string('des_usuario_id', 15);
            $table->enum('tipo_estado', ['Vacaciones','Descanso']);
            $table->date('des_fecha_inicio');
            $table->date('des_fecha_fin');
            $table->string('des_observaciones',200);

            // Índices y claves foráneas
            $table->foreign('des_usuario_id')->references('num_doc')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_especiales');
    }
};
