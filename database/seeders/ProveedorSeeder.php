<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Proveedor;
use App\Models\Categoria;
use Illuminate\Support\Facades\Hash;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = Categoria::all();

        foreach ($categorias as $index => $categoria) {
            // Crear usuario proveedor
            $user = User::create([
                'name' => 'Proveedor ' . $categoria->nombre,
                'email' => 'proveedor' . ($index + 1) . '@ferremax.com',
                'password' => Hash::make('password123'),
            ]);
            $user->assignRole('Proveedor');

            // Crear registro de proveedor asociado a la categoría
            Proveedor::create([
                'user_id' => $user->id,
                'categoria_id' => $categoria->id,
                'nombre_compania' => 'Suministros ' . $categoria->nombre . ' S.A.',
                'ruc' => '099000000000' . ($index + 1),
                'telefono' => '04200000' . ($index + 1),
                'email' => 'ventas@suministros' . strtolower(str_replace(' ', '', $categoria->nombre)) . '.com',
                'direccion' => 'Av. Proveedores ' . ($index + 1) . ', Guayaquil',
            ]);
        }
    }
}
