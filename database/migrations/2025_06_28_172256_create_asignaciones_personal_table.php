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
        Schema::create('asignaciones_personal', function (Blueprint $table) {
            $table->id();
            $table->string('usu_num_doc', 15);
            $table->string('dia_origen',20);
            $table->unsignedBigInteger('punto_origen_id');
            $table->timestamp('fecha_movimiento');
            $table->enum('estado_asignacion', ['Activo', 'Inactivo']);
            $table->enum('tipo_movimiento', ['Traslado','Nueva Asignación']);
            
            // Índices y claves foráneas
            $table->foreign('usu_num_doc')->references('num_doc')->on('users')->onDelete('restrict');
            $table->foreign('punto_origen_id')->references('id')->on('puntos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_personal');
    }
};
