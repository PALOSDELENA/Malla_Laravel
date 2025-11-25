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
        Schema::create('cotizacion_item_extras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotizacion_id');
            $table->unsignedBigInteger('item_extra_id')->nullable(); // null si es personalizado
            $table->string('nombre'); // nombre del concepto (del catálogo o personalizado)
            $table->decimal('valor', 10, 2);
            $table->boolean('suma_al_total')->default(true);
            $table->timestamps();
            
            $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->onDelete('cascade');
            $table->foreign('item_extra_id')->references('id')->on('item_extras')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_item_extras');
    }
};
