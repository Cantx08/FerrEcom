<?php

namespace App\Services;

use App\Enums\PedidoEstado;
use App\Enums\MetodoPago;
use App\Events\PedidoEstadoCambiado;
use App\Models\PedidoCompra;
use App\Models\DetallePedidoCompra;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PedidoCompraService
{
    /**
     * Crea un nuevo pedido.
     * - Si el creador es Bodeguero → estado 'en_aprobacion' (requiere visto bueno del Dueño).
     * - Si el creador es Administrador → estado 'emitido' (ya tiene autorización implícita).
     */
    public function crearPedido(array $datos, User $creador): PedidoCompra
    {
        return DB::transaction(function () use ($datos, $creador) {
            $esBodeguero = $creador->hasRole('Bodeguero');
            $estadoInicial = $esBodeguero
                ? PedidoEstado::EnAprobacion
                : PedidoEstado::Emitido;

            $total = 0;
            foreach ($datos['productos'] as $prod) {
                $total += $prod['cantidad'] * $prod['precio_unitario'];
            }

            $pedido = PedidoCompra::create([
                'proveedor_id' => $datos['proveedor_id'],
                'bodeguero_id' => $creador->id,
                'total'        => $total,
                'estado'       => $estadoInicial->value,
                'metodo_pago'  => MetodoPago::Efectivo->value,
            ]);

            foreach ($datos['productos'] as $prod) {
                DetallePedidoCompra::create([
                    'pedido_compra_id'    => $pedido->id,
                    'producto_catalogo_id' => $prod['id'],
                    'cantidad_solicitada'  => $prod['cantidad'],
                    'precio'              => $prod['precio_unitario'],
                ]);
            }

            // Disparar evento con estado inicial (null → estado inicial)
            PedidoEstadoCambiado::dispatch(
                $pedido,
                $estadoInicial, // anterior ficticio = el mismo para creación
                $estadoInicial,
                $creador,
                $esBodeguero
                    ? 'Pedido creado por bodeguero, pendiente de aprobación.'
                    : 'Pedido emitido directamente por el administrador.'
            );

            return $pedido;
        });
    }

    /**
     * El Administrador aprueba un pedido que está en 'en_aprobacion'.
     * Lo mueve a 'emitido' y notifica.
     */
    public function aprobarPedido(PedidoCompra $pedido, User $aprobador): void
    {
        $this->validarTransicion($pedido, PedidoEstado::Emitido);

        DB::transaction(function () use ($pedido, $aprobador) {
            $estadoAnterior = $pedido->estado;

            $pedido->update([
                'estado'       => PedidoEstado::Emitido->value,
                'aprobado_por' => $aprobador->id,
            ]);
            $pedido->refresh();

            PedidoEstadoCambiado::dispatch(
                $pedido,
                $estadoAnterior,
                PedidoEstado::Emitido,
                $aprobador,
                'Pedido aprobado y emitido al proveedor.'
            );
        });
    }

    /**
     * Cambia el estado del pedido validando la máquina de estados.
     * Usado por el Proveedor (aceptado, en_preparacion, en_transito)
     * y por el Bodeguero/Admin (recibido_*, pagado).
     */
    public function cambiarEstado(
        PedidoCompra $pedido,
        PedidoEstado $nuevoEstado,
        User $actor,
        ?string $notas = null
    ): void {
        $this->validarTransicion($pedido, $nuevoEstado);

        // El pago SOLO puede ser en efectivo (ya está en el modelo, validación extra)
        if ($nuevoEstado === PedidoEstado::Pagado) {
            if ($pedido->metodo_pago !== MetodoPago::Efectivo) {
                throw ValidationException::withMessages([
                    'metodo_pago' => 'Los pedidos a proveedor solo pueden pagarse en efectivo.',
                ]);
            }
        }

        DB::transaction(function () use ($pedido, $nuevoEstado, $actor, $notas) {
            $estadoAnterior = $pedido->estado;

            $pedido->update(['estado' => $nuevoEstado->value]);
            $pedido->refresh();

            // Si se recibe completamente, actualizar stock
            if ($nuevoEstado === PedidoEstado::RecibidoCompleto) {
                $this->actualizarStock($pedido);
            }

            PedidoEstadoCambiado::dispatch($pedido, $estadoAnterior, $nuevoEstado, $actor, $notas);
        });
    }

    /**
     * Cancela un pedido. Posible en cualquier estado anterior a la recepción
     * de mercadería o al pago (ver PedidoEstado::estadosCancelables()).
     */
    public function cancelarPedido(PedidoCompra $pedido, User $actor, string $motivo): void
    {
        if (!$pedido->esCancelable()) {
            throw ValidationException::withMessages([
                'estado' => 'El pedido no puede cancelarse en su estado actual (' . $pedido->estado->etiqueta() . ').',
            ]);
        }

        DB::transaction(function () use ($pedido, $actor, $motivo) {
            $estadoAnterior = $pedido->estado;

            $pedido->update([
                'estado'              => PedidoEstado::Cancelado->value,
                'cancelado_por'       => $actor->id,
                'motivo_cancelacion'  => $motivo,
            ]);
            $pedido->refresh();

            PedidoEstadoCambiado::dispatch(
                $pedido,
                $estadoAnterior,
                PedidoEstado::Cancelado,
                $actor,
                "Cancelado por: {$motivo}"
            );
        });
    }

    /**
     * Registra las cantidades disponibles que el proveedor confirma al aceptar
     * un pedido, y sus observaciones. Usado tanto desde el panel admin como
     * desde el portal del proveedor.
     */
    public function registrarCantidadesDisponibles(PedidoCompra $pedido, array $detallesInput, ?string $observaciones): void
    {
        foreach ($pedido->detalles as $detalle) {
            $cantidadDisponible = $detallesInput[$detalle->id]['cantidad_disponible'] ?? null;
            if (!is_null($cantidadDisponible)) {
                $detalle->update(['cantidad_disponible' => $cantidadDisponible]);
            }
        }

        if (filled($observaciones)) {
            $pedido->update(['observaciones_proveedor' => $observaciones]);
        }
    }

    // ─── Métodos privados ────────────────────────────────────────────────────────

    private function validarTransicion(PedidoCompra $pedido, PedidoEstado $nuevoEstado): void
    {
        if (!$pedido->puedeTransicionarA($nuevoEstado)) {
            throw ValidationException::withMessages([
                'estado' => sprintf(
                    'No se puede pasar de "%s" a "%s".',
                    $pedido->estado->etiqueta(),
                    $nuevoEstado->etiqueta()
                ),
            ]);
        }
    }

    private function actualizarStock(PedidoCompra $pedido): void
    {
        foreach ($pedido->detalles as $detalle) {
            $productoLocal = \App\Models\Producto::where('nombre', $detalle->productoCatalogo->nombre)->first();
            if ($productoLocal) {
                $productoLocal->increment(
                    'stock_actual',
                    $detalle->cantidad_disponible ?? $detalle->cantidad_solicitada
                );
            }
        }
    }
}
