<?php

namespace App\Listeners;

use App\Events\VentaEstadoCambiada;
use App\Models\VentaEstadoHistorial;
use App\Enums\VentaEstado;
use App\Notifications\VentaEstadoCambiadaNotification;

class NotificarCambioVenta
{
    /**
     * Guarda historial y notifica al cliente (canal database) cuando
     * el estado de su venta online cambia a uno relevante.
     */
    public function handle(VentaEstadoCambiada $event): void
    {
        // 1. Registrar en historial
        VentaEstadoHistorial::create([
            'venta_id'        => $event->venta->id,
            'estado_anterior' => $event->estadoAnterior?->value,
            'estado_nuevo'    => $event->estadoNuevo->value,
            'user_id'         => $event->actor?->id,
            'notas'           => $event->notas,
            'created_at'      => now(),
        ]);

        // 2. Notificar al cliente solo si tiene cuenta y el estado requiere notificación
        $cliente = $event->venta->cliente;

        if (
            $cliente &&
            $event->venta->esOnline() &&
            in_array($event->estadoNuevo, VentaEstado::estadosConNotificacion(), strict: true)
        ) {
            $cliente->notify(new VentaEstadoCambiadaNotification($event->venta, $event->estadoNuevo));
        }
    }
}
