<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categoria;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $costo = $this->faker->randomFloat(2, 5, 150);
        $precio = $costo * 1.4; // 40% margin
        
        return [
            'sku' => 'FERR-' . $this->faker->unique()->randomNumber(6, true),
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'unidad_medida' => 'UNIDAD',
            'precio_venta' => $precio,
            'precio_compra' => $costo,
            'stock_actual' => $this->faker->numberBetween(10, 100),
            'stock_minimo' => 5,
            'estado' => true,
        ];
    }
}
