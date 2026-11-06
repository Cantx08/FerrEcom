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
        Schema::table('producto_proveedors', function (Blueprint $table) {
            $table->string('unidad', 30)->nullable()->after('costo_unitario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto_proveedors', function (Blueprint $table) {
            $table->dropColumn('unidad');
        });
    }
};
