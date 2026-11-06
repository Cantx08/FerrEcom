@extends('layouts.admin')

@section('page_title', 'Usuarios y Roles')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
    <!-- Usuarios Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Usuarios del Sistema</h3>
            <a href="{{ route('users.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-sm transition">
                <i class="fa-solid fa-plus mr-1"></i> Nuevo Usuario
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Roles</th>
                        <th class="px-6 py-3">Fecha Registro</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                                <span class="bg-sky-100 text-sky-800 text-xs font-bold px-2 py-1 rounded-md">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center">
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-slate-400 hover:text-orange-500 transition mx-1"><i class="fa-solid fa-pen-to-square"></i></a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500 transition mx-1"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Roles Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Roles y Permisos</h3>
            <a href="{{ route('roles.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-sm transition">
                <i class="fa-solid fa-plus mr-1"></i> Nuevo Rol
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            @foreach($roles as $role)
            <div class="border border-slate-200 rounded-xl p-5 hover:border-sky-300 transition shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <h4 class="font-bold text-lg text-sky-900">{{ $role->name }}</h4>
                    <div class="flex gap-2">
                        @can('update', $role)
                            <a href="{{ route('roles.edit', $role->id) }}" class="text-slate-400 hover:text-orange-500"><i class="fa-solid fa-pen-to-square"></i></a>
                        @endcan
                        @can('delete', $role)
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este rol?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        @endcan
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Permisos asignados</p>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($role->permissions as $perm)
                        <span class="bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ $perm->name }}</span>
                    @empty
                        <span class="text-slate-400 text-xs italic">Sin permisos específicos</span>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
