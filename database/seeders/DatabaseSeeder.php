<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Orden importante: cada seeder depende de que el/los anteriores ya
     * hayan corrido (roles antes que usuarios, categorías antes que
     * proveedores, proveedores y categorías antes que productos, etc.).
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,               // Roles + usuarios de staff de prueba
            PermissionGranularSeeder::class, // Permisos granulares por rol
            CategoriaSeeder::class,          // Categorías de productos
            ProveedorSeeder::class,          // Proveedores (1 por categoría) + su usuario
            ProductoSeeder::class,           // Catálogo de productos (inventario + catálogo proveedor)
            ProfileSeeder::class,            // Perfiles (cédula, teléfono, dirección) para POS
            TransaccionesSeeder::class,      // Historial de ventas y pedidos de compra de prueba
        ]);
    }
}
