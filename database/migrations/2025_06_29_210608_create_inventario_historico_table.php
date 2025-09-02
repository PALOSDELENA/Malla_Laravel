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
        Schema::create('inventario_historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('punto_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('encargado', 45);
            $table->json('datos');
            $table->timestamp('creado_en')->useCurrent();

            // Llave foránea
            $table->foreign('punto_id')->references('id')->on('puntos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_historico');
    }
};
