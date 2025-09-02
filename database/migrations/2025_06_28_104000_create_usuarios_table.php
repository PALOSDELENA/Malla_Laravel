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
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('t_doc');
            $table->string('num_doc')->primary();
            $table->string('usu_nombre', 45);
            $table->string('usu_apellido', 45);
            $table->string('usu_celular', 10);
            $table->string('email', 45);
            $table->string('usu_comentario', 200)->nullable();
            $table->unsignedBigInteger('usu_cargo');
            $table->unsignedBigInteger('usu_punto');
            $table->enum('usu_estado', ['Activo', 'Inactivo']);

            // Índices y claves foráneas
            $table->foreign('t_doc')->references('id')->on('tipo_documento')->onDelete('restrict');
            $table->foreign('usu_cargo')->references('id')->on('cargos')->onDelete('restrict');
            $table->foreign('usu_punto')->references('id')->on('puntos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
