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
        Schema::create('orden_producto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('inventario');
            $table->integer('sugerido');
            $table->integer('pedido_1');
            $table->integer('pedido_2');
            $table->integer('total_pedido');
            $table->double('precio_total');
            $table->text('observaciones');
            $table->double('cantidad_bodega');
            $table->double('stock_minimo');
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('orden_compra')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_producto');
    }
};
