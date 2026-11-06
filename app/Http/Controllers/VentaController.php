<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosVentaRequest;
use App\Models\Profile;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\VentaService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function __construct(
        private readonly VentaService $servicio
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Venta::class);

        if ($request->ajax()) {
            $query = Venta::with(['cliente', 'vendedor'])->select('ventas.*');

            return DataTables::of($query)
                ->editColumn('created_at', fn($v) => $v->created_at->format('d/m/Y H:i'))
                ->editColumn('total', fn($v) => '$' . number_format($v->total, 2))
                ->editColumn('estado', fn($v) => $v->estado->etiqueta())
                ->editColumn('canal', fn($v) => $v->canal->etiqueta())
                ->addColumn('cliente_nombre', fn($v) => $v->cliente?->name ?? 'Consumidor Final')
                ->addColumn('vendedor_nombre', fn($v) => $v->vendedor?->name ?? 'N/A')
                ->make(true);
        }

        $ventas = Venta::with(['vendedor', 'cliente'])->orderByDesc('created_at')->paginate(15);
        return view('admin.ventas.index', compact('ventas'));
    }

    public function create()
    {
        $this->authorize('crearPOS', Venta::class);

        $productos = Producto::where('estado', true)->where('stock_actual', '>', 0)->orderBy('nombre')->get();
        return view('admin.ventas.create', compact('productos'));
    }

    public function store(StorePosVentaRequest $request)
    {
        $datos = $request->validated();

        // Resolver cliente por cédula si se proporcionó
        $clienteId = null;
        if (!empty($datos['cliente_documento'])) {
            $perfil = Profile::where('identificacion', $datos['cliente_documento'])->first();
            if ($perfil) {
                $clienteId = $perfil->user_id;
                $perfil->update([
                    'nombres'   => $datos['cliente_nombre'] ?? $perfil->nombres,
                    'direccion' => $datos['cliente_direccion'] ?? $perfil->direccion,
                    'telefono'  => $datos['cliente_telefono'] ?? $perfil->telefono,
                    'email'     => $datos['cliente_correo'] ?? $perfil->email,
                ]);
            }
        }

        $datos['cliente_id'] = $clienteId;
        $datos['factura'] = [
            'ruc_cedula'   => $datos['cliente_documento'] ?? '9999999999',
            'razon_social' => $datos['cliente_nombre'] ?? 'Consumidor Final',
            'direccion'    => $datos['cliente_direccion'] ?? 'S/N',
            'telefono'     => $datos['cliente_telefono'] ?? '',
            'correo'       => $datos['cliente_correo'] ?? '',
        ];

        $venta = $this->servicio->procesarVentaPOS($datos, auth()->user());

        return redirect()
            ->route('ventas.index')
            ->with('success', 'Venta POS registrada exitosamente.');
    }

    public function show(Venta $venta)
    {
        $this->authorize('viewAny', Venta::class);

        $venta->load(['vendedor', 'cliente', 'detalles.producto', 'factura', 'historial.usuario']);

        return view('admin.ventas.show', compact('venta'));
    }

    public function buscarCliente($cedula)
    {
        $perfil = Profile::where('identificacion', $cedula)->first();

        if ($perfil) {
            return response()->json([
                'encontrado' => true,
                'datos' => [
                    'nombre'   => trim($perfil->nombres . ' ' . $perfil->apellidos),
                    'direccion' => $perfil->direccion,
                    'telefono' => $perfil->telefono,
                    'correo'   => $perfil->email,
                ],
            ]);
        }

        return response()->json(['encontrado' => false]);
    }
}
