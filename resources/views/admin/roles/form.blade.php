@extends('layouts.admin')

@section('page_title', isset($role) ? 'Editar Rol' : 'Nuevo Rol')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                {{ isset($role) ? 'Editar Rol: ' . $role->name : 'Crear Nuevo Rol' }}
            </h3>
            <a href="{{ route('roles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>
        
        <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST" class="p-6">
            @csrf
            @if(isset($role))
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Rol *</label>
                    <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Permisos Asignados</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($permissions as $perm)
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" 
                                    {{ isset($role) && $role->hasPermissionTo($perm->name) ? 'checked' : (is_array(old('permissions')) && in_array($perm->name, old('permissions')) ? 'checked' : '') }}
                                    class="w-4 h-4 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                                <span class="ml-2 text-sm font-medium text-slate-700">{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('permissions') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> {{ isset($role) ? 'Guardar Cambios' : 'Registrar Rol' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
