<?php

namespace App\Events;

use App\Models\Venta;
use App\Models\User;
use App\Enums\VentaEstado;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VentaEstadoCambiada
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Venta $venta,
        public readonly ?VentaEstado $estadoAnterior,
        public readonly VentaEstado $estadoNuevo,
        public readonly ?User $actor,
        public readonly ?string $notas = null,
    ) {}
}
