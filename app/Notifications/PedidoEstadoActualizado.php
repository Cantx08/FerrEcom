<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PedidoCompra;

class PedidoEstadoActualizado extends Notification
{
    use Queueable;

    public function __construct(
        public readonly PedidoCompra $pedido,
        public readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = method_exists($notifiable, 'hasRole') && $notifiable->hasRole('Proveedor')
            ? route('proveedor.pedidos.show', $this->pedido->id)
            : route('pedidos.show', $this->pedido->id);

        return [
            'pedido_id'    => $this->pedido->id,
            'estado'       => $this->pedido->estado->value,
            'estado_label' => $this->pedido->estado->etiqueta(),
            'message'      => $this->message,
            'url'          => $url,
        ];
    }
}
