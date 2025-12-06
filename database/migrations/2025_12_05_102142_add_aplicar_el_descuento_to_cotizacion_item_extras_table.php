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
        Schema::table('cotizacion_item_extras', function (Blueprint $table) {
            $table->boolean('aplicar_el_descuento')->default(false)->after('suma_al_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizacion_item_extras', function (Blueprint $table) {
            $table->dropColumn('aplicar_el_descuento');
        });
    }
};
