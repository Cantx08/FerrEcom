@extends('layouts.admin')

@section('title', 'Portal Proveedor — Mis Pedidos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-sky-900">Mis Pedidos de Compra</h1>
            <p class="text-slate-500 text-sm mt-1">Proveedor: <span class="font-semibold text-sky-700">{{ $proveedor->nombre_compania }}</span></p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <div>
                <p class="font-bold">¡Éxito!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
            <div>
                <p class="font-bold">Error</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($pedidos->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center mt-6">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No tienes pedidos asignados aún</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
                Tu historial de pedidos de compra está vacío.
            </p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($pedidos as $pedido)
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" x-data="{ open: false }">
                    {{-- Header del pedido --}}
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 cursor-pointer hover:bg-slate-100 transition-colors" @click="open = !open">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="font-bold text-sky-900 text-lg">Orden #{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</span>
                                
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-widest
                                    @if($pedido->estado->color() === 'success') bg-emerald-100 text-emerald-700
                                    @elseif($pedido->estado->color() === 'warning') bg-yellow-100 text-yellow-700
                                    @elseif($pedido->estado->color() === 'info') bg-sky-100 text-sky-700
                                    @elseif($pedido->estado->color() === 'primary') bg-blue-100 text-blue-700
                                    @elseif($pedido->estado->color() === 'danger') bg-red-100 text-red-700
                                    @else bg-slate-200 text-slate-600 @endif">
                                    {{ $pedido->estado->etiqueta() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mb-1">Solicitado el {{ $pedido->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                            <p class="text-xs text-slate-500">Bodeguero: <span class="font-semibold text-slate-700">{{ $pedido->bodeguero?->name ?? 'N/A' }}</span></p>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto mt-2 sm:mt-0">
                            <div class="text-left sm:text-right flex-1 sm:flex-none">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total</p>
                                <p class="font-black text-xl text-sky-900">${{ number_format($pedido->total, 2) }}</p>
                            </div>
                            <a href="{{ route('proveedor.pedidos.show', $pedido->id) }}" @click.stop class="shrink-0 bg-sky-600 border border-transparent text-white hover:bg-sky-700 font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                <i class="fa-solid fa-eye mr-1.5"></i> Ver Pedido
                            </a>
                            <button class="text-slate-400 hover:text-sky-900 transition-transform duration-300 ml-2 hidden sm:block" :class="{'rotate-180': open}">
                                <i class="fa-solid fa-chevron-down text-xl"></i>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Detalles de productos --}}
                    <div class="p-6" x-show="open" x-collapse>
                        <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Detalle de Productos Solicitados</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($pedido->detalles as $detalle)
                                <div class="flex items-center gap-4 bg-slate-50 border border-slate-100 p-3 rounded-xl">
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-bold text-slate-700 truncate" title="{{ optional($detalle->productoCatalogo)->nombre ?? 'Producto Desconocido' }}">
                                            {{ optional($detalle->productoCatalogo)->nombre ?? 'Producto Desconocido' }}
                                        </h5>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $detalle->cantidad_solicitada }} x ${{ number_format($detalle->precio, 2) }}</p>
                                    </div>
                                    <div class="font-black text-sky-900 shrink-0">
                                        ${{ number_format($detalle->cantidad_solicitada * $detalle->precio, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($pedidos->hasPages())
        <div class="mt-6">
            {{ $pedidos->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
