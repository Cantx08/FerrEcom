@extends('layouts.admin')

@section('page_title', 'Editar Permiso')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">Editar Permiso: {{ $permission->name }}</h3>
            <a href="{{ route('permissions.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>

        <form action="{{ route('permissions.update', $permission->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del permiso *</label>
                <input type="text" name="name" value="{{ old('name', $permission->name) }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm font-mono">
                @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                <p class="text-xs text-amber-600 mt-1">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Renombrar este permiso no actualiza referencias hardcodeadas en el código (Policies, Gates).
                </p>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('permissions.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
