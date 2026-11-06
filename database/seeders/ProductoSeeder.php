<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\ProductoProveedor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Crea el catálogo de productos (inventario propio de la ferretería) y su
 * contraparte en el catálogo de cada proveedor, con precios de compra/venta
 * realistas para el mercado ecuatoriano (USD).
 *
 * Un subconjunto de productos incluye precio por mayor (cantidad_por_mayor /
 * descuento_por_mayor) para poder probar el descuento al por mayor en ventas.
 */
class ProductoSeeder extends Seeder
{
    /**
     * n: nombre, d: descripción, pc: precio compra, pv: precio venta,
     * um: unidad de medida (default UNIDAD si se omite),
     * cpm: cantidad mínima para precio por mayor, dpm: % de descuento por mayor.
     */
    private function productosReales(): array
    {
        return [
            'Herramientas' => [
                ['n' => 'Taladro Percutor Bosch 1/2"', 'd' => 'Taladro percutor profesional de 700W con maletín de transporte.', 'pc' => 65.00, 'pv' => 95.00],
                ['n' => 'Amoladora Angular Makita 4-1/2"', 'd' => 'Amoladora angular de 840W ideal para cortes en metal y concreto.', 'pc' => 55.00, 'pv' => 85.00],
                ['n' => 'Juego de Llaves Mixtas Stanley', 'd' => 'Set de 14 llaves mixtas de acero cromo vanadio (1/4 a 1 pulgada).', 'pc' => 18.00, 'pv' => 28.00, 'um' => 'JUEGO'],
                ['n' => 'Martillo de Uña Truper 16 oz', 'd' => 'Martillo con mango de fibra de vidrio y empuñadura ergonómica.', 'pc' => 6.50, 'pv' => 11.00],
                ['n' => 'Sierra Circular DeWalt 7-1/4"', 'd' => 'Sierra circular de 1400W con disco de corte para madera.', 'pc' => 115.00, 'pv' => 160.00],
                ['n' => 'Destornillador Phillips Truper', 'd' => 'Destornillador punta de cruz 1/4" x 4" magnético.', 'pc' => 2.50, 'pv' => 4.50, 'cpm' => 12, 'dpm' => 10],
                ['n' => 'Alicate Universal Stanley 8"', 'd' => 'Alicate universal forjado en acero con mango antideslizante.', 'pc' => 8.00, 'pv' => 14.00],
                ['n' => 'Cinta Métrica Global Plus 5m', 'd' => 'Flexómetro de 5 metros con carcasa resistente a impactos.', 'pc' => 3.50, 'pv' => 6.00, 'cpm' => 10, 'dpm' => 8],
                ['n' => 'Llave Francesa Ajustable 10"', 'd' => 'Llave ajustable de 10 pulgadas de acero forjado.', 'pc' => 12.00, 'pv' => 18.50],
                ['n' => 'Taladro Inalámbrico 20V Max', 'd' => 'Taladro atornillador inalámbrico con 2 baterías de litio.', 'pc' => 140.00, 'pv' => 195.00],
            ],
            'Construcción' => [
                ['n' => 'Cemento Portland Tipo 1 Holcim', 'd' => 'Saco de cemento estructural de 50 kg de alta resistencia.', 'pc' => 7.20, 'pv' => 8.50, 'um' => 'SACO', 'cpm' => 20, 'dpm' => 8],
                ['n' => 'Varilla Corrugada 12mm Adelca', 'd' => 'Varilla de acero corrugado de 12mm x 12m para construcción.', 'pc' => 10.50, 'pv' => 12.00, 'cpm' => 25, 'dpm' => 10],
                ['n' => 'Bloque de Hormigón 15x20x40', 'd' => 'Bloque prensado de hormigón estándar para mampostería.', 'pc' => 0.35, 'pv' => 0.50, 'cpm' => 100, 'dpm' => 12],
                ['n' => 'Arena Fina Lavada (Saco)', 'd' => 'Saco de arena fina especial para enlucidos y acabados.', 'pc' => 1.20, 'pv' => 2.00, 'um' => 'SACO', 'cpm' => 15, 'dpm' => 10],
                ['n' => 'Tubo PVC Desagüe 4" x 3m', 'd' => 'Tubo de PVC rígido sanitario de 4 pulgadas de diámetro.', 'pc' => 5.50, 'pv' => 8.00],
                ['n' => 'Saco de Yeso Blanco 25 kg', 'd' => 'Yeso de construcción de fraguado rápido para acabados.', 'pc' => 4.50, 'pv' => 6.50, 'um' => 'SACO'],
                ['n' => 'Malla Electrosoldada 5x5', 'd' => 'Panel de malla electrosoldada para refuerzo de contrapisos.', 'pc' => 25.00, 'pv' => 32.00],
                ['n' => 'Adoquín de Hormigón Vehicular', 'd' => 'Adoquín resistente para alto tráfico vehicular.', 'pc' => 0.25, 'pv' => 0.40],
                ['n' => 'Alambre Recocido Negro', 'd' => 'Rollo de alambre negro recocido #18 para amarre de varillas.', 'pc' => 1.50, 'pv' => 2.50, 'um' => 'ROLLO', 'cpm' => 10, 'dpm' => 8],
                ['n' => 'Empaste Acrílico Interior', 'd' => 'Caneca de empaste acrílico para preparación de paredes.', 'pc' => 18.00, 'pv' => 25.00, 'um' => 'CANECA'],
            ],
            'Eléctrico' => [
                ['n' => 'Cable Sólido THHN #12 Cobre', 'd' => 'Rollo de 100m de cable de cobre sólido THHN AWG #12.', 'pc' => 38.00, 'pv' => 55.00, 'um' => 'ROLLO', 'cpm' => 5, 'dpm' => 10],
                ['n' => 'Interruptor Doble Ticino', 'd' => 'Interruptor de pared empotrable doble blanco.', 'pc' => 2.80, 'pv' => 4.50, 'cpm' => 20, 'dpm' => 10],
                ['n' => 'Breaker Termomagnético 20A', 'd' => 'Disyuntor de protección termomagnética de 20 amperios 110V.', 'pc' => 3.50, 'pv' => 6.00],
                ['n' => 'Tomacorriente Doble con Tierra', 'd' => 'Tomacorriente empotrable doble con conector de puesta a tierra.', 'pc' => 2.20, 'pv' => 3.80, 'cpm' => 20, 'dpm' => 10],
                ['n' => 'Cinta Aislante 3M Super 33+', 'd' => 'Cinta aislante de vinilo premium de 19mm x 20m color negro.', 'pc' => 3.50, 'pv' => 5.50],
                ['n' => 'Tubo Conduit PVC 1/2" x 3m', 'd' => 'Tubería rígida de PVC conduit para canalización eléctrica.', 'pc' => 1.20, 'pv' => 2.00],
                ['n' => 'Cajetín Rectangular Plástico', 'd' => 'Cajetín de pared para interruptores y tomacorrientes 4x2.', 'pc' => 0.40, 'pv' => 0.80],
                ['n' => 'Reflector LED Exterior 50W', 'd' => 'Reflector LED de alta potencia resistente al agua IP65.', 'pc' => 15.00, 'pv' => 24.00],
                ['n' => 'Multímetro Digital Fluke 101', 'd' => 'Tester multímetro digital básico portátil para electricistas.', 'pc' => 45.00, 'pv' => 65.00],
                ['n' => 'Tablero de Distribución 8 Polos', 'd' => 'Caja metálica para 8 breakers enchufables monofásica.', 'pc' => 18.00, 'pv' => 26.00],
            ],
            'Plomería' => [
                ['n' => 'Tubo de Presión PVC 1/2" Agua Fría', 'd' => 'Tubería de PVC de presión de 1/2 pulgada para agua fría.', 'pc' => 2.50, 'pv' => 4.00, 'cpm' => 20, 'dpm' => 10],
                ['n' => 'Llave de Paso Bola Bronce 1/2"', 'd' => 'Válvula de bola de latón forjado roscada de media pulgada.', 'pc' => 4.50, 'pv' => 7.00],
                ['n' => 'Mezcladora para Fregadero FV', 'd' => 'Grifería cromada mezcladora de pared para cocina.', 'pc' => 25.00, 'pv' => 38.00],
                ['n' => 'Sifón Plástico Tipo P', 'd' => 'Sifón de desagüe tipo P ajustable para lavabos.', 'pc' => 2.20, 'pv' => 4.00],
                ['n' => 'Teflón en Cinta 3/4" x 12m', 'd' => 'Cinta de teflón de alta densidad para sellado de roscas.', 'pc' => 0.50, 'pv' => 1.00, 'cpm' => 24, 'dpm' => 10],
                ['n' => 'Inodoro de Dos Piezas Edesa', 'd' => 'Inodoro cerámico blanco con tanque bajo de doble descarga.', 'pc' => 85.00, 'pv' => 120.00],
                ['n' => 'Pegamento para Tubo PVC 1/4 Gl', 'd' => 'Cemento solvente transparente para tuberías de PVC.', 'pc' => 4.00, 'pv' => 6.50],
                ['n' => 'Codo PVC Presión 90° 1/2"', 'd' => 'Accesorio codo liso de 90 grados para tubería de agua.', 'pc' => 0.25, 'pv' => 0.50, 'cpm' => 50, 'dpm' => 15],
                ['n' => 'Bomba de Agua Periférica 1/2 HP', 'd' => 'Bomba de agua eléctrica periférica de 1/2 caballo de fuerza.', 'pc' => 28.00, 'pv' => 45.00],
                ['n' => 'Tanque Cisterna Rotoplas 1100L', 'd' => 'Tanque de polietileno de triple capa para reserva de agua.', 'pc' => 180.00, 'pv' => 230.00],
            ],
            'Pinturas' => [
                ['n' => 'Pintura Látex Suprema Blanco 1Gl', 'd' => 'Pintura látex acrílica lavable de alta cobertura color blanco.', 'pc' => 12.00, 'pv' => 18.00, 'um' => 'GALON', 'cpm' => 5, 'dpm' => 8],
                ['n' => 'Esmalte Sintético Anticorrosivo Negro', 'd' => 'Pintura esmalte brillante para protección de metales.', 'pc' => 14.00, 'pv' => 22.00, 'um' => 'GALON', 'cpm' => 5, 'dpm' => 8],
                ['n' => 'Rodillo de Felpa 9" Antigota', 'd' => 'Rodillo para pintura interior con mango plástico reforzado.', 'pc' => 3.50, 'pv' => 6.00],
                ['n' => 'Brocha de Cerdas Naturales 3"', 'd' => 'Brocha para madera y esmaltes de 3 pulgadas de ancho.', 'pc' => 2.20, 'pv' => 4.00],
                ['n' => 'Thinner Acrílico 1 Galón', 'd' => 'Disolvente thinner formulado para pinturas acrílicas y limpieza.', 'pc' => 8.00, 'pv' => 12.50, 'um' => 'GALON'],
                ['n' => 'Bandeja Plástica para Pintura', 'd' => 'Bandeja acanalada de plástico resistente para rodillos de 9".', 'pc' => 1.80, 'pv' => 3.50],
                ['n' => 'Cinta Masking Tape 2"', 'd' => 'Cinta de papel adhesivo de enmascarar para trabajos de pintura.', 'pc' => 2.50, 'pv' => 4.00, 'cpm' => 12, 'dpm' => 10],
                ['n' => 'Sellador de Paredes Interiores', 'd' => 'Sellador acrílico base agua para preparar superficies porosas.', 'pc' => 10.00, 'pv' => 15.00, 'um' => 'GALON'],
                ['n' => 'Impermeabilizante Techos Rojo', 'd' => 'Pintura elastomérica impermeabilizante para cubiertas.', 'pc' => 35.00, 'pv' => 48.00, 'um' => 'GALON'],
                ['n' => 'Aerosol Esmalte Acrílico Plata', 'd' => 'Pintura en spray de secado rápido color plata brillante.', 'pc' => 2.80, 'pv' => 5.00],
            ],
            'Tornillería' => [
                ['n' => 'Caja de Tornillos Drywall 6x1-1/4"', 'd' => 'Caja de 1000 tornillos negros punta fina para gypsum.', 'pc' => 8.50, 'pv' => 14.00, 'um' => 'CAJA', 'cpm' => 5, 'dpm' => 12],
                ['n' => 'Clavos de Acero para Concreto 2"', 'd' => 'Funda de 1 kg de clavos estriados de acero templado.', 'pc' => 2.20, 'pv' => 4.00, 'um' => 'FUNDA', 'cpm' => 10, 'dpm' => 10],
                ['n' => 'Tacos Fisher Plásticos 8mm (100u)', 'd' => 'Funda de 100 tacos de expansión de nylon de 8mm.', 'pc' => 2.50, 'pv' => 4.50, 'um' => 'FUNDA'],
                ['n' => 'Tornillo Autoperforante Techo 2"', 'd' => 'Tornillo galvanizado cabeza hexagonal con arandela neopreno.', 'pc' => 0.08, 'pv' => 0.15, 'cpm' => 200, 'dpm' => 15],
                ['n' => 'Tuerca Hexagonal de Acero Inox 3/8"', 'd' => 'Paquete de tuercas de acero inoxidable rosca estándar 3/8.', 'pc' => 0.12, 'pv' => 0.25, 'um' => 'FUNDA', 'cpm' => 200, 'dpm' => 15],
                ['n' => 'Arandela Plana Galvanizada 1/4"', 'd' => 'Funda de arandelas planas para distribución de carga.', 'pc' => 0.03, 'pv' => 0.10, 'um' => 'FUNDA', 'cpm' => 300, 'dpm' => 20],
                ['n' => 'Tirafondo Hexagonal 5/16" x 3"', 'd' => 'Perno tirafondo para madera de alta resistencia.', 'pc' => 0.20, 'pv' => 0.40],
                ['n' => 'Candado de Latón Macizo 50mm', 'd' => 'Candado de seguridad con cuerpo de bronce y arco de acero templado.', 'pc' => 6.50, 'pv' => 11.00],
                ['n' => 'Remaches Pop de Aluminio 1/8"', 'd' => 'Caja de 500 remaches ciegos de aluminio cabeza de hongo.', 'pc' => 3.50, 'pv' => 6.00, 'um' => 'CAJA'],
                ['n' => 'Bisagra Biselada 3" x 3" (Par)', 'd' => 'Par de bisagras de acero zincado para puertas interiores.', 'pc' => 1.80, 'pv' => 3.50, 'um' => 'PAR'],
            ],
            'Seguridad' => [
                ['n' => 'Casco de Seguridad Industrial Tipo I', 'd' => 'Casco de protección dieléctrico en color amarillo.', 'pc' => 4.50, 'pv' => 8.00],
                ['n' => 'Guantes de Cuero Mosquetero', 'd' => 'Guantes de carnaza reforzados para soldadura y trabajo pesado.', 'pc' => 2.50, 'pv' => 4.50, 'um' => 'PAR', 'cpm' => 12, 'dpm' => 10],
                ['n' => 'Gafas de Protección Transparentes', 'd' => 'Lentes de policarbonato anti-impactos y anti-rayaduras.', 'pc' => 1.80, 'pv' => 3.50],
                ['n' => 'Botas Punta de Acero Talla 42', 'd' => 'Calzado de seguridad industrial dieléctrico de cuero negro.', 'pc' => 22.00, 'pv' => 35.00, 'um' => 'PAR'],
                ['n' => 'Mascarilla Respirador N95 (Caja)', 'd' => 'Caja de 20 mascarillas para partículas y polvo fino.', 'pc' => 12.00, 'pv' => 20.00, 'um' => 'CAJA', 'cpm' => 5, 'dpm' => 10],
                ['n' => 'Arnés de Seguridad Cuerpo Entero', 'd' => 'Arnés anticaídas con tres argollas y línea de vida.', 'pc' => 28.00, 'pv' => 45.00],
                ['n' => 'Chaleco Reflectivo Naranja', 'd' => 'Chaleco ligero con bandas reflectantes de alta visibilidad.', 'pc' => 3.50, 'pv' => 6.00, 'cpm' => 10, 'dpm' => 10],
                ['n' => 'Tapones Auditivos de Silicona', 'd' => 'Tapones reutilizables con cordón para protección auditiva.', 'pc' => 1.20, 'pv' => 2.50, 'um' => 'PAR'],
                ['n' => 'Cinta de Peligro 300m', 'd' => 'Rollo de cinta plástica de señalización amarilla y negra.', 'pc' => 4.50, 'pv' => 8.00, 'um' => 'ROLLO'],
                ['n' => 'Extintor PQS de 10 lbs', 'd' => 'Extintor recargable de Polvo Químico Seco clase ABC.', 'pc' => 25.00, 'pv' => 40.00],
            ],
            'Iluminación' => [
                ['n' => 'Foco LED Bulbo 9W Luz Blanca', 'd' => 'Foco LED base E27 6500K equivalente a 60W tradicionales.', 'pc' => 1.20, 'pv' => 2.50, 'cpm' => 20, 'dpm' => 12],
                ['n' => 'Panel LED Empotrable 18W Redondo', 'd' => 'Luminaria LED ultradelgada para cielorrasos luz blanca.', 'pc' => 3.50, 'pv' => 6.00],
                ['n' => 'Tubo LED T8 18W 120cm', 'd' => 'Tubo de vidrio LED conexión a un solo lado, reemplazo directo.', 'pc' => 2.80, 'pv' => 5.00, 'cpm' => 20, 'dpm' => 10],
                ['n' => 'Reflector LED Solar 100W', 'd' => 'Reflector con panel solar integrado y control remoto.', 'pc' => 25.00, 'pv' => 42.00],
                ['n' => 'Lámpara de Emergencia 30 LEDs', 'd' => 'Lámpara recargable con autonomía de 8 horas.', 'pc' => 8.50, 'pv' => 14.00],
                ['n' => 'Farol Exterior de Pared Clásico', 'd' => 'Farol decorativo de aluminio color negro para exteriores.', 'pc' => 12.00, 'pv' => 20.00],
                ['n' => 'Cinta LED 5050 RGB 5m', 'd' => 'Rollo de tira LED multicolor con controlador y fuente de poder.', 'pc' => 10.00, 'pv' => 18.00, 'um' => 'ROLLO'],
                ['n' => 'Dicroico LED 5W GU10 Luz Cálida', 'd' => 'Foco dicroico direccional de luz cálida 3000K.', 'pc' => 2.20, 'pv' => 4.00],
                ['n' => 'Luminaria Hermética para 2 Tubos LED', 'd' => 'Carcasa IP65 a prueba de polvo y humedad para parqueos.', 'pc' => 15.00, 'pv' => 25.00],
                ['n' => 'Campana Industrial LED 150W', 'd' => 'Luminaria UFO High Bay para galpones y bodegas de doble altura.', 'pc' => 35.00, 'pv' => 55.00],
            ],
        ];
    }

    public function run(): void
    {
        $productosReales = $this->productosReales();
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();

        foreach ($proveedores as $proveedor) {
            $categoria = $categorias->where('id', $proveedor->categoria_id)->first();
            $nombresCategoria = $productosReales[$categoria->nombre] ?? null;

            for ($i = 0; $i < 10; $i++) {
                if ($nombresCategoria && isset($nombresCategoria[$i % count($nombresCategoria)])) {
                    $baseItem = $nombresCategoria[$i % count($nombresCategoria)];
                    $nombreReal = $baseItem['n'] . ($i >= count($nombresCategoria) ? ' (Variante ' . ($i - count($nombresCategoria) + 1) . ')' : '');
                    $descReal = $baseItem['d'];
                    $precioCompra = $baseItem['pc'];
                    $precioVenta = $baseItem['pv'];
                    $unidadMedida = $baseItem['um'] ?? 'UNIDAD';
                    $cantidadPorMayor = $baseItem['cpm'] ?? null;
                    $descuentoPorMayor = $baseItem['dpm'] ?? null;
                } else {
                    $nombreReal = 'Producto ' . $categoria->nombre . ' ' . ($i + 1);
                    $descReal = 'Descripción genérica para ' . $nombreReal;
                    $precioCompra = 10.00;
                    $precioVenta = 15.00;
                    $unidadMedida = 'UNIDAD';
                    $cantidadPorMayor = null;
                    $descuentoPorMayor = null;
                }

                // Producto del proveedor (catálogo que compra la ferretería)
                $prodProveedor = ProductoProveedor::factory()->create([
                    'proveedor_id'   => $proveedor->id,
                    'nombre'         => $nombreReal,
                    'descripcion'    => $descReal,
                    'costo_unitario' => $precioCompra,
                    'unidad'         => $unidadMedida,
                ]);

                $slugName = str_replace('/', '-', $nombreReal);
                $imageName = Str::slug($slugName) . '.jpg';
                $imagePath = 'images/productos/' . $imageName;

                if (!File::exists(public_path($imagePath))) {
                    $imagePath = null;
                }

                // Producto de la ferretería (inventario que se vende a clientes).
                // Si tiene precio por mayor, garantizamos stock suficiente para poder
                // probar ventas al por mayor (varias veces la cantidad mínima).
                $atributosProducto = [
                    'categoria_id'        => $categoria->id,
                    'nombre'              => $prodProveedor->nombre,
                    'descripcion'         => $prodProveedor->descripcion,
                    'unidad_medida'       => $unidadMedida,
                    'precio_compra'       => $precioCompra,
                    'precio_venta'        => $precioVenta,
                    'cantidad_por_mayor'  => $cantidadPorMayor,
                    'descuento_por_mayor' => $descuentoPorMayor,
                    'imagen'              => $imagePath,
                ];

                if ($cantidadPorMayor) {
                    $atributosProducto['stock_actual'] = $cantidadPorMayor * rand(3, 6);
                }

                Producto::factory()->create($atributosProducto);
            }
        }
    }
}
