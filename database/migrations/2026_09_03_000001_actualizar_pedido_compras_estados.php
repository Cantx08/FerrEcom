<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Migrar datos existentes antes de cambiar el enum
        $mapeoEstados = [
            'pendiente'      => 'borrador',
            'revisado'       => 'en_aprobacion',
            'stock_incompleto' => 'en_aprobacion',
            'confirmado'     => 'emitido',
            'enviado'        => 'en_transito',
            'completado'     => 'recibido_completado',
            'cancelado'      => 'cancelado',
        ];

        foreach ($mapeoEstados as $estadoViejo => $estadoNuevo) {
            DB::table('pedido_compras')
                ->where('estado', $estadoViejo)
                ->update(['estado' => $estadoNuevo]);
        }

        // 2. Modificar la tabla con los nuevos campos y enum
        Schema::table('pedido_compras', function (Blueprint $table) {
            // Cambiar el enum de estado a los 10 nuevos valores
            $table->enum('estado', [
                'borrador',
                'en_aprobacion',
                'emitido',
                'aceptado',
                'en_preparacion',
                'en_transito',
                'recibido_parcialmente',
                'recibido_completado',
                'pagado',
                'cancelado',
            ])->default('borrador')->change();

            // Método de pago — solo efectivo para pedidos a proveedor
            $table->enum('metodo_pago', ['efectivo'])->default('efectivo')->after('total');

            // Quién aprobó el pedido (el Dueño/Administrador)
            $table->foreignId('aprobado_por')
                ->nullable()
                ->after('bodeguero_id')
                ->constrained('users')
                ->nullOnDelete();

            // Quién canceló y por qué
            $table->foreignId('cancelado_por')
                ->nullable()
                ->after('aprobado_por')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('motivo_cancelacion')->nullable()->after('cancelado_por');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_compras', function (Blueprint $table) {
            $table->dropForeign(['aprobado_por']);
            $table->dropForeign(['cancelado_por']);
            $table->dropColumn(['metodo_pago', 'aprobado_por', 'cancelado_por', 'motivo_cancelacion']);

            $table->enum('estado', [
                'pendiente', 'revisado', 'stock_incompleto', 'confirmado', 'enviado', 'completado', 'cancelado',
            ])->default('pendiente')->change();
        });
    }
};
