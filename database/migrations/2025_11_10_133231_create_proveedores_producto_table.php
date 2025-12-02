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
        Schema::dropIfExists('proveedores_producto');
        Schema::create('proveedores_producto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_proveedor');
            $table->enum('calidad_producto', ['excelente', 'aceptable', 'bueno', 'malo']);
            $table->enum('tiempo_entrega', ['excelente', 'aceptable', 'bueno', 'malo']);
            $table->enum('presentacion_personal', ['excelente', 'aceptable', 'bueno', 'malo']);
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores_producto');
    }
};
