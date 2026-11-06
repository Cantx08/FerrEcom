<?php

namespace App\Http\Controllers;

use App\Enums\PedidoEstado;
use App\Http\Requests\StorePedidoCompraRequest;
use App\Http\Requests\UpdatePedidoEstadoRequest;
use App\Models\PedidoCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\ProductoProveedor;
use App\Services\PedidoCompraService;
use Illuminate\Http\Request;

class PedidoCompraController extends Controller
{
    public function __construct(
        private readonly PedidoCompraService $servicio
    ) {}

    public function index()
    {
        $query = PedidoCompra::with(['proveedor', 'bodeguero', 'detalles.productoCatalogo'])
            ->orderByDesc('created_at');

        // Proveedor: redirigir a su portal separado
        if (auth()->user()->hasRole('Proveedor')) {
            return redirect()->route('proveedor.pedidos.index');
        }

        $pedidos = $query->paginate(15);
        return view('admin.pedidos.index', compact('pedidos'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', PedidoCompra::class);

        $proveedores       = Proveedor::orderBy('nombre_compania')->get();
        $productos         = ProductoProveedor::all();
        $prefilledProducts = [];

        if ($request->has('low_stock') && $request->has('proveedor_id')) {
            $proveedorId      = $request->proveedor_id;
            $lowStockProducts = Producto::lowStock()->get();

            if ($lowStockProducts->count() > 0) {
                $nombres      = $lowStockProducts->pluck('nombre')->toArray();
                $provProductos = ProductoProveedor::where('proveedor_id', $proveedorId)
                    ->whereIn('nombre', $nombres)
                    ->get();

                foreach ($provProductos as $pp) {
                    $local           = $lowStockProducts->firstWhere('nombre', $pp->nombre);
                    $sugerida        = $local
                        ? max(1, ($local->stock_minimo - $local->stock_actual) + 5)
                        : 1;

                    $prefilledProducts[] = [
                        'id'      => $pp->id,
                        'cantidad' => $sugerida,
                        'precio'  => $pp->costo_unitario,
                    ];
                }
            }
        }

        return view('admin.pedidos.create', compact('proveedores', 'productos', 'prefilledProducts'));
    }

    public function store(StorePedidoCompraRequest $request)
    {
        try {
            $pedido = $this->servicio->crearPedido($request->validated(), auth()->user());
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Error al crear pedido de compra: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al generar el pedido.');
        }

        return redirect()
            ->route('pedidos.show', $pedido->id)
            ->with('success', 'Pedido de compra generado exitosamente.');
    }

    public function show($id)
    {
        $pedido = PedidoCompra::with([
            'proveedor',
            'bodeguero',
            'aprobadoPor',
            'canceladoPor',
            'detalles.productoCatalogo',

        ])->findOrFail($id);

        $this->authorize('view', $pedido);

        return view('admin.pedidos.show', compact('pedido'));
    }

    /**
     * Cambio de estado genérico (proveedor y bodeguero/admin).
     */
    public function updateStatus(UpdatePedidoEstadoRequest $request, $id)
    {
        $pedido      = PedidoCompra::with('detalles.productoCatalogo')->findOrFail($id);
        $nuevoEstado = PedidoEstado::from($request->validated('estado'));

        // Si el proveedor actualiza cantidades disponibles
        if ($nuevoEstado === PedidoEstado::Aceptado && $request->has('detalles')) {
            $this->servicio->registrarCantidadesDisponibles(
                $pedido,
                $request->input('detalles', []),
                $request->input('observaciones_proveedor')
            );
        }

        try {
            $this->servicio->cambiarEstado(
                $pedido,
                $nuevoEstado,
                auth()->user(),
                $request->validated('notas')
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error("Error al cambiar estado del pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el estado del pedido.');
        }

        return redirect()
            ->route('pedidos.show', $pedido->id)
            ->with('success', 'Estado del pedido actualizado a: ' . $nuevoEstado->etiqueta());
    }

    /**
     * Aprobación del pedido por el Administrador (en_aprobacion → emitido).
     */
    public function aprobar($id)
    {
        $pedido = PedidoCompra::findOrFail($id);
        $this->authorize('aprobar', $pedido);

        try {
            $this->servicio->aprobarPedido($pedido, auth()->user());
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error("Error al aprobar pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al aprobar el pedido.');
        }

        return redirect()
            ->route('pedidos.show', $pedido->id)
            ->with('success', 'Pedido aprobado y emitido al proveedor exitosamente.');
    }

    /**
     * Cancelación del pedido.
     */
    public function cancelar(Request $request, $id)
    {
        $pedido = PedidoCompra::findOrFail($id);
        $this->authorize('cancelar', $pedido);

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:500',
        ]);

        try {
            $this->servicio->cancelarPedido($pedido, auth()->user(), $request->motivo_cancelacion);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error("Error al cancelar pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cancelar el pedido.');
        }

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido cancelado exitosamente.');
    }

    /**
     * Registrar pago (solo Administrador, solo efectivo).
     */
    public function pagar($id)
    {
        $pedido = PedidoCompra::findOrFail($id);
        $this->authorize('pagar', $pedido);

        try {
            $this->servicio->cambiarEstado(
                $pedido,
                PedidoEstado::Pagado,
                auth()->user(),
                'Pago registrado en efectivo.'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error("Error al registrar pago del pedido #{$pedido->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al registrar el pago.');
        }

        return redirect()
            ->route('pedidos.show', $pedido->id)
            ->with('success', 'Pedido marcado como pagado exitosamente.');
    }
}
