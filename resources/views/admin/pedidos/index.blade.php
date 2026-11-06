@extends('layouts.admin')

@section('page_title', 'Pedidos de Compra (Proveedores)')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h3 class="font-black text-slate-800 text-2xl">Pedidos Emitidos</h3>
        <p class="text-sm text-slate-500 mt-1">Historial de reabastecimiento con proveedores detallado.</p>
    </div>
    @hasanyrole('Bodeguero|Administrador')
    <div class="flex items-center gap-2">
        <a href="{{ route('pedidos.create') }}" class="btn-cta text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition whitespace-nowrap flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Nuevo Pedido
        </a>
    </div>
    @endhasanyrole
</div>

@php
    $totales = \App\Models\PedidoCompra::selectRaw('estado, COUNT(*) as count')->groupBy('estado')->pluck('count', 'estado');
    $pendientes = ($totales[\App\Enums\PedidoEstado::Borrador->value] ?? 0) + ($totales[\App\Enums\PedidoEstado::EnAprobacion->value] ?? 0);
    $por_validar = ($totales[\App\Enums\PedidoEstado::Emitido->value] ?? 0) + ($totales[\App\Enums\PedidoEstado::Aceptado->value] ?? 0);
    $en_transito = $totales[\App\Enums\PedidoEstado::EnTransito->value] ?? 0;
@endphp

{{-- Dashboard KPIs --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-clock text-xl"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Pendientes</p>
            <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $pendientes }}</h4>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-clipboard-check text-xl"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Por Validar</p>
            <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $por_validar }}</h4>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-truck-fast text-xl"></i>
        </div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Enviado</p>
            <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $en_transito }}</h4>
        </div>
    </div>
</div>

@if($pedidos->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-truck-ramp-box text-4xl text-slate-300"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-700 mb-2">No hay pedidos emitidos</h3>
        <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">
            No se encontraron pedidos de compra registrados en el sistema.
        </p>
    </div>
@else
    <div x-data="{ filtro: 'todos' }">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide mb-6 border-b border-slate-200 pb-px">
            <button @click="filtro = 'todos'" :class="filtro === 'todos' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap px-4 py-2 border-b-2 font-bold text-sm transition-colors">
                Todos los pedidos
            </button>
            <button @click="filtro = 'accion'" :class="filtro === 'accion' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap px-4 py-2 border-b-2 font-bold text-sm transition-colors flex items-center gap-2">
                En Proceso <span class="bg-orange-100 text-orange-600 text-[10px] px-2 py-0.5 rounded-full font-black">{{ $por_validar + $en_transito }}</span>
            </button>
            <button @click="filtro = 'completados'" :class="filtro === 'completados' ? 'border-orange-500 text-orange-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap px-4 py-2 border-b-2 font-bold text-sm transition-colors">
                Completados
            </button>
        </div>

        <div class="space-y-6">
        @foreach($pedidos as $pedido)
            <div x-show="filtro === 'todos' || (filtro === 'accion' && ['emitido', 'aceptado', 'en_preparacion', 'en_transito'].includes('{{ $pedido->estado->value }}')) || (filtro === 'completados' && ['recibido_completado', 'pagado'].includes('{{ $pedido->estado->value }}'))" 
                 x-transition.opacity.duration.300ms
                 x-data="{ open: false }"
                 class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                {{-- Header del pedido --}}
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 cursor-pointer hover:bg-slate-100 transition-colors" @click="open = !open">
                    <div>
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <span class="font-bold text-sky-900 text-lg">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
                            
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-widest
                                @if($pedido->estado->color() === 'success') bg-emerald-100 text-emerald-700
                                @elseif($pedido->estado->color() === 'warning') bg-yellow-100 text-yellow-700
                                @elseif($pedido->estado->color() === 'info') bg-sky-100 text-sky-700
                                @elseif($pedido->estado->color() === 'primary') bg-blue-100 text-blue-700
                                @elseif($pedido->estado->color() === 'danger') bg-red-100 text-red-700
                                @else bg-slate-200 text-slate-600 @endif">
                                {{ $pedido->estado->etiqueta() }}
                            </span>
                            <div class="mt-4 flex flex-wrap items-center gap-2 md:gap-4 md:mt-0">
                                <span class="text-slate-500 text-xs font-semibold md:ml-2"><i class="fa-solid fa-truck mr-1"></i> {{ $pedido->proveedor->nombre_compania ?? 'Proveedor Eliminado' }}</span>
                                <span class="text-slate-300 hidden md:inline">|</span>
                                <span class="text-slate-500 text-xs font-semibold"><i class="fa-solid fa-user-tag mr-1"></i> Por: {{ $pedido->bodeguero->name ?? 'Admin' }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-2">
                            Emitido el {{ $pedido->created_at->format('d/m/Y \a \l\a\s H:i') }} 
                        </p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="text-right flex-1 md:flex-none">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total</p>
                            <p class="font-black text-xl text-sky-900">${{ number_format($pedido->total, 2) }}</p>
                        </div>
                        <a href="{{ route('pedidos.show', $pedido->id) }}" @click.stop class="shrink-0 bg-white border border-slate-200 text-sky-600 hover:bg-sky-50 hover:border-sky-200 font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                            <i class="fa-solid fa-eye mr-1.5"></i> Gestionar
                        </a>
                        <button class="text-slate-400 hover:text-sky-900 transition-transform duration-300 ml-2 hidden sm:block" :class="{'rotate-180': open}">
                            <i class="fa-solid fa-chevron-down text-xl"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Detalles de productos --}}
                <div class="p-6" x-show="open" x-collapse>
                    <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Detalle del Pedido</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($pedido->detalles as $detalle)
                            <div class="flex items-center gap-4 bg-white border border-slate-200 hover:border-sky-200 hover:bg-sky-50/30 p-3 rounded-xl transition-colors">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-bold text-slate-700 truncate" title="{{ $detalle->productoCatalogo->nombre ?? 'Producto Eliminado' }}">{{ $detalle->productoCatalogo->nombre ?? 'Producto Eliminado' }}</h5>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $detalle->cantidad_solicitada }} ud. x ${{ number_format($detalle->precio, 2) }}</p>
                                </div>
                                <div class="font-black text-sky-900 shrink-0 pr-2">
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
        <div class="mt-8">
            {{ $pedidos->links() }}
        </div>
    @endif
    </div>
@endif
</div>
@endsection
