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
        Schema::create('orden_compra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('punto_id');
            $table->string('responsable', 100);
            $table->string('email');
            $table->date('fecha_entrega_1');
            $table->date('fecha_entrega_2');
            $table->string('estado');
            $table->text('comentario_admin');

            $table->timestamps();

            $table->foreign('punto_id')->references('id')->on('puntos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_compra');
    }
};
