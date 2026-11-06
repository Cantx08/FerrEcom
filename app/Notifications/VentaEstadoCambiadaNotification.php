<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Venta;
use App\Enums\VentaEstado;

class VentaEstadoCambiadaNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Venta $venta,
        public readonly VentaEstado $nuevoEstado,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $mensaje = match($this->nuevoEstado) {
            VentaEstado::Pagado => sprintf(
                '¡Tu compra #%d ha sido confirmada! Tu código de rastreo es: %s',
                $this->venta->id,
                $this->venta->tracking_code ?? $this->venta->codigo_retiro
            ),
            VentaEstado::Enviado => sprintf(
                'Tu pedido #%d está en camino. Código de rastreo: %s',
                $this->venta->id,
                $this->venta->tracking_code ?? $this->venta->codigo_retiro
            ),
            VentaEstado::ListoParaRetirar => sprintf(
                'Tu pedido #%d está listo para retirar en tienda. Presenta el código: %s',
                $this->venta->id,
                $this->venta->tracking_code ?? $this->venta->codigo_retiro
            ),
            VentaEstado::Entregado => sprintf(
                'Tu pedido #%d ha sido entregado. ¡Gracias por tu compra!',
                $this->venta->id
            ),
            default => sprintf(
                'El estado de tu pedido #%d ha cambiado a: %s',
                $this->venta->id,
                $this->nuevoEstado->etiqueta()
            ),
        };

        return [
            'venta_id'       => $this->venta->id,
            'estado'         => $this->nuevoEstado->value,
            'estado_label'   => $this->nuevoEstado->etiqueta(),
            'tracking_code'  => $this->venta->tracking_code ?? $this->venta->codigo_retiro,
            'message'        => $mensaje,
            'url'            => route('cliente.compras'),
        ];
    }
}
