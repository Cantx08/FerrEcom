<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Mapear datos existentes antes de modificar el enum
        $mapeoEstados = [
            'pendiente' => 'pagado',   // ventas POS existentes quedan en pagado
            'pagado'    => 'pagado',
            'entregado' => 'entregado',
        ];

        foreach ($mapeoEstados as $estadoViejo => $estadoNuevo) {
            DB::table('ventas')
                ->where('estado', $estadoViejo)
                ->update(['estado' => $estadoNuevo]);
        }

        // Mapear campo 'tipo' → 'canal'
        // fisico → pos, online → online
        DB::table('ventas')->where('tipo', 'fisico')->update(['tipo' => 'pos']);

        Schema::table('ventas', function (Blueprint $table) {
            // Actualizar enum de estado al flujo completo
            $table->enum('estado', [
                'completado',
                'pendiente_pago',
                'pagado',
                'en_preparacion',
                'en_transito',
                'listo_para_retirar',
                'entregado',
                'cancelado',
            ])->default('completado')->change();

            // Renombrar tipo → canal y actualizar valores
            $table->renameColumn('tipo', 'canal');

            // Método de entrega para ventas online
            $table->enum('metodo_entrega', ['retiro_en_caja', 'envio_domicilio'])
                ->nullable()
                ->after('metodo_pago');

            // tracking_code unifica codigo_retiro (se mantiene codigo_retiro por compatibilidad)
            // pero se agrega tracking_code como campo canónico con índice único
            $table->string('tracking_code', 30)->nullable()->unique()->after('metodo_entrega');
        });

        // Copiar codigo_retiro a tracking_code para las ventas online existentes
        DB::statement('UPDATE ventas SET tracking_code = codigo_retiro WHERE codigo_retiro IS NOT NULL AND canal = \'online\'');
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->renameColumn('canal', 'tipo');
            $table->dropColumn(['metodo_entrega', 'tracking_code']);

            $table->enum('estado', ['pendiente', 'pagado', 'entregado'])
                ->default('pendiente')->change();
        });
    }
};
