<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Migra los permisos agrupados (ej: gestionar_empleados) hacia
 * permisos granulares CRUD por módulo.
 *
 * Ejecutar en producción una sola vez:
 *   php artisan db:seed --class=PermissionGranularSeeder
 */
class PermissionGranularSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Definición de permisos granulares por módulo ─────────────────────────

        $modulos = [
            'empleados'   => ['ver', 'crear', 'editar', 'eliminar'],
            'roles'       => ['ver', 'crear', 'editar', 'eliminar'],
            'permisos'    => ['ver', 'crear', 'editar', 'eliminar'],
            'pedidos'     => ['ver', 'crear', 'editar', 'eliminar', 'aprobar', 'cancelar', 'pagar'],
            'ventas'      => ['ver', 'crear', 'editar', 'eliminar'],
            'productos'   => ['ver', 'crear', 'editar', 'eliminar'],
            'proveedores' => ['ver', 'crear', 'editar', 'eliminar'],
            'anuncios'    => ['ver', 'crear', 'editar', 'eliminar', 'aprobar'],
            'reportes'    => ['ver', 'exportar'],
            'dashboard'   => ['ver'],
            'retiros'     => ['ver', 'validar'],
            'catalogo'    => ['ver', 'crear', 'editar', 'eliminar'], // para proveedor
        ];

        $permisosCreados = [];

        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $nombre = "{$accion}_{$modulo}";
                $permiso = Permission::firstOrCreate(['name' => $nombre]);
                $permisosCreados[$nombre] = $permiso;
            }
        }

        // Permiso especial para compras online (solo Clientes)
        $permisosCreados['comprar_online'] = Permission::firstOrCreate(['name' => 'comprar_online']);

        // ─── Asignación por rol ───────────────────────────────────────────────────

        $asignaciones = [
            'SuperAdmin'    => array_keys($permisosCreados), // Todos los permisos

            'Administrador' => [
                'ver_empleados', 'crear_empleados', 'editar_empleados',
                'ver_roles', 'editar_roles',
                'ver_permisos',
                'ver_pedidos', 'crear_pedidos', 'editar_pedidos', 'aprobar_pedidos', 'cancelar_pedidos', 'pagar_pedidos',
                'ver_ventas', 'crear_ventas', 'editar_ventas',
                'ver_productos', 'crear_productos', 'editar_productos',
                'ver_proveedores', 'crear_proveedores', 'editar_proveedores',
                'ver_anuncios', 'crear_anuncios', 'editar_anuncios', 'eliminar_anuncios', 'aprobar_anuncios',
                'ver_reportes', 'exportar_reportes',
                'ver_dashboard',
                'ver_retiros', 'validar_retiros',
            ],

            'Supervisor'    => [
                'ver_anuncios', 'crear_anuncios', 'editar_anuncios',
                'ver_dashboard',
            ],

            'Bodeguero'     => [
                'ver_pedidos', 'crear_pedidos', 'cancelar_pedidos',
                'ver_productos',
                'ver_proveedores',
                'ver_dashboard',
            ],

            'Vendedor'      => [
                'ver_ventas', 'crear_ventas',
                'ver_retiros', 'validar_retiros',
                'ver_dashboard',
            ],

            'Proveedor'     => [
                'ver_pedidos',
                'ver_catalogo', 'crear_catalogo', 'editar_catalogo', 'eliminar_catalogo',
            ],

            'Cliente'       => [
                'comprar_online',
            ],
        ];

        foreach ($asignaciones as $rolNombre => $permisos) {
            $rol = Role::where('name', $rolNombre)->first();

            if (!$rol) {
                continue;
            }

            // Sincronizar (no agregar, reemplazar) los permisos del rol
            $rol->syncPermissions($permisos);
        }

        // ─── Limpiar permisos viejos agrupados ────────────────────────────────────

        $permisosViejos = [
            'gestionar_empleados',
            'gestionar_permisos',
            'gestionar_roles',
            'gestionar_pedidos',
            'gestionar_ventas',
            'gestionar_catalogo',
            'gestionar_anuncios',
            'generar_reportes',
            'ver_dashboard',      // se recrea granularmente
            'aprobar_anuncios',   // ahora es 'aprobar_anuncios' (sin cambio, pero fue en la lista vieja)
        ];

        // Solo eliminar los que ya no usamos y no existen en el nuevo esquema
        $permisosAEliminar = ['gestionar_empleados', 'gestionar_permisos', 'gestionar_roles',
                              'gestionar_pedidos', 'gestionar_ventas', 'gestionar_catalogo',
                              'gestionar_anuncios', 'generar_reportes'];

        Permission::whereIn('name', $permisosAEliminar)->delete();

        $this->command->info('✅ Permisos granulares creados y asignados correctamente.');
    }
}
