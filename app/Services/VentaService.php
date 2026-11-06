<?php

namespace App\Services;

use App\Enums\CanalVenta;
use App\Enums\MetodoEntrega;
use App\Enums\MetodoPago;
use App\Enums\VentaEstado;
use App\Events\VentaEstadoCambiada;
use App\Models\DetalleVenta;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Notifications\BajoStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class VentaService
{
    /**
     * Procesa una venta POS.
     * Canal: pos | Métodos permitidos: efectivo, transferencia | Estado final: entregado
     */
    public function procesarVentaPOS(array $datos, User $vendedor): Venta
    {
        $metodoPago = MetodoPago::from($datos['metodo_pago']);

        if (!in_array($metodoPago, MetodoPago::permitidosPOS(), strict: true)) {
            throw ValidationException::withMessages([
                'metodo_pago' => 'El canal POS solo acepta efectivo o transferencia bancaria.',
            ]);
        }

        return DB::transaction(function () use ($datos, $vendedor, $metodoPago) {
            $detalles = $this->prepararDetalles($datos['productos']);

            $venta = Venta::create([
                'vendedor_id'  => $vendedor->id,
                'cliente_id'   => $datos['cliente_id'] ?? null,
                'canal'        => CanalVenta::POS->value,
                'metodo_pago'  => $metodoPago->value,
                'estado'       => VentaEstado::Entregado->value,
                'total'        => $detalles['total'],
                'descuento'    => $detalles['descuento'],
            ]);

            $this->guardarDetalles($venta, $detalles['items']);

            if (!empty($datos['factura'])) {
                $this->crearFactura($venta, $datos['factura']);
            }

            return $venta;
        });
    }

    /**
     * Procesa una venta Online.
     * Canal: online | Método: tarjeta | Estado inicial: pagado
     * Genera tracking_code único.
     */
    public function procesarVentaOnline(array $datos, User $cliente): Venta
    {
        // Solo clientes pueden comprar online
        if (!$cliente->hasRole('Cliente')) {
            throw ValidationException::withMessages([
                'cliente' => 'Solo los clientes registrados pueden realizar compras en línea.',
            ]);
        }

        $metodoPago = MetodoPago::from($datos['metodo_pago'] ?? MetodoPago::Tarjeta->value);

        if (!in_array($metodoPago, MetodoPago::permitidosOnline(), strict: true)) {
            throw ValidationException::withMessages([
                'metodo_pago' => 'Las compras en línea solo aceptan pago con tarjeta.',
            ]);
        }

        $metodoEntrega = MetodoEntrega::from($datos['metodo_entrega'] ?? MetodoEntrega::RetiroEnCaja->value);

        return DB::transaction(function () use ($datos, $cliente, $metodoPago, $metodoEntrega) {
            $detalles      = $this->prepararDetalles($datos['productos']);
            $trackingCode  = Venta::generarTrackingCode();

            $venta = Venta::create([
                'cliente_id'      => $cliente->id,
                'canal'           => CanalVenta::Online->value,
                'metodo_pago'     => $metodoPago->value,
                'metodo_entrega'  => $metodoEntrega->value,
                'estado'          => VentaEstado::Pagado->value,
                'tracking_code'   => $trackingCode,
                'codigo_retiro'   => $trackingCode,  // compatibilidad con campo existente
                'total'           => $detalles['total'],
                'descuento'       => $detalles['descuento'],
            ]);

            $this->guardarDetalles($venta, $detalles['items']);

            if (!empty($datos['factura'])) {
                $this->crearFactura($venta, $datos['factura']);
            }

            // Disparar evento de estado 'pagado' (notificación al cliente)
            VentaEstadoCambiada::dispatch(
                $venta,
                null,
                VentaEstado::Pagado,
                $cliente,
                'Pago en línea procesado exitosamente.'
            );

            return $venta;
        });
    }

    private function crearFactura(Venta $venta, array $datosFactura): void
    {
        Factura::create([
            'venta_id'     => $venta->id,
            'ruc_cedula'   => $datosFactura['ruc_cedula'] ?? '9999999999',
            'razon_social' => $datosFactura['razon_social'] ?? 'Consumidor Final',
            'direccion'    => $datosFactura['direccion'] ?? 'S/N',
            'telefono'     => $datosFactura['telefono'] ?? '',
            'correo'       => $datosFactura['correo'] ?? '',
        ]);
    }

    /**
     * Avanza el estado de una venta online siguiendo la máquina de estados.
     */
    public function avanzarEstado(Venta $venta, VentaEstado $nuevoEstado, User $actor, ?string $notas = null): void
    {
        if (!$venta->puedeTransicionarA($nuevoEstado)) {
            throw ValidationException::withMessages([
                'estado' => sprintf(
                    'No se puede pasar de "%s" a "%s".',
                    $venta->estado->etiqueta(),
                    $nuevoEstado->etiqueta()
                ),
            ]);
        }

        DB::transaction(function () use ($venta, $nuevoEstado, $actor, $notas) {
            $estadoAnterior = $venta->estado;
            $venta->update(['estado' => $nuevoEstado->value]);
            $venta->refresh();

            VentaEstadoCambiada::dispatch($venta, $estadoAnterior, $nuevoEstado, $actor, $notas);
        });
    }

    /**
     * Valida un código de retiro/tracking y marca la venta como 'entregado'.
     * Usado en la ruta de retiro en caja.
     */
    public function validarRetiro(string $trackingCode, User $cajero): Venta
    {
        $venta = Venta::where(function ($q) use ($trackingCode) {
            $q->where('tracking_code', $trackingCode)
              ->orWhere('codigo_retiro', $trackingCode);
        })->where('canal', CanalVenta::Online->value)->first();

        if (!$venta) {
            throw ValidationException::withMessages([
                'tracking_code' => 'Código de retiro inválido o no encontrado.',
            ]);
        }

        if ($venta->estado === VentaEstado::Entregado) {
            throw ValidationException::withMessages([
                'tracking_code' => 'Este pedido ya fue entregado anteriormente.',
            ]);
        }

        if (!$venta->puedeTransicionarA(VentaEstado::Entregado)) {
            throw ValidationException::withMessages([
                'tracking_code' => 'El pedido no está listo para entrega (estado actual: ' . $venta->estado->etiqueta() . ').',
            ]);
        }

        $this->avanzarEstado($venta, VentaEstado::Entregado, $cajero, 'Retirado en caja.');

        return $venta->fresh();
    }

    // ─── Métodos privados ────────────────────────────────────────────────────────

    private function prepararDetalles(array $productosInput): array
    {
        $items       = [];
        $total       = 0;
        $descuento   = 0;

        foreach ($productosInput as $item) {
            $producto = Producto::findOrFail($item['id']);

            if ($producto->stock_actual < $item['cantidad']) {
                throw ValidationException::withMessages([
                    'productos' => "Stock insuficiente para {$producto->nombre}.",
                ]);
            }

            $precioUnitario   = $producto->precio_venta;
            $descuentoUnitario = 0;

            // Descuento por mayor
            if (
                $producto->cantidad_por_mayor &&
                $item['cantidad'] >= $producto->cantidad_por_mayor &&
                $producto->descuento_por_mayor
            ) {
                $descuentoUnitario = $precioUnitario * ($producto->descuento_por_mayor / 100);
                $precioUnitario   -= $descuentoUnitario;
            }

            $subtotal   = $precioUnitario * $item['cantidad'];
            $total     += $subtotal;
            $descuento += $descuentoUnitario * $item['cantidad'];

            $items[] = [
                'producto'    => $producto,
                'cantidad'    => $item['cantidad'],
                'precio'      => $precioUnitario,
                'subtotal'    => $subtotal,
            ];
        }

        return compact('items', 'total', 'descuento');
    }

    private function guardarDetalles(Venta $venta, array $items): void
    {
        foreach ($items as $item) {
            DetalleVenta::create([
                'venta_id'    => $venta->id,
                'producto_id' => $item['producto']->id,
                'cantidad'    => $item['cantidad'],
                'precio'      => $item['precio'],
            ]);

            $oldStock = $item['producto']->stock_actual;
            $item['producto']->decrement('stock_actual', $item['cantidad']);

            // Alerta de bajo stock
            if ($oldStock > $item['producto']->stock_minimo &&
                $item['producto']->fresh()->stock_actual <= $item['producto']->stock_minimo
            ) {
                $admins = User::whereHas('roles', fn($q) =>
                    $q->whereIn('name', ['Administrador', 'Bodeguero', 'SuperAdmin'])
                )->get();

                Notification::send($admins, new BajoStockNotification($item['producto']));
            }
        }
    }
}
