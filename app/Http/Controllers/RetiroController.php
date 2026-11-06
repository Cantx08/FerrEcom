<?php

namespace App\Http\Controllers;

use App\Services\VentaService;
use Illuminate\Http\Request;

class RetiroController extends Controller
{
    public function __construct(
        private readonly VentaService $servicio
    ) {}

    public function index()
    {
        return view('admin.retiros.index');
    }

    public function validar(Request $request)
    {
        $request->validate([
            'codigo_retiro' => 'required|string',
        ]);

        try {
            $venta = $this->servicio->validarRetiro(
                $request->codigo_retiro,
                auth()->user()
            );

            return redirect()->back()->with(
                'success',
                "Pedido #{$venta->id} verificado y marcado como entregado exitosamente."
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = implode(' ', $e->errors()[array_key_first($e->errors())]);
            return redirect()->back()->with('error', $error);
        }
    }
}
