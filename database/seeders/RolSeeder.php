<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Crea los roles base del sistema y un usuario de prueba por rol interno
 * (Proveedor y Clientes adicionales se crean en ProveedorSeeder/ProfileSeeder).
 *
 * Los permisos se definen y asignan en PermissionGranularSeeder, que corre
 * inmediatamente después de este seeder.
 */
class RolSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['SuperAdmin', 'Administrador', 'Supervisor', 'Bodeguero', 'Vendedor', 'Proveedor', 'Cliente'] as $rol) {
            Role::create(['name' => $rol]);
        }

        $usuarios = [
            ['name' => 'Super Admin',       'email' => 'superadmin@ferremax.com', 'role' => 'SuperAdmin'],
            ['name' => 'Dueño Ferretería',  'email' => 'admin@ferremax.com',      'role' => 'Administrador'],
            ['name' => 'Vendedor Juan',     'email' => 'vendedor@ferremax.com',   'role' => 'Vendedor'],
            ['name' => 'Bodeguero Carlos',  'email' => 'bodeguero@ferremax.com',  'role' => 'Bodeguero'],
            ['name' => 'Supervisor Ana',    'email' => 'supervisor@ferremax.com', 'role' => 'Supervisor'],
            ['name' => 'Cliente Test',      'email' => 'cliente@ferremax.com',    'role' => 'Cliente'],
        ];

        foreach ($usuarios as $datos) {
            $usuario = User::create([
                'name'     => $datos['name'],
                'email'    => $datos['email'],
                'password' => Hash::make('password123'),
            ]);
            $usuario->assignRole($datos['role']);
        }
    }
}
