@extends('layouts.admin')

@section('page_title', 'Inventario de Productos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h3 class="font-black text-slate-800 text-2xl">Catálogo Interno</h3>
            <p class="text-sm text-slate-500 mt-1">Gestiona los productos disponibles para la venta y controla el stock.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative hidden sm:block">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                <input type="text" placeholder="Buscar producto..." class="pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-sky-500 focus:border-sky-500 w-64 shadow-sm transition">
            </div>
            <a href="{{ route('productos.create') }}" class="btn-cta text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition whitespace-nowrap flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nuevo Producto
            </a>
        </div>
    </div>

    @if(isset($lowStockSuppliers) && count($lowStockSuppliers) > 0)
        <div class="mb-8 bg-orange-50 border-l-4 border-orange-500 rounded-r-xl p-5 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="text-orange-500 mt-1">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-orange-800 text-lg mb-1">Productos con Bajo Stock</h3>
                    <p class="text-orange-700 text-sm mb-4">Los siguientes proveedores tienen los productos que necesitas reabastecer:</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($lowStockSuppliers as $supplierData)
                            <div class="bg-white p-3 rounded-lg border border-orange-200 flex items-center justify-between shadow-sm">
                                <div>
                                    <p class="font-bold text-slate-700 text-sm truncate" title="{{ $supplierData['proveedor']->nombre_compania }}">
                                        {{ $supplierData['proveedor']->nombre_compania }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $supplierData['productos_count'] }} productos con bajo stock</p>
                                </div>
                                <a href="{{ route('pedidos.create', ['proveedor_id' => $supplierData['proveedor_id'], 'low_stock' => 1]) }}" class="shrink-0 bg-orange-100 hover:bg-orange-200 text-orange-700 p-2 rounded-lg transition" title="Generar Pedido">
                                    <i class="fa-solid fa-cart-arrow-down"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @php
        $stats = \App\Models\Producto::selectRaw('
            COUNT(*) as total, 
            SUM(CASE WHEN stock_actual < 10 AND stock_actual > 0 THEN 1 ELSE 0 END) as low_stock, 
            SUM(CASE WHEN stock_actual = 0 THEN 1 ELSE 0 END) as out_stock
        ')->first();
    @endphp

    {{-- Dashboard KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-boxes-stacked text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Productos</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $stats->total ?? 0 }}</h4>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Stock Bajo</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $stats->low_stock ?? 0 }}</h4>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-box-open text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Agotados</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $stats->out_stock ?? 0 }}</h4>
            </div>
        </div>
    </div>

    @if($productos->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-box-open text-5xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">No hay productos</h3>
            <p class="text-slate-500 max-w-md mx-auto mb-6">Aún no se han registrado productos en el catálogo interno.</p>
            <a href="{{ route('productos.create') }}" class="btn-cta text-white px-6 py-3 rounded-xl font-bold inline-flex items-center gap-2 shadow-sm transition">
                <i class="fa-solid fa-plus"></i> Agregar el primer producto
            </a>
        </div>
    @else
        <div data-testid="productos-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($productos as $p)
                <div data-testid="producto-item-{{ $p->id }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col group">
                    <div class="relative bg-slate-50 aspect-video flex items-center justify-center p-4 border-b border-slate-100 overflow-hidden">
                        @if($p->imagen)
                            <img src="{{ asset($p->imagen) }}" class="max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        @else
                            <i class="fa-solid fa-box text-6xl text-slate-300 group-hover:scale-110 transition-transform duration-500"></i>
                        @endif
                        
                        {{-- Badges --}}
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if($p->stock_actual <= 0)
                                <span class="bg-red-100 text-red-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-sm">
                                    Agotado
                                </span>
                            @elseif($p->stock_actual < 10)
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest shadow-sm">
                                    Stock Bajo
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div>
                                <span class="text-[10px] font-bold text-sky-600 uppercase tracking-widest mb-1 block">{{ $p->categoria->nombre ?? 'Sin categoría' }}</span>
                                <h4 class="font-bold text-slate-800 text-lg leading-tight line-clamp-2">{{ $p->nombre }}</h4>
                            </div>
                        </div>
                        
                        <p class="text-xs text-slate-400 font-mono mb-4">SKU: {{ $p->sku }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Precio</p>
                                <p class="font-black text-xl text-slate-800">${{ number_format($p->precio_venta, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Stock</p>
                                <p class="font-black text-lg {{ $p->stock_actual <= 0 ? 'text-red-500' : ($p->stock_actual < 10 ? 'text-amber-500' : 'text-emerald-500') }}">{{ $p->stock_actual }} <span class="text-xs text-slate-400 font-medium">unidades</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a data-testid="btn-edit-{{ $p->id }}" href="{{ route('productos.edit', $p->id) }}" class="flex-1 bg-white border border-slate-200 hover:border-sky-300 hover:text-sky-600 hover:bg-sky-50 text-slate-600 font-bold text-sm px-4 py-2 rounded-xl transition-all text-center">
                            <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                        </a>
                        <form action="{{ route('productos.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-white border border-slate-200 hover:border-red-300 hover:text-red-600 hover:bg-red-50 text-slate-600 font-bold text-sm w-10 h-10 flex items-center justify-center rounded-xl transition-all">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $productos->links() }}
        </div>
    @endif
</div>
@endsection
