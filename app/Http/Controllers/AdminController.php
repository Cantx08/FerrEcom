<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\PedidoCompra;
use App\Models\Producto;
use App\Models\Anuncio;
use App\Models\DetalleVenta;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    private function getPeriodDates($period)
    {
        if ($period === 'hoy') {
            return [now()->startOfDay(), now()->endOfDay(), now()->subDay()->startOfDay(), now()->subDay()->endOfDay()];
        } elseif ($period === 'semana') {
            return [now()->startOfWeek(), now()->endOfWeek(), now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
        } elseif ($period === 'anio') {
            return [now()->startOfYear(), now()->endOfYear(), now()->subYear()->startOfYear(), now()->subYear()->endOfYear()];
        }
        return [now()->startOfMonth(), now()->endOfMonth(), now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
    }

    public function index(Request $request)
    {
        $period = $request->query('period', 'mes');
        [$start, $end, $startPrev, $endPrev] = $this->getPeriodDates($period);
        
        // 1. Total Ventas (Periodo Actual)
        $totalVentas = Venta::whereBetween('created_at', [$start, $end])->sum('total');
        
        // Comparativa Periodo Anterior
        $totalVentasPrev = Venta::whereBetween('created_at', [$startPrev, $endPrev])->sum('total');
        $ventasTendencia = $totalVentasPrev > 0 ? (($totalVentas - $totalVentasPrev) / $totalVentasPrev) * 100 : ($totalVentas > 0 ? 100 : 0);

        // 2. Total Compras (Periodo Actual)
        $totalCompras = PedidoCompra::whereIn('estado', \App\Enums\PedidoEstado::valoresComprometidos())->whereBetween('created_at', [$start, $end])->sum('total');
        
        // 3. Margen Bruto
        $balance = $totalVentas - $totalCompras;
        
        return view('admin.dashboard', compact(
            'totalVentas', 'ventasTendencia', 
            'totalCompras', 'balance', 'period'
        ));
    }

    public function getChartData(Request $request)
    {
        $period = $request->query('period', 'mes');
        
        // Cashflow (Ventas vs Compras)
        $categories = [];
        $ventasSeries = [];
        $comprasSeries = [];
        
        for ($i = 5; $i >= 0; $i--) {
            if ($period === 'hoy') {
                $date = now()->subDays($i);
                $startGraph = $date->copy()->startOfDay();
                $endGraph = $date->copy()->endOfDay();
                $categories[] = $date->translatedFormat('d M');
            } elseif ($period === 'semana') {
                $date = now()->subWeeks($i);
                $startGraph = $date->copy()->startOfWeek();
                $endGraph = $date->copy()->endOfWeek();
                $categories[] = 'Sem ' . $date->weekOfYear;
            } elseif ($period === 'anio') {
                $date = now()->subYears($i);
                $startGraph = $date->copy()->startOfYear();
                $endGraph = $date->copy()->endOfYear();
                $categories[] = $date->translatedFormat('Y');
            } else {
                $date = now()->subMonths($i);
                $startGraph = $date->copy()->startOfMonth();
                $endGraph = $date->copy()->endOfMonth();
                $categories[] = $date->translatedFormat('M Y');
            }
            
            $ventasSeries[] = (float) Venta::whereBetween('created_at', [$startGraph, $endGraph])->sum('total');
            $comprasSeries[] = (float) PedidoCompra::whereIn('estado', \App\Enums\PedidoEstado::valoresComprometidos())->whereBetween('created_at', [$startGraph, $endGraph])->sum('total');
        }

        // Distribución por trabajador (Top 5 en el periodo actual)
        [$start, $end, $startPrev, $endPrev] = $this->getPeriodDates($period);
        
        $vendedores = Venta::select('vendedor_id', DB::raw('SUM(total) as total_vendido'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('vendedor_id')
            ->groupBy('vendedor_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->with('vendedor')
            ->get();
            
        $donutLabels = [];
        $donutSeries = [];
        
        foreach ($vendedores as $v) {
            $donutLabels[] = $v->vendedor ? explode(' ', $v->vendedor->name)[0] : 'Desconocido';
            $donutSeries[] = (float) $v->total_vendido;
        }

        return response()->json([
            'area' => [
                'categories' => $categories,
                'series' => [
                    ['name' => 'Ingresos (Ventas)', 'data' => $ventasSeries],
                    ['name' => 'Egresos (Compras)', 'data' => $comprasSeries]
                ]
            ],
            'donut' => [
                'labels' => !empty($donutLabels) ? $donutLabels : ['Sin datos'],
                'series' => !empty($donutSeries) ? $donutSeries : [1]
            ]
        ]);
    }

    public function getVentasData(Request $request)
    {
        $query = Venta::with(['cliente', 'vendedor'])->select('ventas.*');

        return DataTables::of($query)
            ->editColumn('created_at', function ($venta) {
                return $venta->created_at->format('d/m/Y H:i');
            })
            ->editColumn('total', function ($venta) {
                return '$' . number_format($venta->total, 2);
            })
            ->addColumn('cliente_nombre', function ($venta) {
                return $venta->cliente ? $venta->cliente->name : 'Consumidor Final';
            })
            ->addColumn('vendedor_nombre', function ($venta) {
                return $venta->vendedor ? $venta->vendedor->name : 'N/A';
            })
            ->make(true);
    }

    public function getComprasData(Request $request)
    {
        $query = PedidoCompra::with(['proveedor', 'bodeguero'])->select('pedido_compras.*');

        return DataTables::of($query)
            ->editColumn('created_at', function ($pedido) {
                return $pedido->created_at->format('d/m/Y H:i');
            })
            ->editColumn('total', function ($pedido) {
                return '$' . number_format($pedido->total, 2);
            })
            ->addColumn('proveedor_nombre', function ($pedido) {
                return $pedido->proveedor ? $pedido->proveedor->nombre_comercial : 'N/A';
            })
            ->addColumn('bodeguero_nombre', function ($pedido) {
                return $pedido->bodeguero ? $pedido->bodeguero->name : 'N/A';
            })
            ->make(true);
    }

    public function exportVentas(Request $request)
    {
        $period = $request->query('period', 'mes');
        [$start, $end] = $this->getPeriodDates($period);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\VentasExport($start, $end), "ventas_{$period}.xlsx");
    }

    public function exportVentasPdf(Request $request)
    {
        $period = $request->query('period', 'mes');
        [$start, $end] = $this->getPeriodDates($period);

        $totalVentas = Venta::whereBetween('created_at', [$start, $end])->sum('total');
        $totalCompras = PedidoCompra::whereIn('estado', \App\Enums\PedidoEstado::valoresComprometidos())->whereBetween('created_at', [$start, $end])->sum('total');
        $balance = $totalVentas - $totalCompras;
        $ventasCount = Venta::whereBetween('created_at', [$start, $end])->count();
        $comprasCount = PedidoCompra::whereBetween('created_at', [$start, $end])->count();
        
        $productosMasVendidos = DetalleVenta::select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->whereHas('venta', function($q) use($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->with('producto')
            ->get();
            
        $data = [
            'filtro' => $period,
            'start' => $start,
            'end' => $end,
            'totalVentas' => $totalVentas,
            'totalCompras' => $totalCompras,
            'balance' => $balance,
            'ventasCount' => $ventasCount,
            'comprasCount' => $comprasCount,
            'productosMasVendidos' => $productosMasVendidos,
        ];

        $pdf = Pdf::loadView('admin.reporte_ventas_pdf', $data);
        return $pdf->download('reporte_estadistico_mes.pdf');
    }
}
