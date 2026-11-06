@extends('layouts.admin')

@section('page_title', 'Categorías')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h3 class="font-black text-slate-800 text-2xl">Categorías de Producto</h3>
            <p class="text-sm text-slate-500 mt-1">Organiza el catálogo interno en categorías.</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="btn-cta text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition whitespace-nowrap flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Nueva Categoría
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-4 shadow-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 shadow-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Nombre</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Descripción</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Productos</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Estado</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($categorias as $categoria)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $categoria->nombre }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $categoria->slug }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $categoria->descripcion ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $categoria->producto_count }}</td>
                            <td class="px-6 py-4">
                                @if($categoria->estado)
                                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest">Activa</span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest">Inactiva</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('categorias.edit', $categoria->id) }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center hover:bg-sky-100 hover:text-sky-700 transition-colors" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 hover:text-red-700 transition-colors" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-tags text-4xl mb-3 text-slate-300"></i>
                                <p>No hay categorías registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $categorias->links() }}
        </div>
    </div>
</div>
@endsection
