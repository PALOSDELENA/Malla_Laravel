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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('proNombre', 45);
            $table->string('proUnidadMedida', 45);
            $table->string('proTipo', 45);
            $table->string('proListaIngredientes')->nullable();
            $table->string('proCondicionesConservacion', 255)->nullable();
            $table->string('proFabricante', 255)->nullable();
            $table->unsignedBigInteger('id_proveedor')->nullable();

            $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
