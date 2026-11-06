@extends('layouts.admin')

@section('page_title', 'Nuevo Permiso')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">Nuevo Permiso</h3>
            <a href="{{ route('permissions.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>

        <form action="{{ route('permissions.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del permiso *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="ej: ver_reportes" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm font-mono">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                <p class="text-xs text-slate-400 mt-1">Convención usada en el sistema: <code>accion_modulo</code> (ej: <code>crear_productos</code>).</p>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('permissions.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Crear Permiso
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
