<?php

namespace App\Http\Controllers;

use App\Enums\PedidoEstado;
use App\Models\PedidoCompra;
use App\Models\ProductoProveedor;
use App\Models\Proveedor;
use App\Services\PedidoCompraService;
use Illuminate\Http\Request;

class ProductoProveedorController extends Controller
{
    public function __construct(
        private readonly PedidoCompraService $servicio
    ) {
    }

    /**
     * Obtiene el proveedor vinculado al usuario autenticado.
     */
    private function miProveedor(): Proveedor
    {
        $proveedor = Proveedor::where('user_id', auth()->id())->first();

        if (!$proveedor) {
            abort(403, 'Tu cuenta no está vinculada a ningún proveedor.');
        }

        return $proveedor;
    }

    // ─── Pedidos asignados al proveedor ──────────────────────────────────────────

    public function pedidosIndex()
    {
        $proveedor = $this->miProveedor();

        $pedidos = PedidoCompra::with(['bodeguero', 'detalles.productoCatalogo'])
            ->where('proveedor_id', $proveedor->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('proveedor.pedidos.index', compact('pedidos', 'proveedor'));
    }

    public function pedidosShow($id)
    {
        $proveedor = $this->miProveedor();

        $pedido = PedidoCompra::with([
            'bodeguero',
            'detalles.productoCatalogo',

        ])->where('proveedor_id', $proveedor->id)->findOrFail($id);

        // Estados que el proveedor puede aplicar desde el estado actual
        $transicionesDisponibles = $pedido->estado->transicionesPermitidas();
        $estadosProveedor = [
            PedidoEstado::Aceptado,
            PedidoEstado::EnPreparacion,
            PedidoEstado::EnTransito,
        ];

        $accionesDisponibles = array_filter(
            $transicionesDisponibles,
            fn($e) => in_array($e, $estadosProveedor, strict: true)
        );

        return view('proveedor.pedidos.show', compact('pedido', 'proveedor', 'accionesDisponibles'));
    }

    /**
     * El proveedor cambia el estado logístico del pedido.
     * Solo puede transicionar a: aceptado, en_preparacion, en_transito.
     */
    public function pedidosUpdateStatus(Request $request, $id)
    {
        $proveedor = $this->miProveedor();

        $pedido = PedidoCompra::where('proveedor_id', $proveedor->id)->findOrFail($id);

        $request->validate([
            'estado' => 'required|string|in:aceptado,en_preparacion,en_transito',
            'notas'  => 'nullable|string|max:500',
            'observaciones_proveedor' => 'nullable|string|max:1000',

            // Cantidades disponibles cuando acepta el pedido
            'detalles'                              => 'nullable|array',
            'detalles.*.cantidad_disponible'        => 'nullable|numeric|min:0',
        ]);

        $nuevoEstado = PedidoEstado::from($request->estado);

        // Validar que la transición sea válida para el proveedor
        $estadosPermitidos = [PedidoEstado::Aceptado, PedidoEstado::EnPreparacion, PedidoEstado::EnTransito];

        if (!in_array($nuevoEstado, $estadosPermitidos, strict: true)) {
            return redirect()->back()->with('error', 'Acción no permitida para proveedores.');
        }

        // Si acepta, actualizar cantidades disponibles
        if ($nuevoEstado === PedidoEstado::Aceptado && $request->has('detalles')) {
            $this->servicio->registrarCantidadesDisponibles(
                $pedido,
                $request->input('detalles', []),
                $request->input('observaciones_proveedor')
            );
        }

        $this->servicio->cambiarEstado($pedido, $nuevoEstado, auth()->user(), $request->notas);

        return redirect()
            ->route('proveedor.pedidos.show', $pedido->id)
            ->with('success', 'Pedido ' . $nuevoEstado->etiqueta());
    }

    // ─── Catálogo del proveedor ───────────────────────────────────────────────────

    public function catalogoIndex()
    {
        $proveedor = $this->miProveedor();

        $productos = ProductoProveedor::where('proveedor_id', $proveedor->id)
            ->orderBy('nombre')
            ->paginate(20);

        return view('proveedor.catalogo.index', compact('productos', 'proveedor'));
    }

    public function catalogoCreate()
    {
        $proveedor = $this->miProveedor();
        return view('proveedor.catalogo.form', compact('proveedor'));
    }

    public function catalogoStore(Request $request)
    {
        $proveedor = $this->miProveedor();

        $validated = $request->validate([
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'costo_unitario'  => 'required|numeric|min:0',
            'unidad'          => 'nullable|string|max:30',
        ]);

        ProductoProveedor::create([
            ...$validated,
            'proveedor_id' => $proveedor->id,
        ]);

        return redirect()
            ->route('proveedor.catalogo.index')
            ->with('success', 'Producto agregado al catálogo exitosamente.');
    }

    public function catalogoEdit(ProductoProveedor $producto)
    {
        $proveedor = $this->miProveedor();

        if ($producto->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        return view('proveedor.catalogo.form', compact('producto', 'proveedor'));
    }

    public function catalogoUpdate(Request $request, ProductoProveedor $producto)
    {
        $proveedor = $this->miProveedor();

        if ($producto->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string',
            'costo_unitario' => 'required|numeric|min:0',
            'unidad'         => 'nullable|string|max:30',
        ]);

        $producto->update($validated);

        return redirect()
            ->route('proveedor.catalogo.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function catalogoDestroy(ProductoProveedor $producto)
    {
        $proveedor = $this->miProveedor();

        if ($producto->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        $producto->delete();

        return redirect()
            ->route('proveedor.catalogo.index')
            ->with('success', 'Producto eliminado del catálogo.');
    }
}
