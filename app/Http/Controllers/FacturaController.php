<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function generarFactura(Request $request, $ventaId)
    {
        $venta = Venta::with(['cliente.profile', 'detalles.producto', 'factura'])->findOrFail($ventaId);

        // Validar que la venta pertenece al cliente (o es admin)
        if (auth()->user()->id !== $venta->cliente_id && !auth()->user()->hasAnyRole(['SuperAdmin', 'Administrador', 'Vendedor'])) {
            abort(403, 'No autorizado');
        }

        // Leer la imagen en base64 para DomPDF (ya que imagenes locales a veces no cargan por permisos)
        $path = public_path('logo.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        // No todas las ventas tienen un registro de factura con datos de facturación
        // (es opcional al momento de la compra), así que usamos datos del cliente
        // como respaldo para no romper la generación del PDF.
        $factura = $venta->factura;
        $profile = $venta->cliente?->profile;

        if (!$factura) {
            $factura = new \App\Models\Factura([
                'razon_social' => $profile
                    ? trim($profile->nombres . ' ' . $profile->apellidos)
                    : ($venta->cliente?->name ?? 'Consumidor Final'),
                'ruc_cedula' => $profile?->identificacion ?? '9999999999',
                'direccion' => $profile?->direccion ?? 'S/N',
                'telefono' => $profile?->telefono ?? '',
                'correo' => $venta->cliente?->email ?? '',
            ]);
        }

        $data = [
            'venta' => $venta,
            'factura' => $factura,
            'logo' => $logoBase64,
            'fecha' => now()->format('d/m/Y H:i')
        ];

        $pdf = Pdf::loadView('factura.pdf', $data);

        return $pdf->stream('factura_ferremax_' . str_pad($venta->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}
