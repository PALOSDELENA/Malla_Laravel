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
        Schema::table('cotizaciones', function (Blueprint $table) {
            // Eliminar la foreign key primero
            if (Schema::hasColumn('cotizaciones', 'extra_id')) {
                $table->dropForeign('fk_cotizaciones_item_extra');
                $table->dropColumn('extra_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('extra_id')->nullable();
            $table->foreign('extra_id')->references('id')->on('item_extras')->onDelete('cascade');
        });
    }
};
