<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Roles que el Administrador NUNCA puede crear, editar, eliminar
     * ni cuyos permisos puede modificar (reservados a SuperAdmin).
     */
    private const ROLES_RESTRINGIDOS_ADMIN = ['SuperAdmin', 'Cliente', 'Proveedor'];

    /**
     * SuperAdmin y Administrador pueden ver el listado de roles.
     */
    public function viewAny(User $actor): bool
    {
        return $actor->hasAnyRole(['SuperAdmin', 'Administrador']);
    }

    /**
     * SuperAdmin: crea cualquier rol.
     * Administrador: solo puede crear roles cuyo nombre no esté reservado.
     * (el nombre aún no existe como modelo, así que se valida por string)
     */
    public function crear(User $actor, string $nombreRol): bool
    {
        if ($actor->hasRole('SuperAdmin')) {
            return true;
        }

        if ($actor->hasRole('Administrador')) {
            return !in_array($nombreRol, self::ROLES_RESTRINGIDOS_ADMIN, strict: true);
        }

        return false;
    }

    /**
     * SuperAdmin: puede editar cualquier rol (nombre y permisos).
     * Administrador: solo roles Administrador, Bodeguero, Vendedor, Supervisor.
     */
    public function update(User $actor, Role $rol): bool
    {
        if ($actor->hasRole('SuperAdmin')) {
            return true;
        }

        if ($actor->hasRole('Administrador')) {
            return !in_array($rol->name, self::ROLES_RESTRINGIDOS_ADMIN, strict: true);
        }

        return false;
    }

    /**
     * Solo SuperAdmin puede eliminar roles, y nunca los roles base del sistema
     * (romperían el middleware `role:` de las rutas).
     */
    public function delete(User $actor, Role $rol): bool
    {
        if (!$actor->hasRole('SuperAdmin')) {
            return false;
        }

        return !in_array($rol->name, self::ROLES_RESTRINGIDOS_ADMIN, strict: true);
    }
}
