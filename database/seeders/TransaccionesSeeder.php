<?php

namespace Database\Seeders;

use App\Enums\MetodoEntrega;
use App\Enums\MetodoPago;
use App\Enums\PedidoEstado;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Services\PedidoCompraService;
use App\Services\VentaService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Genera historial de ventas (POS y online) y pedidos de compra a proveedores
 * usando los servicios reales (VentaService / PedidoCompraService), en vez de
 * insertar filas directamente. Así el seeder ejercita — y por lo tanto
 * verifica — la misma lógica de negocio que usa la aplicación en producción:
 * descuento por mayor, descuento de stock al vender, e incremento de stock al
 * recibir un pedido de compra.
 *
 * Las fechas se recorren hacia atrás (más recientes en los últimos registros)
 * y se ajustan directamente en BD después de cada creación, únicamente para
 * dar variedad histórica a los gráficos del dashboard — la lógica de negocio
 * ya se ejecutó con datos "de hoy" antes de mover la fecha.
 */
class TransaccionesSeeder extends Seeder
{
    private VentaService $ventaService;
    private PedidoCompraService $pedidoService;

    public function __construct()
    {
        $this->ventaService = app(VentaService::class);
        $this->pedidoService = app(PedidoCompraService::class);
    }

    public function run(): void
    {
        $vendedores = User::role('Vendedor')->get();
        $administradores = User::role('Administrador')->get();
        $bodegueros = User::role('Bodeguero')->get();
        $clientes = User::role('Cliente')->get();
        $proveedores = Proveedor::with('productoProveedor')->get();
        $productos = Producto::all();

        if ($vendedores->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('Faltan vendedores o productos: corra RolSeeder/ProductoSeeder antes que este seeder.');
            return;
        }

        $this->generarVentasPOS($vendedores, $clientes, $productos);
        $this->generarVentasAlPorMayor($vendedores, $clientes, $productos);
        $this->generarVentasOnline($clientes, $productos);
        $this->generarPedidosCompra($administradores, $bodegueros, $proveedores);

        $this->command->info('Ventas y pedidos de compra generados correctamente.');
    }

    // ─── Ventas ──────────────────────────────────────────────────────────────────

    private function generarVentasPOS($vendedores, $clientes, $productos): void
    {
        for ($i = 0; $i < 35; $i++) {
            $numProductos = rand(1, 4);
            $seleccion = $productos->random(min($numProductos, $productos->count()));

            $carrito = $seleccion
                ->filter(fn ($p) => $p->stock_actual >= 1)
                ->map(fn ($p) => ['id' => $p->id, 'cantidad' => min((int) $p->stock_actual, rand(1, 3))])
                ->values()
                ->all();

            if (empty($carrito)) {
                continue;
            }

            $datos = [
                'metodo_pago' => collect(MetodoPago::permitidosPOS())->random()->value,
                'productos'   => $carrito,
                'cliente_id'  => $clientes->isNotEmpty() && rand(1, 100) <= 40 ? $clientes->random()->id : null,
            ];

            try {
                $venta = $this->ventaService->procesarVentaPOS($datos, $vendedores->random());
                $this->moverAlPasado($venta, rand(1, 240));
            } catch (ValidationException $e) {
                // Stock insuficiente para la combinación elegida: se omite esta iteración.
                continue;
            }
        }
    }

    /**
     * Ventas dedicadas a ejercitar el descuento por mayor: usa productos con
     * cantidad_por_mayor definido y vende exactamente esa cantidad (o más).
     */
    private function generarVentasAlPorMayor($vendedores, $clientes, $productos): void
    {
        $productosPorMayor = $productos->whereNotNull('cantidad_por_mayor');

        if ($productosPorMayor->isEmpty()) {
            return;
        }

        foreach ($productosPorMayor->take(10) as $producto) {
            $cantidad = (int) $producto->cantidad_por_mayor + rand(0, 5);

            if ($producto->stock_actual < $cantidad) {
                continue;
            }

            $datos = [
                'metodo_pago' => MetodoPago::Efectivo->value,
                'productos'   => [['id' => $producto->id, 'cantidad' => $cantidad]],
                'cliente_id'  => $clientes->isNotEmpty() ? $clientes->random()->id : null,
            ];

            try {
                $venta = $this->ventaService->procesarVentaPOS($datos, $vendedores->random());
                $this->moverAlPasado($venta, rand(1, 200));
            } catch (ValidationException $e) {
                continue;
            }
        }
    }

    private function generarVentasOnline($clientes, $productos): void
    {
        if ($clientes->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 8; $i++) {
            $numProductos = rand(1, 3);
            $seleccion = $productos->random(min($numProductos, $productos->count()));

            $carrito = $seleccion
                ->filter(fn ($p) => $p->stock_actual >= 1)
                ->map(fn ($p) => ['id' => $p->id, 'cantidad' => min((int) $p->stock_actual, rand(1, 2))])
                ->values()
                ->all();

            if (empty($carrito)) {
                continue;
            }

            $datos = [
                'metodo_entrega' => collect(MetodoEntrega::cases())->random()->value,
                'productos'      => $carrito,
            ];

            try {
                $venta = $this->ventaService->procesarVentaOnline($datos, $clientes->random());
                $this->moverAlPasado($venta, rand(1, 90));
            } catch (ValidationException $e) {
                continue;
            }
        }
    }

    // ─── Pedidos de compra ────────────────────────────────────────────────────────

    private function generarPedidosCompra($administradores, $bodegueros, $proveedores): void
    {
        if ($proveedores->isEmpty() || $administradores->isEmpty()) {
            return;
        }

        // Objetivo de estado final por lote, para cubrir toda la máquina de estados.
        $lotes = [
            ['destino' => PedidoEstado::EnAprobacion,     'cantidad' => 4, 'creador' => 'bodeguero'],
            ['destino' => PedidoEstado::Emitido,           'cantidad' => 4, 'creador' => 'admin'],
            ['destino' => PedidoEstado::Aceptado,          'cantidad' => 4, 'creador' => 'admin'],
            ['destino' => PedidoEstado::EnPreparacion,     'cantidad' => 3, 'creador' => 'admin'],
            ['destino' => PedidoEstado::EnTransito,        'cantidad' => 3, 'creador' => 'admin'],
            ['destino' => PedidoEstado::RecibidoCompleto,  'cantidad' => 4, 'creador' => 'admin'],
            ['destino' => PedidoEstado::Pagado,            'cantidad' => 2, 'creador' => 'admin'],
            ['destino' => PedidoEstado::Cancelado,         'cantidad' => 2, 'creador' => 'bodeguero'],
        ];

        foreach ($lotes as $lote) {
            for ($i = 0; $i < $lote['cantidad']; $i++) {
                $proveedor = $proveedores->random();
                $catalogo = $proveedor->productoProveedor;

                if ($catalogo->isEmpty()) {
                    continue;
                }

                $creador = $lote['creador'] === 'bodeguero' && $bodegueros->isNotEmpty()
                    ? $bodegueros->random()
                    : $administradores->random();

                $productosPedido = $catalogo->random(min(rand(2, 5), $catalogo->count()));

                $datos = [
                    'proveedor_id' => $proveedor->id,
                    'productos'    => $productosPedido->map(fn ($p) => [
                        'id'              => $p->id,
                        'cantidad'        => rand(5, 30),
                        'precio_unitario' => $p->costo_unitario,
                    ])->values()->all(),
                ];

                try {
                    $pedido = $this->pedidoService->crearPedido($datos, $creador);
                    $this->avanzarPedidoHasta($pedido, $lote['destino'], $administradores, $bodegueros);
                    $this->moverAlPasado($pedido, rand(1, 200));
                } catch (ValidationException $e) {
                    continue;
                }
            }
        }
    }

    /**
     * Progresa un pedido a través de la máquina de estados hasta el destino
     * deseado, ejecutando cada transición con el servicio real (esto es lo
     * que efectivamente incrementa el stock al llegar a RecibidoCompleto).
     */
    private function avanzarPedidoHasta($pedido, PedidoEstado $destino, $administradores, $bodegueros): void
    {
        $admin = $administradores->random();
        $bodeguero = $bodegueros->isNotEmpty() ? $bodegueros->random() : $admin;

        if ($destino === PedidoEstado::Cancelado) {
            $this->pedidoService->cancelarPedido($pedido, $admin, 'Proveedor sin stock disponible / pedido duplicado.');
            return;
        }

        // Si quedó en 'en_aprobacion' (creado por bodeguero) y el destino va más allá, aprobar primero.
        if ($pedido->estado === PedidoEstado::EnAprobacion && $destino !== PedidoEstado::EnAprobacion) {
            $this->pedidoService->aprobarPedido($pedido, $admin);
            $pedido->refresh();
        }

        $secuencia = [
            PedidoEstado::Aceptado,
            PedidoEstado::EnPreparacion,
            PedidoEstado::EnTransito,
            PedidoEstado::RecibidoCompleto,
            PedidoEstado::Pagado,
        ];

        foreach ($secuencia as $estado) {
            if ($pedido->estado === $destino) {
                break;
            }

            $actor = $estado === PedidoEstado::Pagado ? $admin : $bodeguero;
            $this->pedidoService->cambiarEstado($pedido, $estado, $actor);
            $pedido->refresh();
        }
    }

    // ─── Utilidades ──────────────────────────────────────────────────────────────

    /**
     * Ajusta created_at/updated_at (del registro y sus detalles) hacia una
     * fecha pasada aleatoria, solo para variar los datos en los gráficos.
     * No afecta el stock ni ningún cálculo: eso ya se ejecutó correctamente
     * a través del servicio antes de llamar a este método.
     */
    private function moverAlPasado($modelo, int $diasAtras): void
    {
        $fecha = Carbon::now()->subDays($diasAtras);
        $tabla = $modelo->getTable();

        DB::table($tabla)->where('id', $modelo->id)->update([
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ]);

        $tablaDetalle = $tabla === 'ventas' ? 'detalle_ventas' : 'detalle_pedido_compras';
        $fk = $tabla === 'ventas' ? 'venta_id' : 'pedido_compra_id';

        DB::table($tablaDetalle)->where($fk, $modelo->id)->update([
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ]);
    }
}
