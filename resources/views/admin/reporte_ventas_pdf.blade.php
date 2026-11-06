<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Estadístico</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0369a1; padding-bottom: 10px; }
        .header h1 { color: #0369a1; margin: 0 0 10px 0; font-size: 24px; }
        .header p { margin: 0; color: #64748b; }
        .summary-box { width: 31%; display: inline-block; padding: 15px; margin-right: 1%; box-sizing: border-box; border-radius: 5px; text-align: center; }
        .summary-box.green { background-color: #dcfce7; border: 1px solid #bbf7d0; color: #166534; }
        .summary-box.red { background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }
        .summary-box.blue { background-color: #e0f2fe; border: 1px solid #bae6fd; color: #075985; }
        .summary-box h3 { margin: 0 0 5px 0; font-size: 14px; text-transform: uppercase; }
        .summary-box p.amount { margin: 0; font-size: 20px; font-weight: bold; }
        .summary-box p.count { margin: 5px 0 0 0; font-size: 12px; }
        .section-title { margin-top: 30px; margin-bottom: 15px; font-size: 18px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FerreMax - Reporte Estadístico</h1>
        <p>Periodo: {{ $start->format('d/m/Y') }} al {{ $end->format('d/m/Y') }} (Filtro: {{ ucfirst($filtro) }})</p>
    </div>

    <div>
        <div class="summary-box green">
            <h3>Ingresos (Ventas)</h3>
            <p class="amount">${{ number_format($totalVentas, 2) }}</p>
            <p class="count">{{ $ventasCount }} transacciones</p>
        </div>
        <div class="summary-box red">
            <h3>Egresos (Compras)</h3>
            <p class="amount">${{ number_format($totalCompras, 2) }}</p>
            <p class="count">{{ $comprasCount }} pedidos aprobados</p>
        </div>
        <div class="summary-box blue">
            <h3>Balance</h3>
            <p class="amount">${{ number_format($balance, 2) }}</p>
            <p class="count">{{ $balance >= 0 ? 'Positivo' : 'Negativo' }}</p>
        </div>
    </div>

    <h2 class="section-title">Top Productos Vendidos</h2>
    @if($productosMasVendidos->isEmpty())
        <p>No hay ventas en este periodo.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th class="text-right">Cantidad Vendida</th>
                    <th class="text-right">Total Generado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productosMasVendidos as $prod)
                    <tr>
                        <td>{{ $prod->producto->nombre }}</td>
                        <td>{{ $prod->producto->sku }}</td>
                        <td class="text-right">{{ $prod->total_vendido }} ud.</td>
                        <td class="text-right">${{ number_format($prod->total_vendido * $prod->producto->precio_venta, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
