<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionsController extends Controller
{
    /**
     * Muestra el formulario para asignar permisos a un rol.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.role-permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $role = Role::findOrFail($id);
        $permissions = request('permissions', []);

        $role->syncPermissions($permissions);

        return redirect()->route('role-permissions.edit', $role->id)->with('success', 'Permisos actualizados exitosamente.');
    }
}
