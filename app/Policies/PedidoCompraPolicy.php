<?php

namespace App\Policies;

use App\Enums\PedidoEstado;
use App\Models\PedidoCompra;
use App\Models\User;

class PedidoCompraPolicy
{
    /**
     * Bodeguero y Administrador pueden crear pedidos.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Bodeguero', 'Administrador', 'SuperAdmin']);
    }

    /**
     * Todos los roles autorizados pueden ver un pedido.
     * El Proveedor solo puede ver sus propios pedidos (se filtra en el controlador).
     */
    public function view(User $user, PedidoCompra $pedido): bool
    {
        if ($user->hasAnyRole(['Administrador', 'SuperAdmin', 'Bodeguero'])) {
            return true;
        }

        // Proveedor solo ve sus pedidos
        if ($user->hasRole('Proveedor') && $user->proveedor) {
            return $pedido->proveedor_id === $user->proveedor->id;
        }

        return false;
    }

    /**
     * Bodeguero: solo puede cancelar sus propios pedidos.
     * Administrador: puede cancelar cualquiera.
     * Solo se puede cancelar mientras no se haya recibido mercadería ni pagado
     * (ver PedidoEstado::estadosCancelables()).
     */
    public function cancelar(User $user, PedidoCompra $pedido): bool
    {
        if (!$pedido->esCancelable()) {
            return false;
        }

        if ($user->hasAnyRole(['Administrador', 'SuperAdmin'])) {
            return true;
        }

        if ($user->hasRole('Bodeguero')) {
            return $pedido->bodeguero_id === $user->id;
        }

        return false;
    }

    /**
     * Solo el Administrador puede aprobar pedidos (pasar de en_aprobacion → emitido).
     */
    public function aprobar(User $user, PedidoCompra $pedido): bool
    {
        return $user->hasAnyRole(['Administrador', 'SuperAdmin'])
            && $pedido->estado === PedidoEstado::EnAprobacion;
    }

    /**
     * El Proveedor puede cambiar el estado a: aceptado, en_preparacion, en_transito.
     * El Bodeguero/Admin pueden marcar: recibido_parcialmente, recibido_completado, pagado.
     */
    public function cambiarEstado(User $user, PedidoCompra $pedido): bool
    {
        if ($user->hasAnyRole(['Administrador', 'SuperAdmin', 'Bodeguero'])) {
            return true;
        }

        if ($user->hasRole('Proveedor') && $user->proveedor) {
            return $pedido->proveedor_id === $user->proveedor->id;
        }

        return false;
    }

    /**
     * Solo el Administrador puede registrar el pago (y solo con efectivo, validado en el service).
     */
    public function pagar(User $user, PedidoCompra $pedido): bool
    {
        return $user->hasAnyRole(['Administrador', 'SuperAdmin'])
            && $pedido->estado === PedidoEstado::RecibidoCompleto;
    }
}
