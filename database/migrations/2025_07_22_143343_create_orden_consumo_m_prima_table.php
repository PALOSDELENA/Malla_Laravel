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
        Schema::create('orden_consumo_m_prima', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_produccion_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('cantidad');
            $table->timestamp('fecha_consumo');

            // Índices y claves foráneas
            $table->foreign('orden_produccion_id')->references('id')->on('orden_produccion')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_consumo_m_prima');
    }
};
