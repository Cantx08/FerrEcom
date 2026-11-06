@extends('layouts.admin')

@section('page_title', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                {{ isset($producto) ? 'Editar Producto: ' . $producto->nombre : 'Registrar Nuevo Producto' }}
            </h3>
            <a href="{{ route('productos.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>
        
        <form action="{{ isset($producto) ? route('productos.update', $producto->id) : route('productos.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @if(isset($producto))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="space-y-4 md:col-span-2">
                    <h4 class="font-bold text-sky-900 border-b border-slate-100 pb-2">Información Básica</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Producto *</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            @error('nombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Código SKU *</label>
                            <input type="text" name="sku" value="{{ old('sku', $producto->sku ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm font-mono">
                            @error('sku') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Categoría *</label>
                            <select name="categoria_id" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Unidad de Medida *</label>
                            <input type="text" name="unidad_medida" value="{{ old('unidad_medida', $producto->unidad_medida ?? 'UNIDAD') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            @error('unidad_medida') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                        @error('descripcion') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Precios e Inventario -->
                <div class="space-y-4">
                    <h4 class="font-bold text-sky-900 border-b border-slate-100 pb-2">Precios e Inventario</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Precio Compra *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500">$</span>
                                <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '0.00') }}" required class="w-full pl-7 rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            </div>
                            @error('precio_compra') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Precio Venta *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500">$</span>
                                <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta ?? '0.00') }}" required class="w-full pl-7 rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm font-bold text-sky-900">
                            </div>
                            @error('precio_venta') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Actual</label>
                            <input type="number" step="0.01" name="stock_actual" value="{{ old('stock_actual', $producto->stock_actual ?? '0') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            @error('stock_actual') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Stock Mínimo</label>
                            <input type="number" step="0.01" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? '5') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            @error('stock_minimo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <h5 class="font-bold text-sky-800 border-t border-slate-100 pt-4 mt-4 text-sm uppercase tracking-wide">Configuración al Por Mayor</h5>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Umbral (Unidades)</label>
                            <input type="number" name="cantidad_por_mayor" value="{{ old('cantidad_por_mayor', $producto->cantidad_por_mayor ?? '') }}" placeholder="Ej: 50" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            <span class="text-[10px] text-slate-400 block mt-1">Dejar vacío si no aplica</span>
                            @error('cantidad_por_mayor') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Descuento (%)</label>
                            <div class="relative">
                                <input type="number" step="0.01" max="100" name="descuento_por_mayor" value="{{ old('descuento_por_mayor', $producto->descuento_por_mayor ?? '') }}" placeholder="Ej: 10.00" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                                <span class="absolute right-3 top-2 text-slate-500">%</span>
                            </div>
                            @error('descuento_por_mayor') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Imagen y Estado -->
                <div class="space-y-4">
                    <h4 class="font-bold text-sky-900 border-b border-slate-100 pb-2">Imagen y Estado</h4>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Imagen del Producto</label>
                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        @if(isset($producto) && $producto->imagen)
                            <div class="mt-2">
                                <p class="text-xs text-slate-500 mb-1">Imagen actual:</p>
                                <img src="{{ asset($producto->imagen) }}" alt="Imagen de {{ $producto->nombre }}" class="h-20 w-20 object-contain border border-slate-200 rounded-lg p-1 bg-slate-50">
                            </div>
                        @endif
                        @error('imagen') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="estado" value="1" {{ old('estado', $producto->estado ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-5 h-5">
                            <div>
                                <span class="block text-sm font-semibold text-slate-700">Producto Activo</span>
                                <span class="block text-xs text-slate-500">Los productos inactivos no se muestran en la tienda pública.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('productos.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> {{ isset($producto) ? 'Guardar Cambios' : 'Registrar Producto' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
