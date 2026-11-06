<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FerreMax — @yield('title', '')</title>
    <meta name="description" content="FerreMax: tu ferretería online de confianza. Herramientas, construcción, plomería, eléctrico y más. Precios de mayorista, envío a todo Ecuador.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1d2e493c85.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .toast-container { z-index: 99999; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        /* Navbar blur effect */
        .navbar-blur {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        /* Product card image zoom */
        .product-img-wrap { overflow: hidden; }
        .product-img-wrap img { transition: transform 0.4s cubic-bezier(.25,.46,.45,.94); }
        .product-card:hover .product-img-wrap img { transform: scale(1.08); }
        /* Premium button glow */
        .btn-cta {
            background: #ea580c;
            box-shadow: 0 4px 20px rgba(234, 88, 12, 0.3);
            transition: all 0.2s ease;
        }
        .btn-cta:hover {
            background: #f97316;
            box-shadow: 0 6px 28px rgba(234, 88, 12, 0.45);
            transform: translateY(-1px);
        }
        /* Cart badge pulse */
        @keyframes cart-pulse {
            0%,100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        .cart-badge-anim { animation: cart-pulse 0.4s ease; }
        /* Hero gradient */
        .hero-gradient { background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 55%, #075985 100%); }
        /* Announcement card gradient */
        .promo-gradient { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen" x-data="cartComponent()">

    <!-- Main Navbar -->
    <header class="navbar-blur bg-white/85 border-b border-slate-200/60 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
            <div class="flex items-center gap-3 lg:gap-6 py-2">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 order-1 group">
                    <div class="p-1.5 rounded-xl border border-sky-900/10 shadow-sm bg-white group-hover:shadow-md transition-shadow">
                        <img class="h-12 sm:h-14 w-auto object-contain" src="{{ asset('logo.png') }}" alt="FerreMax">
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="font-black text-sky-900 text-xl leading-none">Ferre<span class="text-orange-600">Max</span></h1>
                    </div>
                </a>

                <!-- Search Bar -->
                <form action="{{ route('home') }}" method="GET" class="flex-1 order-2 max-w-2xl">
                    <div class="relative flex items-center group">
                        <div class="absolute left-3.5 text-slate-400 group-focus-within:text-orange-500 transition-colors">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </div>
                        <label class="sr-only" for="q">Buscar productos</label>
                        <input
                            id="q" name="q" value="{{ request('q') }}" type="text"
                            placeholder="Busca taladros, tornillos, cemento..."
                            class="w-full pl-10 pr-28 py-2.5 border-2 border-slate-200 focus:border-orange-500 focus:ring-0 rounded-xl text-sm bg-slate-50 focus:bg-white transition-all duration-200"
                        >
                        <button type="submit"
                            class="absolute right-1.5 btn-cta text-white font-bold text-xs px-4 py-1.5 rounded-lg">
                            Buscar
                        </button>
                    </div>
                </form>

                <!-- Actions: Login + Cart -->
                <div class="flex items-center gap-1 sm:gap-2 order-3 shrink-0">
                    @auth
                        @include('partials.notification-bell')

                        <div class="relative" x-data="{ userMenu: false }" @click.away="userMenu = false">
                            <button @click="userMenu = !userMenu" title="Mi cuenta"
                               class="flex flex-col items-center p-2 rounded-xl hover:bg-slate-100 text-slate-600 hover:text-orange-600 transition group focus:outline-none">
                                <div class="w-8 h-8 bg-sky-900 rounded-full flex items-center justify-center group-hover:bg-orange-600 transition-colors">
                                    <i class="fa-solid fa-user text-white text-xs"></i>
                                </div>
                                <span class="text-[10px] font-semibold mt-0.5 hidden sm:block">{{ strtok(Auth::user()->name, ' ') }}</span>
                            </button>
                            
                            {{-- Dropdown --}}
                            <div x-show="userMenu" x-transition.opacity.duration.200ms
                                 class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50" style="display: none;">
                                @include('partials.user-menu-dropdown')
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" title="Ingresar"
                           class="flex flex-col items-center p-2 rounded-xl hover:bg-slate-100 text-slate-600 hover:text-sky-900 transition group">
                            <i class="fa-solid fa-right-to-bracket text-xl group-hover:text-orange-600 transition-colors"></i>
                            <span class="text-[10px] font-semibold mt-0.5 hidden sm:block">Ingresar</span>
                        </a>
                    @endauth

                    <!-- Cart Button (solo Clientes) -->
                    @role('Cliente')
                    <button @click="open = true" title="Carrito"
                        class="relative flex flex-col items-center p-2 rounded-xl hover:bg-slate-100 text-slate-600 hover:text-orange-600 transition group">
                        <i class="fa-solid fa-cart-shopping text-xl group-hover:text-orange-600 transition-colors"></i>
                        <span class="text-[10px] font-semibold mt-0.5 hidden sm:block">Carrito</span>
                        <span x-show="count > 0" x-text="count"
                            class="absolute -top-0.5 -right-0.5 bg-orange-500 text-white text-[9px] font-black min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1 shadow-md border-2 border-white">
                        </span>
                    </button>
                    @endrole

                </div>

            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-sky-950 text-slate-400 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- Brand -->
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-1.5 rounded-xl bg-white/10 border border-white/10">
                        <img src="{{ asset('logo.png') }}" class="h-10 w-auto" alt="FerreMax">
                    </div>
                    <span class="font-black text-white text-xl">Ferre<span class="text-orange-500">Max</span></span>
                </div>
                <p class="text-sm leading-relaxed max-w-xs text-slate-400">
                    Tu ferretería de confianza desde 1998. Gran variedad de productos para construcción, hogar e industria a precios de mayorista.
                </p>
                <div class="flex items-center gap-3 mt-5">
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-orange-600 rounded-lg flex items-center justify-center transition-colors text-sm"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-orange-600 rounded-lg flex items-center justify-center transition-colors text-sm"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-8 h-8 bg-white/10 hover:bg-orange-600 rounded-lg flex items-center justify-center transition-colors text-sm"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <!-- Links -->
            <div>
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Categorías</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home', ['cat' => 'Herramientas']) }}" class="hover:text-orange-400 transition-colors">Herramientas</a></li>
                    <li><a href="{{ route('home', ['cat' => 'Construcción']) }}" class="hover:text-orange-400 transition-colors">Construcción</a></li>
                    <li><a href="{{ route('home', ['cat' => 'Eléctrico']) }}" class="hover:text-orange-400 transition-colors">Eléctrico</a></li>
                    <li><a href="{{ route('home', ['cat' => 'Plomería']) }}" class="hover:text-orange-400 transition-colors">Plomería</a></li>
                    <li><a href="{{ route('home', ['cat' => 'Pinturas']) }}" class="hover:text-orange-400 transition-colors">Pinturas</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div>
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Contacto</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-phone w-4 text-orange-500"></i>
                        <span>(04) 380-2200</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-solid fa-envelope w-4 text-orange-500"></i>
                        <span>ventas@ferremax.ec</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <i class="fa-solid fa-location-dot w-4 text-orange-500 mt-0.5"></i>
                        <span>Av. Juan Tanca Marengo Km 1.5, Guayaquil</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i class="fa-regular fa-clock w-4 text-orange-500"></i>
                        <span>Lun–Sáb: 8:00–19:00</span>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Bottom Bar -->
        <div class="border-t border-white/5 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-500">
                <span>&copy; {{ date('Y') }} FerreMax S.A. · Todos los derechos reservados.</span>
                <div class="flex items-center gap-3">
                    <i class="fa-brands fa-cc-visa text-xl opacity-60"></i>
                    <i class="fa-brands fa-cc-mastercard text-xl opacity-60"></i>
                    <i class="fa-brands fa-cc-amex text-xl opacity-60"></i>
                    <i class="fa-solid fa-shield-halved text-green-500 opacity-80"></i>
                    <span class="text-green-600 font-semibold">Pago Seguro</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Nav -->
    <nav class="fixed bottom-0 inset-x-0 z-40 sm:hidden bg-white/90 navbar-blur border-t border-slate-200 flex justify-around items-center py-2 px-4 shadow-xl">
        <a href="{{ route('home') }}" class="flex flex-col items-center text-slate-500 hover:text-orange-600 transition text-[10px] font-semibold gap-1">
            <i class="fa-solid fa-house text-lg"></i>Inicio
        </a>
        <a href="{{ route('home', ['cat' => 'Ofertas']) }}" class="flex flex-col items-center text-slate-500 hover:text-orange-600 transition text-[10px] font-semibold gap-1">
            <i class="fa-solid fa-percent text-lg"></i>Ofertas
        </a>
        @auth
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-slate-500 hover:text-orange-600 transition text-[10px] font-semibold gap-1">
                <i class="fa-solid fa-user text-lg"></i>Mi Cuenta
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center text-slate-500 hover:text-orange-600 transition text-[10px] font-semibold gap-1">
                <i class="fa-solid fa-right-to-bracket text-lg"></i>Ingresar
            </a>
        @endauth
    </nav>

    <!-- Cart Drawer (solo Clientes) -->
    @role('Cliente')
        @include('tienda.partials.cart_drawer')
    @endrole

    @stack('scripts')
</body>
</html>
