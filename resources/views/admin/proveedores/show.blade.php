@extends('layouts.admin')

@section('page_title', $proveedor->nombre_compania)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="font-black text-slate-800 text-2xl">{{ $proveedor->nombre_compania }}</h3>
            <p class="text-sm text-slate-500 mt-1">RUC: {{ $proveedor->ruc }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('proveedores.edit', $proveedor->id) }}" class="bg-white border border-slate-200 text-slate-600 hover:text-sky-700 hover:border-sky-200 font-bold text-sm px-4 py-2.5 rounded-xl transition-all shadow-sm">
                <i class="fa-solid fa-pen-to-square mr-1.5"></i> Editar
            </a>
            <a href="{{ route('proveedores.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                <i class="fa-solid fa-chevron-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider">Datos de Contacto</h4>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-0.5">Correo</dt>
                <dd class="text-slate-700">{{ $proveedor->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-0.5">Teléfono</dt>
                <dd class="text-slate-700">{{ $proveedor->telefono }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-0.5">Dirección</dt>
                <dd class="text-slate-700">{{ $proveedor->direccion }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Catálogo Ofrecido</h4>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($proveedor->productoProveedor as $item)
                <div class="flex items-center justify-between px-6 py-3 text-sm">
                    <div>
                        <p class="font-semibold text-slate-700">{{ $item->nombre }}</p>
                        <p class="text-xs text-slate-500">{{ $item->unidad ?? 'unidad' }}</p>
                    </div>
                    <span class="font-bold text-sky-900">${{ number_format($item->costo_unitario, 2) }}</span>
                </div>
            @empty
                <p class="px-6 py-8 text-center text-slate-400 text-sm">Este proveedor aún no tiene productos en su catálogo.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
