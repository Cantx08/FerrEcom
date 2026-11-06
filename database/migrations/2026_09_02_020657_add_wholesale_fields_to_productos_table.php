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
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('cantidad_por_mayor')->nullable()->after('stock_actual');
            $table->decimal('descuento_por_mayor', 5, 2)->nullable()->after('cantidad_por_mayor')->comment('Porcentaje de descuento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['cantidad_por_mayor', 'descuento_por_mayor']);
        });
    }
};
