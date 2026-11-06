<?php

namespace App\Events;

use App\Models\PedidoCompra;
use App\Models\User;
use App\Enums\PedidoEstado;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PedidoEstadoCambiado
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly PedidoCompra $pedido,
        public readonly PedidoEstado $estadoAnterior,
        public readonly PedidoEstado $estadoNuevo,
        public readonly ?User $actor,
        public readonly ?string $notas = null,
    ) {}
}
