@extends('layouts.admin')

@section('page_title', 'Lista de Proveedores')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h3 class="font-black text-slate-800 text-2xl">Proveedores</h3>
            <p class="text-sm text-slate-500 mt-1">Gestiona el catálogo de proveedores de la ferretería.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('proveedores.create') }}" class="btn-cta text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition whitespace-nowrap flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl p-4 shadow-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Compañía</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">RUC</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200">Contacto</th>
                        <th class="px-6 py-4 font-bold border-b border-slate-200 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($proveedores as $proveedor)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $proveedor->nombre_compania }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-mono">{{ $proveedor->ruc }}</td>
                            <td class="px-6 py-4">
                                <p class="text-slate-800">{{ $proveedor->email }}</p>
                                <p class="text-slate-500 text-xs">{{ $proveedor->telefono }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('proveedores.show', $proveedor->id) }}" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-100 hover:text-sky-700 transition-colors" title="Ver">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center hover:bg-sky-100 hover:text-sky-700 transition-colors" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('proveedores.destroy', $proveedor->id) }}" method="POST" onsubmit="return confirm('¿Eliminar proveedor? Se eliminará su usuario también.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 hover:text-red-700 transition-colors" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-building-user text-4xl mb-3 text-slate-300"></i>
                                <p>No hay proveedores registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $proveedores->links() }}
        </div>
    </div>
</div>
@endsection
