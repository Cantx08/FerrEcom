<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Policies\UserPolicy;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Roles que solo el SuperAdmin puede asignar (el Admin no puede tocarlos).
     */
    private const ROLES_SOLO_SUPERADMIN = ['SuperAdmin', 'Cliente', 'Proveedor'];

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = $this->rolesDisponibles();
        return view('admin.users.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|exists:roles,name',
        ]);

        // Verificar que el actor puede asignar este rol
        $this->autorizarAsignacionRol($validated['role']);

        $usuario = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $usuario->assignRole($validated['role']);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = $this->rolesDisponibles();
        return view('admin.users.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role'     => 'required|string|exists:roles,name',
        ]);

        $this->autorizarAsignacionRol($validated['role']);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    // ─── Helpers privados ────────────────────────────────────────────────────────

    /**
     * Devuelve los roles que el usuario autenticado puede asignar.
     */
    private function rolesDisponibles(): \Illuminate\Database\Eloquent\Collection
    {
        if (auth()->user()->hasRole('SuperAdmin')) {
            return Role::all();
        }

        // El Administrador solo puede asignar roles operativos
        return Role::whereNotIn('name', self::ROLES_SOLO_SUPERADMIN)->get();
    }

    /**
     * Aborta si el usuario actual no puede asignar el rol dado.
     */
    private function autorizarAsignacionRol(string $rolNombre): void
    {
        if (!auth()->user()->can('asignarRol', [User::class, $rolNombre])) {
            abort(403, "No tienes permiso para asignar el rol '{$rolNombre}'.");
        }
    }
}
