@extends('layouts.admin')

@section('title', 'Mi Catálogo — Portal Proveedor')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-sky-900">Mi Catálogo de Productos</h1>
            <p class="text-slate-500 text-sm mt-1">{{ $proveedor->nombre_compania }}</p>
        </div>
        <a href="{{ route('proveedor.catalogo.create') }}"
           class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 rounded-xl transition-colors text-sm">
            <i class="fa-solid fa-plus"></i> Nuevo Producto
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-slate-600">Nombre</th>
                    <th class="text-left px-5 py-3 font-semibold text-slate-600">Stock Disponible</th>
                    <th class="text-right px-5 py-3 font-semibold text-slate-600">Costo Unitario</th>
                    <th class="text-center px-5 py-3 font-semibold text-slate-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($productos as $producto)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $producto->nombre }}</td>
                    <td class="px-5 py-3 text-slate-600 font-bold {{ $producto->stock_disponible > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $producto->stock_disponible }}</td>
                    <td class="px-5 py-3 text-right font-semibold">${{ number_format($producto->costo_unitario, 2) }}</td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('proveedor.catalogo.edit', $producto->id) }}"
                               class="p-1.5 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-colors" title="Editar">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>
                            <form action="{{ route('proveedor.catalogo.destroy', $producto->id) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este producto del catálogo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors" title="Eliminar">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-slate-400">
                        <i class="fa-solid fa-box-open text-4xl mb-3 block opacity-30"></i>
                        Tu catálogo está vacío. Agrega tu primer producto.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($productos->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $productos->links() }}</div>
        @endif
    </div>
</div>
@endsection
