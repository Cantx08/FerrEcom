<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; margin-bottom: 30px; }
        .header table { width: 100%; }
        .logo { width: 150px; }
        .company-details { text-align: right; }
        .company-details h2 { margin: 0; color: #ea580c; }
        .invoice-details { margin-bottom: 30px; border-top: 2px solid #ea580c; padding-top: 15px; }
        .invoice-details table { width: 100%; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background-color: #0c4a6e; color: white; padding: 10px; text-align: left; }
        .items-table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td { padding: 8px; text-align: right; }
        .totals-table .total-row { font-weight: bold; font-size: 16px; border-top: 2px solid #0c4a6e; color: #0c4a6e; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td style="width: 50%;">
                    <img src="{{ $logo }}" class="logo" alt="FerreMax Logo">
                </td>
                <td class="company-details">
                    <h2>FerreMax S.A.</h2>
                    <p>Av. Juan Tanca Marengo, Guayaquil<br>
                    RUC: 0990000000001<br>
                    Tel: (04) 380-2200<br>
                    ventas@ferremax.ec</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td style="width: 50%;">
                    <strong>Facturar a:</strong><br>
                    {{ $factura->razon_social }}<br>
                    RUC/Cédula: {{ $factura->ruc_cedula }}<br>
                    Dirección: {{ $factura->direccion }}<br>
                    Teléfono: {{ $factura->telefono }}<br>
                    Email: {{ $factura->correo }}<br>
                </td>
                <td style="text-align: right;">
                    <strong>Factura N°:</strong> {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}<br>
                    <strong>Fecha:</strong> {{ $fecha }}<br>
                    <strong>Método de pago:</strong> {{ ucfirst($venta->metodo_pago->value ?? $venta->metodo_pago) }}
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th style="text-align: center;">Cant.</th>
                <th style="text-align: right;">P. Unitario</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotal = 0;
            @endphp
            @foreach($venta->detalles as $detalle)
            @php
                $lineTotal = $detalle->cantidad * $detalle->precio;
                $subtotal += $lineTotal;
            @endphp
            <tr>
                <td>{{ $detalle->producto->nombre }}</td>
                <td style="text-align: center;">{{ $detalle->cantidad }}</td>
                <td style="text-align: right;">${{ number_format($detalle->precio, 2) }}</td>
                <td style="text-align: right;">${{ number_format($lineTotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        @php
            $envio = ($subtotal >= 50 || $subtotal == 0) ? 0 : 4.50;
            $iva = $subtotal * 0.15;
            $totalFinal = $subtotal + $envio + $iva;
        @endphp
        <tr>
            <td style="width: 70%;"></td>
            <td>Subtotal:</td>
            <td>${{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="width: 70%;"></td>
            <td>Envío:</td>
            <td>${{ number_format($envio, 2) }}</td>
        </tr>
        <tr>
            <td style="width: 70%;"></td>
            <td>IVA (15%):</td>
            <td>${{ number_format($iva, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td style="width: 70%;"></td>
            <td>Total:</td>
            <td>${{ number_format($totalFinal, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Gracias por su compra. Documento generado electrónicamente sin valor tributario directo.</p>
    </div>

</body>
</html>
