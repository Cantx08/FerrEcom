@extends('layouts.admin')

@section('page_title', isset($user) ? 'Editar Usuario' : 'Nuevo Usuario')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">
                {{ isset($user) ? 'Editar Usuario: ' . $user->name : 'Crear Nuevo Usuario' }}
            </h3>
            <a href="{{ route('roles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>
        
        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" class="p-6">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                    @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Contraseña {{ isset($user) ? '(Opcional)' : '*' }}</label>
                        <input type="password" name="password" {{ isset($user) ? '' : 'required' }} class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                        @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Confirmar Contraseña {{ isset($user) ? '(Opcional)' : '*' }}</label>
                        <input type="password" name="password_confirmation" {{ isset($user) ? '' : 'required' }} class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Rol *</label>
                    <select name="role" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
                        <option value="" disabled {{ !isset($user) && !old('role') ? 'selected' : '' }}>Seleccione un rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" 
                                {{ isset($user) && $user->hasRole($role->name) ? 'selected' : (old('role') == $role->name ? 'selected' : '') }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-sky-600 rounded-lg hover:bg-sky-700 transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> {{ isset($user) ? 'Guardar Cambios' : 'Registrar Usuario' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
