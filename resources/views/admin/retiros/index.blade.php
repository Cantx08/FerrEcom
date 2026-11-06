@extends('layouts.admin')

@section('page_title', 'Retiros y Envíos')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-sky-900">Validar Retiro o Envío</h1>
        <p class="text-slate-500 mt-2">Ingresa el código proporcionado por el cliente para entregar su pedido.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <div>
                <p class="font-bold">¡Éxito!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
            <div>
                <p class="font-bold">Error</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-6 bg-orange-50 border border-orange-200 text-orange-700 px-6 py-4 rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            <div>
                <p class="font-bold">Atención</p>
                <p class="text-sm">{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 sm:p-12 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <i class="fa-solid fa-box-open text-9xl"></i>
        </div>
        
        <form action="{{ route('retiros.validar') }}" method="POST" class="relative z-10 max-w-sm mx-auto">
            @csrf
            <div class="mb-6">
                <label for="codigo_retiro" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Código de Retiro / Entrega</label>
                <input type="text" id="codigo_retiro" name="codigo_retiro" required placeholder="Ej: ONL-8A2F1" class="w-full text-center text-2xl tracking-widest font-mono bg-slate-50 border-2 border-slate-300 rounded-2xl px-4 py-4 outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20 transition-all uppercase placeholder-slate-300">
            </div>
            <button type="submit" class="w-full btn-cta text-white font-bold py-4 rounded-2xl shadow-lg transition-all flex justify-center items-center gap-2 text-lg">
                <i class="fa-solid fa-check-double"></i> Validar y Entregar
            </button>
        </form>
    </div>
</div>
@endsection
