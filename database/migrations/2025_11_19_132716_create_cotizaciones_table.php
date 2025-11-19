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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');

            // Datos del evento
            $table->string('motivo');
            $table->string('sede');
            $table->date('fecha');
            $table->time('hora');
            $table->integer('numero_personas');

            // Totales calculados
            $table->double('subtotal', 15, 2)->default(0);
            $table->double('ipoconsumo', 15, 2)->default(0);

            // Descuentos
            $table->double('descuento_pct', 5, 2)->default(0);  // %
            $table->double('descuento_monto', 15, 2)->default(0);
            $table->double('subtotal_menos_descuento', 15, 2)->default(0);

            // Impuestos
            $table->double('reteica', 15, 2)->default(0);
            $table->double('retefuente', 15, 2)->default(0);

            // Propina
            $table->double('propina', 15, 2)->default(0);

            // Totales finales
            $table->double('total_final', 15, 2)->default(0);
            $table->double('anticipo', 15, 2)->default(0);
            $table->double('saldo_pendiente', 15, 2)->default(0);
            
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
