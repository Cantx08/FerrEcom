@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

{{-- ─── Category Navigation Strip ─────────────────────────────────────────── --}}
<nav class="bg-white border-b border-slate-200 sticky top-[73px] z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-0.5 overflow-x-auto scrollbar-hide py-0.5">
            @php
                $navItems = [
                    ['label' => '⭐ Destacados', 'value' => 'Destacados'],
                    ['label' => '🔥 Ofertas',    'value' => 'Ofertas'],
                ];
            @endphp
            @foreach(array_merge($navItems, $categorias->map(fn($c) => ['label' => $c->nombre, 'value' => $c->nombre])->toArray()) as $item)
                <a href="{{ route('home', ['cat' => $item['value']]) }}"
                   class="whitespace-nowrap px-4 py-3 text-sm font-semibold border-b-2 transition-all duration-150
                   {{ $cat == $item['value']
                       ? 'border-orange-500 text-orange-600 bg-orange-50/60'
                       : 'border-transparent text-slate-500 hover:text-sky-900 hover:border-sky-200' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24 sm:pb-8">

    {{-- ─── Hero Section (solo en Destacados sin búsqueda) ──────────────── --}}
    @if(!$q && $cat === 'Destacados')
    <section class="relative overflow-hidden rounded-3xl mb-10 hero-gradient text-white shadow-2xl min-h-[320px] flex items-center">
        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-16 -top-16 opacity-[0.07]">
                <i class="fa-solid fa-screwdriver-wrench text-[400px]"></i>
            </div>
            <div class="absolute right-1/4 bottom-0 opacity-[0.05]">
                <i class="fa-solid fa-gear text-[200px]"></i>
            </div>
            {{-- Subtle dot grid --}}
            <div class="absolute inset-0" style="background-image:radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);background-size:24px 24px;"></div>
        </div>

        <div class="relative z-10 px-8 py-14 sm:px-14 sm:py-16 lg:py-20 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 w-full">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/25 rounded-full px-4 py-1.5 text-sm font-bold mb-6">
                    <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                    Temporada de construcción activa
                </div>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-[1.1] tracking-tight mb-5">
                    Todo para tu<br>
                    <span class="text-orange-400">obra</span> y tu <span class="text-orange-400">hogar</span>
                </h2>
                <p class="text-sky-100/90 text-lg leading-relaxed mb-8 max-w-lg">
                    Herramientas, materiales y accesorios de las mejores marcas.<br class="hidden sm:block">
                    Precios de mayorista, envío a domicilio o retiro en tienda.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('home', ['cat' => 'Ofertas']) }}"
                       class="btn-cta text-white px-7 py-3.5 rounded-xl font-bold text-base">
                        Ver ofertas del mes <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                    <a href="{{ route('home', ['cat' => 'Herramientas']) }}"
                       class="bg-white/15 hover:bg-white/25 backdrop-blur-sm border border-white/30 text-white px-7 py-3.5 rounded-xl font-bold text-base transition-all">
                        Ver catálogo
                    </a>
                </div>
            </div>
            {{-- Stats --}}
            <div class="hidden lg:flex flex-col gap-4 shrink-0">
                @php $stats = [['num'=>'12K+','label'=>'Productos'],['num'=>'3K+','label'=>'Clientes'],['num'=>'98%','label'=>'Satisfacción'],['num'=>'24h','label'=>'Despacho']]; @endphp
                @foreach($stats as $s)
                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl px-6 py-4 text-center min-w-[120px]">
                    <div class="text-3xl font-black text-white">{{ $s['num'] }}</div>
                    <div class="text-sky-200 text-xs font-semibold mt-0.5">{{ $s['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── Announcements ──────────────────────────────────────────────────── --}}
    @if($anuncios->count() > 0)
        <div class="mb-10 relative" x-data="{ activeSlide: 0, slides: {{ $anuncios->count() }} }" x-init="setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000)">
            <div class="overflow-hidden rounded-2xl relative shadow-md">
                <div class="flex transition-transform duration-500 ease-in-out" :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                    @foreach($anuncios as $anuncio)
                        <div class="w-full shrink-0 promo-gradient border border-orange-200/60 p-8 sm:p-12 flex flex-col justify-center relative overflow-hidden group min-h-[220px]">
                            <div class="relative z-10">
                                <span class="inline-block bg-orange-100 text-orange-700 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-widest mb-3">Promoción</span>
                                <h3 class="text-2xl font-extrabold text-sky-900 mb-2">{{ $anuncio->titulo }}</h3>
                                <p class="text-slate-600 text-base mb-5 max-w-[65%] leading-relaxed">{{ $anuncio->contenido }}</p>
                                <span class="inline-flex items-center gap-2 bg-orange-600 text-white font-bold text-sm px-5 py-2 rounded-xl cursor-pointer hover:bg-orange-700 transition-colors shadow-sm">
                                    Aprovechar <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </div>
                            @if($anuncio->imagen)
                                <img src="{{ asset('storage/'.$anuncio->imagen) }}" class="absolute right-0 top-0 h-full w-1/3 object-cover opacity-80" style="mask-image: linear-gradient(to left, rgba(0,0,0,1) 50%, rgba(0,0,0,0) 100%); -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 50%, rgba(0,0,0,0) 100%);">
                            @else
                                <i class="fa-solid fa-tags text-[180px] text-orange-200 absolute -right-10 top-1/2 transform -translate-y-1/2 -rotate-12"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                {{-- Carousel Controls --}}
                @if($anuncios->count() > 1)
                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-20">
                    <template x-for="i in slides" :key="i">
                        <button @click="activeSlide = i - 1" class="w-2.5 h-2.5 rounded-full transition-all" :class="activeSlide === i - 1 ? 'bg-orange-600 w-6' : 'bg-orange-300 hover:bg-orange-400'"></button>
                    </template>
                </div>
                <!-- Prev/Next buttons (optional for desktop) -->
                <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/50 hover:bg-white text-orange-600 w-10 h-10 rounded-full flex items-center justify-center shadow-sm backdrop-blur transition-all hidden sm:flex">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/50 hover:bg-white text-orange-600 w-10 h-10 rounded-full flex items-center justify-center shadow-sm backdrop-blur transition-all hidden sm:flex">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                @endif
            </div>
        </div>
    @endif
    @endif

    {{-- ─── Trust Badges (solo en Destacados) ─────────────────────────────── --}}
    @if(!$q && $cat === 'Destacados')
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        @php
            $badges = [
                ['icon'=>'fa-truck-fast',     'color'=>'sky',    'title'=>'Envío rápido',      'desc'=>'24–48h a todo Ecuador'],
                ['icon'=>'fa-store',          'color'=>'orange', 'title'=>'Retiro en tienda',  'desc'=>'Listo en 5 minutos'],
                ['icon'=>'fa-credit-card',    'color'=>'sky',    'title'=>'Pago 100% seguro',  'desc'=>'Visa, Mastercard'],
                ['icon'=>'fa-shield-halved',  'color'=>'orange', 'title'=>'Garantía oficial',  'desc'=>'Productos 100% originales'],
            ];
        @endphp
        @foreach($badges as $b)
        <div class="bg-white border border-slate-200/60 rounded-2xl p-4 sm:p-5 flex items-center gap-3 sm:gap-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0
                {{ $b['color'] === 'sky' ? 'bg-sky-50 text-sky-800' : 'bg-orange-50 text-orange-600' }}">
                <i class="fa-solid {{ $b['icon'] }} text-lg"></i>
            </div>
            <div class="min-w-0">
                <h4 class="font-bold text-slate-800 text-sm leading-tight">{{ $b['title'] }}</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-tight">{{ $b['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ─── Catalog Header ─────────────────────────────────────────────────── --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-extrabold text-sky-900 leading-tight">
                @if($q)
                    Resultados para <span class="text-orange-600">"{{ $q }}"</span>
                @else
                    {{ $cat === 'Destacados' ? 'Productos Destacados' : $cat }}
                @endif
            </h2>
            @if($q)
                <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-orange-600 transition mt-0.5 inline-flex items-center gap-1">
                    <i class="fa-solid fa-xmark"></i> Limpiar búsqueda
                </a>
            @endif
        </div>
        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
            {{ $productos->count() }} producto{{ $productos->count() !== 1 ? 's' : '' }}
        </span>
    </div>

    {{-- ─── Empty State ─────────────────────────────────────────────────────── --}}
    @if($productos->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fa-regular fa-face-frown text-4xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 mb-2">Sin resultados</h3>
            <p class="text-slate-500 text-sm max-w-xs mx-auto mb-6">
                No encontramos productos para "{{ $q ?? $cat }}". Prueba otra búsqueda o revisa otras categorías.
            </p>
            <a href="{{ route('home') }}" class="btn-cta inline-flex items-center gap-2 text-white font-bold px-6 py-2.5 rounded-xl text-sm">
                <i class="fa-solid fa-house"></i> Ver todos los productos
            </a>
        </div>

    {{-- ─── Products Grid ───────────────────────────────────────────────────── --}}
    @else
        <div data-testid="productos-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-5">
            @foreach($productos as $p)
                @php
                    $agotado    = $p->stock_actual <= 0;
                    $bajoStock  = $p->stock_actual > 0 && $p->stock_actual <= $p->stock_minimo;
                    $enOferta   = $p->precio_compra < $p->precio_venta * 0.7;
                @endphp

                <div data-testid="producto-{{ $p->id }}" class="product-card bg-white border border-slate-200/70 rounded-2xl overflow-hidden flex flex-col
                            hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative group shadow-sm">

                    {{-- Badges --}}
                    <div class="absolute top-2.5 left-2.5 z-10 flex flex-col gap-1.5">
                        @if($enOferta)
                            <span class="bg-red-500 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-sm uppercase tracking-wider">
                                ¡OFERTA!
                            </span>
                        @endif
                        @if($cat === 'Destacados')
                            <span class="bg-sky-900 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-sm uppercase tracking-wider">
                                TOP
                            </span>
                        @endif
                        @if($bajoStock)
                            <span class="bg-amber-400 text-amber-900 text-[9px] font-black px-2 py-0.5 rounded-md shadow-sm uppercase tracking-wider">
                                ÚLTIMAS
                            </span>
                        @endif
                    </div>

                    {{-- Image --}}
                    <div class="product-img-wrap h-44 sm:h-48 bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center p-4 relative">
                        @if($p->imagen)
                            <img src="{{ asset($p->imagen) }}" alt="{{ $p->nombre }}"
                                 class="max-h-full max-w-full object-contain mix-blend-multiply drop-shadow-sm">
                        @else
                            <div class="flex flex-col items-center text-slate-300 group-hover:text-slate-400 transition-colors">
                                <i class="fa-solid fa-box text-5xl mb-2"></i>
                                <span class="text-xs font-semibold">Sin imagen</span>
                            </div>
                        @endif

                        @if($agotado)
                            <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px] flex items-center justify-center rounded-t-2xl">
                                <span class="bg-slate-800 text-white font-black px-4 py-1.5 rounded-lg shadow-lg rotate-[-8deg] uppercase tracking-widest text-xs">
                                    Agotado
                                </span>
                            </div>
                        @endif

                        {{-- Quick action on hover (desktop) --}}
                        @if(!$agotado)
                        <div class="absolute bottom-3 inset-x-3 opacity-0 group-hover:opacity-100 transition-all duration-200 translate-y-2 group-hover:translate-y-0 hidden sm:block">
                            @auth
                                @role('Cliente')
                                    <button
                                        data-testid="btn-add-{{ $p->id }}"
                                        @click="addToCart({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->precio_venta }}, {{ $p->stock_actual }}, '{{ $p->imagen }}', {{ $p->cantidad_por_mayor ?? 'null' }}, {{ $p->descuento_por_mayor ?? 'null' }})"
                                        class="w-full btn-cta text-white text-xs font-bold py-2 rounded-xl flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-cart-plus"></i> Añadir al carrito
                                    </button>
                                @endrole
                            @else
                                <a href="{{ route('login') }}" class="w-full bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2 rounded-xl flex items-center justify-center gap-1.5 transition-colors">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Inicia sesión
                                </a>
                            @endauth
                        </div>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4 flex flex-col flex-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 block">
                            {{ $p->categoria->nombre }}
                        </span>
                        <h3 class="text-sm font-semibold text-slate-800 leading-snug mb-2 min-h-[36px] line-clamp-2" title="{{ $p->nombre }}">
                            {{ $p->nombre }}
                        </h3>

                        {{-- Stars --}}
                        <div class="flex items-center gap-1 mb-3">
                            <div class="flex text-amber-400 text-[10px]">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                            </div>
                            <span class="text-[10px] text-slate-400 font-semibold">(4.8)</span>
                        </div>

                        <div class="mt-auto pt-2 border-t border-slate-100 flex flex-col gap-2.5">
                            {{-- Price --}}
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xl font-black text-sky-900">${{ number_format($p->precio_venta, 2) }}</span>
                                @if($enOferta)
                                    <span class="text-xs text-slate-400 line-through">${{ number_format($p->precio_venta * 1.15, 2) }}</span>
                                @endif
                            </div>

                            {{-- Stock Status --}}
                            <div class="flex items-center gap-1 text-[10px] font-bold">
                                @if($agotado)
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span class="text-red-600">Sin stock</span>
                                @elseif($bajoStock)
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    <span class="text-amber-600">¡Últimas {{ $p->stock_actual }} unidades!</span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-emerald-600">En stock</span>
                                @endif
                            </div>

                            {{-- Add to Cart (mobile always visible, desktop uses the hover button on image) — solo Clientes --}}
                            @auth
                                @role('Cliente')
                                    <button
                                        data-testid="btn-add-mobile-{{ $p->id }}"
                                        @if($agotado) disabled @endif
                                        @click="{{ !$agotado ? 'addToCart('.$p->id.', \''.addslashes($p->nombre).'\', '.$p->precio_venta.', '.$p->stock_actual.', \''.$p->imagen.'\', '.($p->cantidad_por_mayor ?? 'null').', '.($p->descuento_por_mayor ?? 'null').')' : '' }}"
                                        class="w-full py-2.5 rounded-xl font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5 sm:hidden
                                        {{ $agotado ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'btn-cta text-white' }}">
                                        @if($agotado)
                                            <i class="fa-solid fa-ban"></i> Agotado
                                        @else
                                            <i class="fa-solid fa-cart-plus"></i> Agregar
                                        @endif
                                    </button>
                                @endrole
                            @else
                                <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5 sm:hidden bg-slate-800 text-white hover:bg-slate-900">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Ingresar
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

