@extends('layouts.admin')

@section('page_title', isset($categoria) ? 'Editar Categoría' : 'Nueva Categoría')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                {{ isset($categoria) ? 'Editar Categoría: ' . $categoria->nombre : 'Nueva Categoría' }}
            </h3>
            <a href="{{ route('categorias.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>

        <form action="{{ isset($categoria) ? route('categorias.update', $categoria->id) : route('categorias.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @if(isset($categoria))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                @error('nombre') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
                @error('descripcion') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="estado" value="1" {{ old('estado', $categoria->estado ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500 w-5 h-5">
                    <div>
                        <span class="block text-sm font-semibold text-slate-700">Categoría Activa</span>
                        <span class="block text-xs text-slate-500">Las categorías inactivas no aparecen al crear/editar productos.</span>
                    </div>
                </label>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('categorias.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> {{ isset($categoria) ? 'Guardar Cambios' : 'Crear Categoría' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
