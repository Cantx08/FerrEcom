@extends('layouts.admin')

@section('title', 'Pedido #' . $pedido->id . ' — Portal Proveedor')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('proveedor.pedidos.index') }}" class="text-sm text-sky-600 hover:underline mb-1 block">
                ← Volver a mis pedidos
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Pedido #{{ $pedido->id }}</h2>
        </div>
        <span class="px-3 py-1.5 rounded-full text-sm font-semibold
            @if($pedido->estado->color() === 'success') bg-green-100 text-green-700
            @elseif($pedido->estado->color() === 'warning') bg-yellow-100 text-yellow-700
            @elseif($pedido->estado->color() === 'info') bg-sky-100 text-sky-700
            @elseif($pedido->estado->color() === 'primary') bg-blue-100 text-blue-700
            @elseif($pedido->estado->color() === 'danger') bg-red-100 text-red-700
            @else bg-slate-100 text-slate-600 @endif">
            {{ $pedido->estado->etiqueta() }}
        </span>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Detalle de productos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">Productos Solicitados</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-slate-600">Producto</th>
                    <th class="text-center px-5 py-3 font-semibold text-slate-600">Cantidad Solicitada</th>
                    <th class="text-center px-5 py-3 font-semibold text-slate-600">Disponible</th>
                    <th class="text-right px-5 py-3 font-semibold text-slate-600">Precio Unit.</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($pedido->detalles as $detalle)
                <tr>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $detalle->productoCatalogo->nombre ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-center">{{ $detalle->cantidad_solicitada }}</td>
                    <td class="px-5 py-3 text-center">{{ $detalle->productoCatalogo->stock_disponible ?? '—' }}</td>
                    <td class="px-5 py-3 text-right">${{ number_format($detalle->precio, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200">
                <tr>
                    <td colspan="3" class="px-5 py-3 font-semibold text-slate-700 text-right">Total</td>
                    <td class="px-5 py-3 font-bold text-slate-800 text-right">${{ number_format($pedido->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Acciones disponibles para el proveedor --}}
    @if(!empty($accionesDisponibles))
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
        <h3 class="font-semibold text-slate-700 mb-4">Actualizar Estado del Pedido</h3>

        <form action="{{ route('proveedor.pedidos.updateStatus', $pedido->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nuevo Estado</label>
                    <select name="estado" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @foreach($accionesDisponibles as $estado)
                            <option value="{{ $estado->value }}">{{ $estado->etiqueta() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Observaciones (opcional)</label>
                    <textarea name="notas" rows="3"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                        placeholder="Notas adicionales para la ferretería..."></textarea>
                </div>
            </div>

            {{-- Cantidades disponibles (visible al aceptar) --}}
            @if(in_array(\App\Enums\PedidoEstado::Aceptado, $accionesDisponibles, strict: true))
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl mb-4">
                <p class="text-sm font-semibold text-yellow-800 mb-3">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                    Al aceptar, indique la cantidad disponible de cada producto:
                </p>
                @foreach($pedido->detalles as $detalle)
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-sm text-slate-700 flex-1">{{ $detalle->productoCatalogo->nombre ?? 'N/A' }}</span>
                    <input type="number"
                           name="detalles[{{ $detalle->id }}][cantidad_disponible]"
                           value="{{ $detalle->cantidad_solicitada }}"
                           min="0"
                           class="w-24 border border-slate-300 rounded-lg px-2 py-1 text-sm text-center focus:ring-2 focus:ring-sky-500">
                </div>
                @endforeach
            </div>
            @endif

            <button type="submit"
                class="w-full bg-sky-600 hover:bg-sky-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-colors text-sm">
                Siguiente<i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
