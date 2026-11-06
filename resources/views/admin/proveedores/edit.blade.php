@extends('layouts.admin')

@section('page_title', 'Editar Proveedor')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('proveedores.index') }}" class="text-sky-600 hover:text-sky-700 text-sm font-bold flex items-center gap-2 mb-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Volver a proveedores
        </a>
        <h3 class="font-black text-slate-800 text-2xl">Editar Proveedor</h3>
    </div>

    <form action="{{ route('proveedores.update', $proveedor->id) }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block font-bold text-xs text-slate-500 uppercase tracking-wider mb-2">Nombre de Compañía <span class="text-red-500">*</span></label>
                <input type="text" name="nombre_compania" value="{{ old('nombre_compania', $proveedor->nombre_compania) }}" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 transition">
                @error('nombre_compania') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-bold text-xs text-slate-500 uppercase tracking-wider mb-2">RUC <span class="text-red-500">*</span></label>
                <input type="text" name="ruc" value="{{ old('ruc', $proveedor->ruc) }}" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 transition">
                @error('ruc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-bold text-xs text-slate-500 uppercase tracking-wider mb-2">Teléfono <span class="text-red-500">*</span></label>
                <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" required class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 transition">
                @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block font-bold text-xs text-slate-500 uppercase tracking-wider mb-2">Dirección <span class="text-red-500">*</span></label>
                <textarea name="direccion" required rows="3" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 transition">{{ old('direccion', $proveedor->direccion) }}</textarea>
                @error('direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex justify-end">
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-save mr-2"></i> Actualizar Proveedor
            </button>
        </div>
    </form>
</div>
@endsection
