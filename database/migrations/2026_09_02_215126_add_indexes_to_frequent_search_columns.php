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
            $table->index('nombre');
            $table->index('estado');
        });

        Schema::table('producto_proveedors', function (Blueprint $table) {
            $table->index('nombre');
        });

        Schema::table('proveedors', function (Blueprint $table) {
            $table->index('nombre_compania');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->index('estado');
            $table->index('codigo_retiro');
        });

        Schema::table('pedido_compras', function (Blueprint $table) {
            $table->index('estado');
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->index('ruc_cedula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['nombre']);
            $table->dropIndex(['estado']);
        });

        Schema::table('producto_proveedors', function (Blueprint $table) {
            $table->dropIndex(['nombre']);
        });

        Schema::table('proveedors', function (Blueprint $table) {
            $table->dropIndex(['nombre_compania']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['codigo_retiro']);
        });

        Schema::table('pedido_compras', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropIndex(['ruc_cedula']);
        });
    }
};
