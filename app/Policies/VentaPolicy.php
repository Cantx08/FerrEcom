<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    /**
     * Vendedor y Administrador pueden crear ventas POS.
     */
    public function crearPOS(User $user): bool
    {
        return $user->hasAnyRole(['Vendedor', 'Administrador', 'SuperAdmin']);
    }

    /**
     * Solo el rol Cliente puede realizar compras online.
     */
    public function crearOnline(User $user): bool
    {
        return $user->hasRole('Cliente');
    }

    /**
     * Administrador y Vendedor pueden ver el listado de ventas en el panel.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Vendedor', 'Administrador', 'SuperAdmin']);
    }

    /**
     * El cajero/vendedor puede validar retiros en caja.
     */
    public function validarRetiro(User $user): bool
    {
        return $user->hasAnyRole(['Vendedor', 'Administrador', 'SuperAdmin']);
    }

    /**
     * Solo Administrador puede avanzar el estado de una venta online manualmente.
     */
    public function avanzarEstado(User $user, Venta $venta): bool
    {
        return $user->hasAnyRole(['Administrador', 'SuperAdmin']);
    }
}
