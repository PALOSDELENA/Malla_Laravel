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
        Schema::create('productos_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->decimal('stock_actual', 10, 2)->default(0);

            // Relación
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_stock');
    }
};
