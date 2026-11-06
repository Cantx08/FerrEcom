<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proveedor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductoProveedor>
 */
class ProductoProveedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => 'PROV-' . $this->faker->unique()->randomNumber(6, true),
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(10),
            'costo_unitario' => $this->faker->randomFloat(2, 5, 200),
            'stock_disponible' => $this->faker->numberBetween(50, 500),
            'estado' => true,
        ];
    }
}
