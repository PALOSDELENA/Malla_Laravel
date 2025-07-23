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
        Schema::create('asignacion_turnos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('turnos_id');
            $table->string('usuarios_num_doc', 15);
            $table->string('tur_usu_dia', 45);
            $table->date('tur_usu_fecha');
            $table->string('nombre');
            $table->string('dia');
            $table->integer('order_column')->nullable();

            // Índices y claves foráneas
            $table->foreign('turnos_id')->references('id_turnos')->on('turnos')->onDelete('restrict');
            $table->foreign('usuarios_num_doc')->references('num_doc')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_turnos');
    }
};
