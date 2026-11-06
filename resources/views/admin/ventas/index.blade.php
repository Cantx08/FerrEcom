@extends('layouts.admin')

@section('page_title', 'Historial de Ventas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h3 class="font-black text-slate-800 text-2xl">Ventas a Clientes</h3>
        <p class="text-sm text-slate-500 mt-1">Listado de transacciones físicas y online con su detalle.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('ventas.create') }}" class="btn-cta text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition whitespace-nowrap">
            <i class="fa-solid fa-plus mr-1"></i> Nueva Venta
        </a>
    </div>
</div>

@if($ventas->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-cash-register text-4xl text-slate-300"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-700 mb-2">No se han registrado ventas</h3>
        <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
            Aún no hay transacciones para mostrar en este historial.
        </p>
    </div>
@else
    <div class="space-y-6">
        @foreach($ventas as $venta)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow" x-data="{ open: false }">
                {{-- Header de la compra --}}
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 cursor-pointer hover:bg-slate-100 transition-colors" @click="open = !open">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <span class="font-bold text-sky-900 text-lg">#VNT-{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
                            
                            @if($venta->esOnline())
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest"><i class="fa-solid fa-globe mr-1"></i> Online</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest"><i class="fa-solid fa-store mr-1"></i> Física</span>
                            @endif
                            
                            @if($venta->estado === \App\Enums\VentaEstado::Pagado)
                                <span class="bg-sky-100 text-sky-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest"><i class="fa-solid fa-money-bill-wave mr-1"></i> Pagado</span>
                            @elseif($venta->estado === \App\Enums\VentaEstado::Entregado)
                                <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest"><i class="fa-solid fa-check-double mr-1"></i> {{ $venta->estado->etiqueta() }}</span>
                            @else
                                <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest">{{ $venta->estado->etiqueta() ?? $venta->estado->value }}</span>
                            @endif
                            
                            <span class="text-slate-500 text-xs font-semibold md:ml-2"><i class="fa-solid fa-user mr-1"></i> {{ $venta->cliente->name ?? ($venta->factura?->razon_social ?? 'Cliente Final') }}</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-2">
                            Realizada el {{ $venta->created_at->format('d/m/Y \a \l\a\s H:i') }} 
                            <span class="mx-2 text-slate-300">•</span> 
                            Vendedor: {{ $venta->vendedor->name ?? 'Sistema' }}
                        </p>
                        @if($venta->tracking_code)
                            <p class="text-xs font-bold text-slate-600 bg-slate-200 inline-block px-2 py-1 rounded-md mt-1" title="Código de retiro/tracking">
                                <i class="fa-solid fa-barcode mr-1"></i> Código: {{ $venta->tracking_code }}
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="text-right flex-1 md:flex-none">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total</p>
                            <p class="font-black text-xl text-sky-900">${{ number_format($venta->total, 2) }}</p>
                        </div>
                        <a href="{{ route('ventas.show', $venta->id) }}" @click.stop class="shrink-0 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-sky-700 hover:border-sky-200 font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            <i class="fa-solid fa-eye mr-1.5"></i> Ver
                        </a>
                        <a href="{{ route('factura.descargar', $venta->id) }}" target="_blank" @click.stop class="shrink-0 bg-white border border-slate-200 text-sky-700 hover:bg-sky-50 hover:text-sky-900 hover:border-sky-200 font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            <i class="fa-solid fa-file-pdf text-red-500 mr-1.5"></i> Factura
                        </a>
                        <button class="text-slate-400 hover:text-sky-900 transition-transform duration-300 ml-2" :class="{'rotate-180': open}">
                            <i class="fa-solid fa-chevron-down text-xl"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Detalles de productos --}}
                <div class="p-6" x-show="open" x-collapse>
                    <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Detalle de Productos</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($venta->detalles as $detalle)
                            <div class="flex items-center gap-4 bg-slate-50 border border-slate-100 p-3 rounded-xl">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-bold text-slate-700 truncate" title="{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}">{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</h5>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $detalle->cantidad }} ud. x ${{ number_format($detalle->precio, 2) }}</p>
                                </div>
                                <div class="font-black text-sky-900 shrink-0 pr-2">
                                    ${{ number_format($detalle->cantidad * $detalle->precio, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($ventas->hasPages())
        <div class="mt-8">
            {{ $ventas->links() }}
        </div>
    @endif
@endif
</div>
@endsection
