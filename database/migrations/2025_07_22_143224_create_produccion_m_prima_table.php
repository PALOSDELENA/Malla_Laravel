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
        Schema::create('produccion_m_prima', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produccion_id');
            $table->unsignedBigInteger('m_prima_id');
            $table->integer('cantidad')->default(0);

            // Relaciones
            $table->foreign('produccion_id')->references('id')->on('producciones')->onDelete('cascade');
            $table->foreign('m_prima_id')->references('id')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produccion_m_prima');
    }
};
