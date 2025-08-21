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
        Schema::create('trazabilidadProductos', function (Blueprint $table) {
            $table->id();
            $table->date('traFechaMovimiento');
            $table->string('traTipoMovimiento'); // 'entrada' o 'salida'
            $table->unsignedBigInteger('traIdProducto'); // ID del producto relacionado
            $table->decimal('traCantidad',10,2); // Cantidad del producto
            $table->string('traLoteSerie')->nullable(); // Lote o serie del producto
            $table->string('traProveedor')->nullable(); // Ubicación del producto
            $table->string('traDestino', 45)->nullable();
            $table->string('traResponsable', 15);
            $table->string('traColor')->nullable();
            $table->string('traTextura')->nullable();
            $table->string('traOlor')->nullable();
            $table->string('traObservaciones')->nullable(); // Descripción opcional del movimiento
            $table->unsignedBigInteger('orden_produccion_id')->nullable(); // ID de la orden de producción asociada
            $table->unsignedBigInteger('traPunto')->nullable();

            $table->foreign('traIdProducto')
                ->references('id')->on('productos')
                ->onDelete('cascade'); // Elimina trazabilidad si se elimina el producto
            $table->foreign('orden_produccion_id')
                ->references('id')->on('orden_produccion')
                ->onDelete('cascade'); // Elimina trazabilidad si se elimina el producto
            $table->foreign('traResponsable')
                ->references('num_doc')->on('users') // Asumiendo que tienes una tabla de usuarios
                ->onDelete('cascade'); // Elimina trazabilidad si se elimina el usuario responsable
            $table->foreign('traPunto')
                ->references('id')->on('puntos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trazabilidad_productos');
    }
};
