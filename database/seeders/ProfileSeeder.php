<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea perfiles (cédula, teléfono, dirección) con datos realistas de Ecuador
 * para todo usuario que aún no tenga uno, y agrega clientes adicionales de
 * prueba para poder buscarlos por cédula en el punto de venta (POS).
 */
class ProfileSeeder extends Seeder
{
    /**
     * Direcciones reales de Guayaquil y Quito para variar los perfiles.
     */
    private const DIRECCIONES = [
        'Av. Francisco de Orellana y Av. Rodrigo Chávez, Guayaquil',
        'Cdla. Kennedy Norte, Mz. 12 Villa 5, Guayaquil',
        'Av. 9 de Octubre y Boyacá, Guayaquil',
        'Urb. Puerto Azul, Vía a la Costa Km 12.5, Guayaquil',
        'Cdla. Alborada 3ra Etapa, Mz. A4, Guayaquil',
        'Av. Amazonas N34-451 y Naciones Unidas, Quito',
        'Sector La Carolina, Calle Suecia E9-45, Quito',
        'Av. Rodrigo de Chávez y Portete, Guayaquil',
        'Cdla. Sauces 9, Mz. 302 Villa 8, Guayaquil',
        'Av. Eloy Alfaro N45-12 y Portugal, Quito',
        'Vía a Samborondón Km 1.5, Samborondón, Guayas',
        'Urdesa Central, Circunvalación Sur, Guayaquil',
    ];

    /**
     * Clientes de prueba adicionales, con cédula fija y conocida para
     * poder probar la búsqueda por cédula en la venta POS.
     */
    private const CLIENTES_POS = [
        ['nombres' => 'María José',      'apellidos' => 'Andrade Zambrano', 'cedula' => '0912345678', 'telefono' => '0991234567'],
        ['nombres' => 'Carlos Eduardo',  'apellidos' => 'Vera Macías',      'cedula' => '0923456789', 'telefono' => '0982345678'],
        ['nombres' => 'Gabriela Alexandra', 'apellidos' => 'Torres Pincay', 'cedula' => '0934567890', 'telefono' => '0973456789'],
        ['nombres' => 'Luis Fernando',   'apellidos' => 'Chávez Bravo',     'cedula' => '0945678901', 'telefono' => '0964567890'],
        ['nombres' => 'Andrea Cristina', 'apellidos' => 'Loor Suárez',      'cedula' => '0956789012', 'telefono' => '0995678901'],
        ['nombres' => 'Jorge Andrés',    'apellidos' => 'Mendoza Rivas',    'cedula' => '1712345678', 'telefono' => '0986789012'],
    ];

    public function run(): void
    {
        // 1. Perfil para cualquier usuario ya existente que no tenga uno
        //    (staff creado en RolSeeder, proveedores creados en ProveedorSeeder).
        $usuariosSinPerfil = User::doesntHave('profile')->get();

        foreach ($usuariosSinPerfil as $index => $usuario) {
            $partesNombre = explode(' ', trim($usuario->name), 2);

            Profile::create([
                'user_id'             => $usuario->id,
                'nombres'             => $partesNombre[0] ?? $usuario->name,
                'apellidos'           => $partesNombre[1] ?? 'Ferretería',
                'email'               => $usuario->email,
                'telefono'            => '09' . str_pad((string) (80000000 + $usuario->id), 8, '0', STR_PAD_LEFT),
                'tipo_identificacion' => 'CEDULA',
                'identificacion'      => '17' . str_pad((string) (10000000 + $usuario->id), 8, '0', STR_PAD_LEFT),
                'direccion'           => self::DIRECCIONES[$index % count(self::DIRECCIONES)],
                'estado'              => true,
            ]);
        }

        // 2. Clientes de prueba con cédula fija, pensados para buscarse en el POS.
        foreach (self::CLIENTES_POS as $index => $datos) {
            $email = strtolower(str_replace(' ', '.', $datos['nombres'])) . '@gmail.com';

            $cliente = User::create([
                'name'     => $datos['nombres'] . ' ' . $datos['apellidos'],
                'email'    => $email,
                'password' => Hash::make('password123'),
            ]);
            $cliente->assignRole('Cliente');

            Profile::create([
                'user_id'             => $cliente->id,
                'nombres'             => $datos['nombres'],
                'apellidos'           => $datos['apellidos'],
                'email'               => $email,
                'telefono'            => $datos['telefono'],
                'tipo_identificacion' => 'CEDULA',
                'identificacion'      => $datos['cedula'],
                'direccion'           => self::DIRECCIONES[$index % count(self::DIRECCIONES)],
                'estado'              => true,
            ]);
        }

        $this->command->info('Perfiles creados. Cédulas de clientes de prueba para el POS: ' . implode(', ', array_column(self::CLIENTES_POS, 'cedula')));
    }
}
