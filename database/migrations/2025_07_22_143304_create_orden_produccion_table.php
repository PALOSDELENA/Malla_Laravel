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
        Schema::create('orden_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('responsable', 15);
            $table->unsignedBigInteger('produccion_id');
            $table->integer('cantidad');
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->enum('estado', ['Pendiente', 'En Proceso', 'Completada', 'Cancelada']);
            $table->text('novedadProduccion')->nullable();

            $table->foreign('produccion_id')->references('id')->on('producciones')->onDelete('cascade');
            $table->foreign('responsable')->references('num_doc')->on('usuarios')->onDelete('cascade'); // Assuming 'users' table exists for responsible person
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_produccion');
    }
};
