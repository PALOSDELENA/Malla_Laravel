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
        Schema::create('novedades_paloteo', function (Blueprint $table) {
            $table->id();
            $table->string('id_usuario', 15);
            $table->unsignedBigInteger('id_punto');
            $table->unsignedBigInteger('id_producto');
            $table->text('comentario_operario');
            $table->text('comentario_admin')->nullable();
            $table->date('fecha_novedad');
            $table->json('imagenes')->nullable(); // almacena ["url1.jpg", "url2.jpg", ...]
            $table->enum('estado', ['pendiente', 'revisado', 'cerrado'])->default('pendiente');
            $table->timestamps();

            // Relaciones
            $table->foreign('id_usuario')->references('num_doc')->on('users')->onDelete('cascade');
            $table->foreign('id_punto')->references('id')->on('puntos')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novedades_paloteo');
    }
};
