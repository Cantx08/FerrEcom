@extends('layouts.app')

@section('title', 'Mis Compras')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-sky-900">Mis Compras</h1>
        </div>
        <a href="{{ route('home') }}" class="btn-cta text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md">
            <i class="fa-solid fa-bag-shopping mr-2"></i> Ir a Tienda
        </a>
    </div>

    @if($ventas->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center mt-6">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No has realizado compras aún</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
                Tu historial está vacío. Explora nuestro catálogo y descubre las mejores ofertas.
            </p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($ventas as $venta)
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ open: false }">
                    {{-- Header de la compra --}}
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 cursor-pointer hover:bg-slate-100 transition-colors" @click="open = !open">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="font-bold text-sky-900 text-lg">Orden #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
                                @if($venta->esOnline())
                                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest"><i class="fa-solid fa-globe mr-1"></i> Online</span>
                                @else
                                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest"><i class="fa-solid fa-store mr-1"></i> Física</span>
                                @endif
                                
                                @if($venta->estado === \App\Enums\VentaEstado::Pagado)
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest">Pagado</span>
                                @else
                                    <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-widest">{{ $venta->estado->etiqueta() ?? $venta->estado->value }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 font-medium mb-1">Realizada el {{ $venta->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                            @if($venta->tracking_code)
                                <p class="text-xs font-bold text-slate-600 bg-slate-200 inline-block px-2 py-1 rounded-md mt-1" title="Presenta este código al retirar tu compra o para rastreo">
                                    <i class="fa-solid fa-barcode mr-1"></i> 
                                    @if($venta->metodo_entrega === \App\Enums\MetodoEntrega::EnvioDomicilio->value)
                                        Tu código para recibir el pedido es: {{ $venta->tracking_code }}
                                    @else
                                        Tu código para retirar tu pedido es: {{ $venta->tracking_code }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <div class="text-right flex-1 sm:flex-none">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total</p>
                                <p class="font-black text-xl text-sky-900">${{ number_format($venta->total, 2) }}</p>
                            </div>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($venta->detalles as $detalle)
                                <div class="flex items-center gap-4 bg-slate-50 border border-slate-100 p-3 rounded-xl">
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-bold text-slate-700 truncate" title="{{ $detalle->producto->nombre }}">{{ $detalle->producto->nombre }}</h5>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $detalle->cantidad }} x ${{ number_format($detalle->precio, 2) }}</p>
                                    </div>
                                    <div class="font-black text-sky-900 shrink-0">
                                        ${{ number_format($detalle->cantidad * $detalle->precio, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // La compra fue exitosa, limpiamos el carrito
        window.dispatchEvent(new CustomEvent('cart-clear'));
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Compra Exitosa!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#ea580c'
            });
        }
    });
</script>
@endif
@endpush

@endsection
