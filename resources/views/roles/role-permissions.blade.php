@extends('layouts.admin')

@section('page_title', 'Permisos del Rol: ' . $role->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="font-black text-slate-800 text-2xl">Permisos de "{{ $role->name }}"</h3>
            <p class="text-sm text-slate-500 mt-1">Selecciona los permisos que tendrán los usuarios con este rol.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
            <i class="fa-solid fa-chevron-left mr-1"></i> Volver
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-4 shadow-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('role-permissions.update', $role->id) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse($permissions as $permission)
                <label class="flex items-center gap-2 border border-slate-200 rounded-lg px-3 py-2 hover:border-sky-300 cursor-pointer transition">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    <span class="text-sm text-slate-700">{{ $permission->name }}</span>
                </label>
            @empty
                <p class="text-slate-400 text-sm italic col-span-full">No hay permisos definidos todavía.</p>
            @endforelse
        </div>

        <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('roles.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Guardar Permisos
            </button>
        </div>
    </form>
</div>
@endsection
