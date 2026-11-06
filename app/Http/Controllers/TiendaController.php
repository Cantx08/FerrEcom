<?php

namespace App\Http\Controllers;

use App\Enums\MetodoEntrega;
use App\Enums\MetodoPago;
use App\Http\Requests\StoreOnlineVentaRequest;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Anuncio;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiendaController extends Controller
{
    public function __construct(
        private readonly VentaService $servicio
    ) {}

    public function index(Request $request)
    {
        $categorias = Categoria::all();
        $q   = $request->query('q');
        $cat = $request->query('cat', 'Destacados');

        $query = Producto::with('categoria')->where('estado', true);

        if ($q) {
            $query->where('nombre', 'LIKE', "%{$q}%");
        } else {
            if ($cat === 'Destacados') {
                $query->where('stock_actual', '>', 0)->inRandomOrder()->limit(12);
            } elseif ($cat === 'Ofertas') {
                $query->where('precio_venta', '<', 50)->inRandomOrder()->limit(8);
            } else {
                $query->whereHas('categoria', fn($c) => $c->where('nombre', $cat));
            }
        }

        $productos = $query->get();
        $anuncios  = Anuncio::where('estado', 'aprobado')->get();

        return view('tienda.index', compact('productos', 'categorias', 'cat', 'q', 'anuncios'));
    }

    public function checkoutView()
    {
        $this->authorize('crearOnline', Venta::class);

        $user       = Auth::user();
        $profile    = $user->profile;
        $metodos_entrega = MetodoEntrega::cases();

        return view('tienda.checkout', compact('user', 'profile', 'metodos_entrega'));
    }

    public function checkoutProcess(StoreOnlineVentaRequest $request)
    {
        $user = Auth::user();

        $cartData = $request->input('cart_data');
        $cart     = json_decode($cartData, true);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'El carrito está vacío.');
        }

        // Construir datos para el servicio
        $productos = collect($cart)->map(fn($item) => [
            'id'       => $item['id'],
            'cantidad' => (int) $item['qty'],
        ])->toArray();

        $datos = [
            'metodo_pago'    => MetodoPago::Tarjeta->value,
            'metodo_entrega' => $request->input('metodo_entrega', MetodoEntrega::RetiroEnCaja->value),
            'productos'      => $productos,
            'factura'        => [
                'razon_social' => $request->input('razon_social'),
                'ruc_cedula'   => $request->input('ruc_cedula'),
                'direccion'    => $request->input('direccion'),
                'telefono'     => $request->input('telefono'),
                'correo'       => $request->input('correo'),
            ],
        ];

        try {
            $venta = $this->servicio->procesarVentaOnline($datos, $user);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', implode(' ', $e->errors()['productos'] ?? $e->errors()[array_key_first($e->errors())]));
        } catch (\Exception $e) {
            \Log::error('Error en checkout online: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al procesar tu compra.');
        }

        return redirect()
            ->route('cliente.compras')
            ->with('success', 'Pago aprobado. Tu código de rastreo es: ' . $venta->tracking_code);
    }

    public function compras()
    {
        $this->authorize('crearOnline', Venta::class);

        $user = Auth::user();

        $ventas = Venta::where('cliente_id', $user->id)
            ->with(['detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tienda.mis-compras', compact('ventas'));
    }
}
