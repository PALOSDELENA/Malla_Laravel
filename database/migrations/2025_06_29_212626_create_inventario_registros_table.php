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
        Schema::create('inventario_registros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('punto_id');
            $table->date('fecha');
            $table->decimal('cantidad', 10, 2);

            // Llaves foráneas
            $table->foreign('produto_id')->references('id')->on('inventario_productos')->onDelete('restrict');
            $table->foreign('punto_id')->references('id')->on('puntos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_registros');
    }
};
