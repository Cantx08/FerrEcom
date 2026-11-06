@extends('layouts.admin')

@section('page_title', isset($anuncio) ? 'Editar Anuncio' : 'Nuevo Anuncio')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                {{ isset($anuncio) ? 'Editar Anuncio: ' . $anuncio->titulo : 'Crear Nuevo Anuncio' }}
            </h3>
            <a href="{{ route('anuncios.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>
        
        <form action="{{ isset($anuncio) ? route('anuncios.update', $anuncio->id) : route('anuncios.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @if(isset($anuncio))
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Título del Anuncio *</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $anuncio->titulo ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                    @error('titulo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Texto Promocional (Contenido)</label>
                    <textarea name="contenido" rows="3" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">{{ old('contenido', $anuncio->contenido ?? '') }}</textarea>
                    @error('contenido') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Estado de Publicación *</label>
                        <select name="estado" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                            <option value="pendiente" {{ old('estado', $anuncio->estado ?? '') == 'pendiente' ? 'selected' : '' }}>Pendiente de Revisión</option>
                            <option value="aprobado" {{ old('estado', $anuncio->estado ?? '') == 'aprobado' ? 'selected' : '' }}>Aprobado (Visible)</option>
                            <option value="rechazado" {{ old('estado', $anuncio->estado ?? '') == 'rechazado' ? 'selected' : '' }}>Rechazado (Oculto)</option>
                        </select>
                        @error('estado') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Imagen / Banner</label>
                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        @if(isset($anuncio) && $anuncio->imagen)
                            <div class="mt-2">
                                <p class="text-xs text-slate-500 mb-1">Imagen actual:</p>
                                <img src="{{ asset('storage/' . $anuncio->imagen) }}" alt="Banner actual" class="h-20 w-auto object-cover border border-slate-200 rounded-lg p-1 bg-slate-50">
                            </div>
                        @endif
                        @error('imagen') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('anuncios.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> {{ isset($anuncio) ? 'Guardar Cambios' : 'Publicar Anuncio' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
