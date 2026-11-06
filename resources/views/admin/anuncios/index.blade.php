@extends('layouts.admin')

@section('page_title', 'Gestión de Anuncios')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50">
        <div>
            <h3 class="font-bold text-slate-800 text-lg">Banners Promocionales</h3>
            <p class="text-xs text-slate-500">Administra los anuncios mostrados en la página principal.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('anuncios.create') }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-sm transition whitespace-nowrap">
                <i class="fa-solid fa-plus mr-1"></i> Nuevo Anuncio
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="px-6 py-3 w-16">Imagen</th>
                    <th class="px-6 py-3">Título / Contenido</th>
                    <th class="px-6 py-3 w-32">Estado</th>
                    <th class="px-6 py-3 w-40 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($anuncios as $anuncio)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="w-16 h-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                            @if($anuncio->imagen)
                                <img src="{{ asset('storage/' . $anuncio->imagen) }}" class="max-h-full object-cover">
                            @else
                                <i class="fa-solid fa-image text-slate-300"></i>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-800 mb-1">{{ $anuncio->titulo }}</p>
                        <p class="text-xs text-slate-500 line-clamp-2 max-w-md">{{ $anuncio->contenido }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($anuncio->estado === 'aprobado')
                            <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full whitespace-nowrap"><i class="fa-solid fa-check mr-1"></i>Aprobado</span>
                        @elseif($anuncio->estado === 'rechazado')
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full whitespace-nowrap"><i class="fa-solid fa-xmark mr-1"></i>Rechazado</span>
                        @else
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full whitespace-nowrap"><i class="fa-regular fa-clock mr-1"></i>Pendiente</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center">
                            @hasanyrole('Administrador|SuperAdmin')
                                @if($anuncio->estado === 'pendiente')
                                    <!-- Formulario para aprobar -->
                                    <form action="{{ route('anuncios.aprobar', $anuncio->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-emerald-500 hover:text-emerald-600 transition p-1.5" title="Aprobar"><i class="fa-solid fa-check-circle"></i></button>
                                    </form>
                                    <!-- Formulario para rechazar -->
                                    <form action="{{ route('anuncios.denegar', $anuncio->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-600 transition p-1.5" title="Rechazar"><i class="fa-solid fa-times-circle"></i></button>
                                    </form>
                                @endif
                            @endhasanyrole
                            <a href="{{ route('anuncios.edit', $anuncio->id) }}" class="text-slate-400 hover:text-orange-500 transition p-1.5 ml-2" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('anuncios.destroy', $anuncio->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este anuncio?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition p-1.5" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                        <i class="fa-solid fa-bullhorn text-4xl mb-3"></i>
                        <p>No hay anuncios registrados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($anuncios->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $anuncios->links() }}
    </div>
    @endif
</div>
@endsection
