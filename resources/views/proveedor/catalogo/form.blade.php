@extends('layouts.admin')

@section('title', isset($producto) ? 'Editar Producto — Catálogo' : 'Nuevo Producto — Catálogo')

@section('content')
<div class="p-6 max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('proveedor.catalogo.index') }}" class="text-sm text-sky-600 hover:underline mb-1 block">
            ← Volver al catálogo
        </a>
        <h2 class="text-2xl font-bold text-slate-800">
            {{ isset($producto) ? 'Editar Producto' : 'Nuevo Producto' }}
        </h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ isset($producto) ? route('proveedor.catalogo.update', $producto->id) : route('proveedor.catalogo.store') }}"
              method="POST">
            @csrf
            @isset($producto) @method('PUT') @endisset

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 @error('nombre') border-red-400 @enderror"
                           placeholder="Ej: Tornillo hexagonal M8">
                    @error('nombre')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="Descripción opcional del producto">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Costo Unitario <span class="text-red-500">*</span></label>
                        <input type="number" name="costo_unitario" step="0.01" min="0"
                               value="{{ old('costo_unitario', $producto->costo_unitario ?? '') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 @error('costo_unitario') border-red-400 @enderror"
                               placeholder="0.00">
                        @error('costo_unitario')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Unidad</label>
                        <input type="text" name="unidad" value="{{ old('unidad', $producto->unidad ?? '') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500"
                               placeholder="Ej: unidad, caja, kg">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-colors text-sm">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                    {{ isset($producto) ? 'Actualizar Producto' : 'Guardar Producto' }}
                </button>
                <a href="{{ route('proveedor.catalogo.index') }}"
                   class="px-5 py-2.5 border border-slate-300 text-slate-600 rounded-xl hover:bg-slate-50 transition-colors text-sm font-medium">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
