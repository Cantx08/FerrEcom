<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Homologar datos históricos en tabla `ventas`
        DB::table('ventas')->where('estado', 'pendiente_pago')->update(['estado' => 'cancelado']);
        DB::table('ventas')->where('estado', 'en_preparacion')->update(['estado' => 'pagado']);
        DB::table('ventas')->where('estado', 'en_transito')->update(['estado' => 'enviado']);

        // 2. Homologar historial en tabla `venta_estado_historial`
        // estado_anterior
        DB::table('venta_estado_historial')->where('estado_anterior', 'pendiente_pago')->update(['estado_anterior' => 'cancelado']);
        DB::table('venta_estado_historial')->where('estado_anterior', 'en_preparacion')->update(['estado_anterior' => 'pagado']);
        DB::table('venta_estado_historial')->where('estado_anterior', 'en_transito')->update(['estado_anterior' => 'enviado']);
        
        // estado_nuevo
        DB::table('venta_estado_historial')->where('estado_nuevo', 'pendiente_pago')->update(['estado_nuevo' => 'cancelado']);
        DB::table('venta_estado_historial')->where('estado_nuevo', 'en_preparacion')->update(['estado_nuevo' => 'pagado']);
        DB::table('venta_estado_historial')->where('estado_nuevo', 'en_transito')->update(['estado_nuevo' => 'enviado']);

        // 3. Modificar la definición de la columna enum en ventas
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado ENUM('pagado', 'enviado', 'listo_para_retirar', 'entregado', 'cancelado') NOT NULL DEFAULT 'entregado'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Al revertir volvemos a la definición anterior.
        // No es posible revertir los datos porque ya se mezclaron con pagado/cancelado.
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado ENUM('completado', 'pendiente_pago', 'pagado', 'en_preparacion', 'en_transito', 'listo_para_retirar', 'entregado', 'cancelado') NOT NULL DEFAULT 'entregado'");
    }
};
