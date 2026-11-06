<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La migración 2026_09_03_000003_actualizar_ventas_flujo_completo renombró
 * la columna `tipo` (enum('fisico','online')) a `canal`, pero nunca redefinió
 * el propio enum. App\Enums\CanalVenta usa los valores 'pos'/'online', así
 * que toda inserción real (VentaService::procesarVentaPOS) fallaba con
 * "Data truncated for column 'canal'". Esta migración corrige el enum.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Por seguridad, mapear cualquier fila legada antes de angostar el enum
        DB::table('ventas')->where('canal', 'fisico')->update(['canal' => 'pos']);

        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('canal', ['pos', 'online'])->default('pos')->change();
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('canal', ['fisico', 'online'])->default('fisico')->change();
        });

        DB::table('ventas')->where('canal', 'pos')->update(['canal' => 'fisico']);
    }
};
