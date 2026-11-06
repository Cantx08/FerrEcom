<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Roles que el Administrador NUNCA puede asignar.
     */
    private const ROLES_RESTRINGIDOS_ADMIN = ['SuperAdmin', 'Cliente', 'Proveedor'];

    /**
     * Roles que el Administrador SÍ puede crear/gestionar.
     */
    private const ROLES_ADMIN_PERMITIDOS = ['Administrador', 'Bodeguero', 'Vendedor', 'Supervisor'];

    /**
     * Solo SuperAdmin puede ver todos los usuarios.
     * Administrador puede ver los usuarios de su equipo (no SuperAdmin, no Cliente, no Proveedor).
     */
    public function viewAny(User $actor): bool
    {
        return $actor->hasAnyRole(['SuperAdmin', 'Administrador']);
    }

    /**
     * SuperAdmin: puede crear cualquier usuario con cualquier rol.
     * Administrador: solo puede crear usuarios con roles de su equipo operativo.
     */
    public function create(User $actor): bool
    {
        return $actor->hasAnyRole(['SuperAdmin', 'Administrador']);
    }

    /**
     * Verifica si el actor puede asignar el rol dado al crear/editar un usuario.
     */
    public function asignarRol(User $actor, string $rolNombre): bool
    {
        if ($actor->hasRole('SuperAdmin')) {
            return true;
        }

        if ($actor->hasRole('Administrador')) {
            return in_array($rolNombre, self::ROLES_ADMIN_PERMITIDOS);
        }

        return false;
    }

    /**
     * SuperAdmin: puede editar cualquier usuario.
     * Administrador: puede editar usuarios de su equipo (no SuperAdmin, no Cliente, no Proveedor).
     */
    public function update(User $actor, User $usuarioObjetivo): bool
    {
        if ($actor->hasRole('SuperAdmin')) {
            return true;
        }

        if ($actor->hasRole('Administrador')) {
            // No puede editar SuperAdmin, Clientes ni Proveedores
            return !$usuarioObjetivo->hasAnyRole(self::ROLES_RESTRINGIDOS_ADMIN);
        }

        return false;
    }

    /**
     * Solo SuperAdmin puede eliminar usuarios.
     */
    public function delete(User $actor, User $usuarioObjetivo): bool
    {
        if (!$actor->hasRole('SuperAdmin')) {
            return false;
        }

        // El SuperAdmin no puede eliminarse a sí mismo
        return $actor->id !== $usuarioObjetivo->id;
    }

    /**
     * Administrador puede asignar permisos directos a usuarios con roles operativos.
     * SuperAdmin puede asignar a cualquiera.
     */
    public function asignarPermisos(User $actor, User $usuarioObjetivo): bool
    {
        if ($actor->hasRole('SuperAdmin')) {
            return true;
        }

        if ($actor->hasRole('Administrador')) {
            return $usuarioObjetivo->hasAnyRole(self::ROLES_ADMIN_PERMITIDOS);
        }

        return false;
    }
}
