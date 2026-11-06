<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - FerreMax</title>
    <script src="https://kit.fontawesome.com/1d2e493c85.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- DataTables CDN -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .navbar-blur {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen overflow-x-hidden" x-data="cartComponent()">
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

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

                        <!-- User Profile -->
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

        <main class="flex-1 overflow-y-auto bg-slate-50">
            @yield('content')
        </main>
    </div>
    
    <!-- Cart Drawer (solo Clientes) -->
    @role('Cliente')
        @include('tienda.partials.cart_drawer')
    @endrole

    @stack('scripts')
</body>
</html>
