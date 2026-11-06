@extends('layouts.admin')

@section('page_title', 'Venta #VNT-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="font-black text-slate-800 text-2xl">Venta #VNT-{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-sm text-slate-500 mt-1">{{ $venta->canal->etiqueta() }} · {{ $venta->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <a href="{{ route('ventas.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
            <i class="fa-solid fa-chevron-left mr-1"></i> Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-wrap items-center gap-3">
            <span class="bg-sky-100 text-sky-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest">{{ $venta->estado->etiqueta() }}</span>
            <span class="text-slate-500 text-xs font-semibold"><i class="fa-solid fa-user mr-1"></i> {{ $venta->cliente->name ?? ($venta->factura?->razon_social ?? 'Consumidor Final') }}</span>
            <span class="text-slate-500 text-xs font-semibold"><i class="fa-solid fa-cash-register mr-1"></i> {{ $venta->vendedor->name ?? 'N/A' }}</span>
            @if($venta->tracking_code)
                <span class="text-slate-500 text-xs font-semibold font-mono"><i class="fa-solid fa-truck mr-1"></i> {{ $venta->tracking_code }}</span>
            @endif
        </div>

        <div class="p-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Detalle de Productos</h4>
            <div class="divide-y divide-slate-100">
                @foreach($venta->detalles as $detalle)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-bold text-slate-700">{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</p>
                            <p class="text-xs text-slate-500">{{ $detalle->cantidad }} ud. x ${{ number_format($detalle->precio, 2) }}</p>
                        </div>
                        <div class="font-black text-sky-900">${{ number_format($detalle->cantidad * $detalle->precio, 2) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-4 border-t border-slate-200 flex justify-end">
                <div class="text-right">
                    @if($venta->descuento > 0)
                        <p class="text-xs text-slate-500">Descuento: -${{ number_format($venta->descuento, 2) }}</p>
                    @endif
                    <p class="text-2xl font-black text-slate-800">${{ number_format($venta->total, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end">
            <a href="{{ route('factura.descargar', $venta->id) }}" target="_blank" class="bg-white border border-slate-200 text-sky-700 hover:bg-sky-50 hover:text-sky-900 hover:border-sky-200 font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                <i class="fa-solid fa-file-pdf text-red-500 mr-1.5"></i> Descargar Factura
            </a>
        </div>
    </div>

    @if($venta->historial->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Historial de Estados</h4>
            <ul class="space-y-3">
                @foreach($venta->historial as $evento)
                    <li class="flex items-start gap-3 text-sm">
                        <i class="fa-solid fa-circle-dot text-sky-400 mt-1 text-[8px]"></i>
                        <div>
                            <p class="text-slate-700">
                                <span class="font-semibold">{{ \App\Enums\VentaEstado::from($evento->estado_nuevo)->etiqueta() }}</span>
                                @if($evento->usuario) por {{ $evento->usuario->name }} @endif
                            </p>
                            <p class="text-xs text-slate-400">{{ $evento->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
