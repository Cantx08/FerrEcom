<?php

namespace App\Listeners;

use App\Events\PedidoEstadoCambiado;
use App\Models\User;

use App\Notifications\PedidoEstadoActualizado;
use Illuminate\Support\Facades\Notification;

class NotificarCambioPedido
{
    /**
     * Guarda historial y notifica (canal database) al bodeguero creador
     * y a todos los Administradores ante cada cambio de estado.
     */
    public function handle(PedidoEstadoCambiado $event): void
    {


        // 2. Determinar destinatarios: bodeguero creador + todos los Administradores
        $destinatarios = User::whereHas('roles', fn($q) =>
            $q->whereIn('name', ['Administrador', 'SuperAdmin'])
        )->get();

        // Agregar al bodeguero si no está ya en la lista
        $bodeguero = $event->pedido->bodeguero;
        if ($bodeguero && !$destinatarios->contains('id', $bodeguero->id)) {
            $destinatarios->push($bodeguero);
        }

        // Agregar al proveedor
        $proveedorUser = $event->pedido->proveedor?->user;
        if ($proveedorUser && !$destinatarios->contains('id', $proveedorUser->id)) {
            $destinatarios->push($proveedorUser);
        }

        // 3. Construir mensaje contextual
        $mensaje = sprintf(
            'El pedido #%d cambió de "%s" a "%s".',
            $event->pedido->id,
            $event->estadoAnterior->etiqueta(),
            $event->estadoNuevo->etiqueta()
        );

        // 4. Enviar notificación solo por canal database
        Notification::send(
            $destinatarios,
            new PedidoEstadoActualizado($event->pedido, $mensaje)
        );
    }
}
