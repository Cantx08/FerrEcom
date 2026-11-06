<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Herramientas', 'descripcion' => 'Herramientas manuales y eléctricas'],
            ['nombre' => 'Construcción', 'descripcion' => 'Materiales de obra, cemento, varillas'],
            ['nombre' => 'Eléctrico', 'descripcion' => 'Cables, focos, interruptores, breakers'],
            ['nombre' => 'Plomería', 'descripcion' => 'Tubos, llaves, mezcladoras, accesorios'],
            ['nombre' => 'Pinturas', 'descripcion' => 'Pinturas, rodillos, brochas, esmaltes'],
            ['nombre' => 'Tornillería', 'descripcion' => 'Tornillos, clavos, tacos, candados'],
            ['nombre' => 'Seguridad', 'descripcion' => 'Equipos de protección personal (EPP)'],
            ['nombre' => 'Iluminación', 'descripcion' => 'Lámparas, reflectores, focos LED']
        ];

        foreach ($categorias as $cat) {
            DB::table('categorias')->insert([
                'nombre' => $cat['nombre'],
                'slug' => Str::slug($cat['nombre']),
                'descripcion' => $cat['descripcion'],
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
