{{--
    Menú desplegable de cuenta de usuario, compartido por layouts.admin y layouts.app.
    Antes existían dos copias de este bloque que fueron divergiendo: la de
    layouts.admin dejó de incluir SuperAdmin en la mayoría de sus @hasanyrole,
    y la de layouts.app no tenía el enlace de Retiros y Envíos. Un solo
    partial evita que vuelvan a desincronizarse.
--}}
<div class="px-4 py-2 border-b border-slate-100 mb-1">
    <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
    <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
</div>

@role('Cliente')
<a href="{{ route('cliente.compras') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
    <i class="fa-solid fa-box-open w-5 text-center text-slate-400"></i> Mis Compras
</a>
@endrole

<a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
    <i class="fa-solid fa-id-card w-5 text-center text-slate-400"></i> Mi Perfil
</a>

@hasanyrole('SuperAdmin|Administrador|Bodeguero|Supervisor|Vendedor')
    <div class="border-t border-slate-100 my-1"></div>
    <div x-data="{ openAdmin: false }" @mouseenter="openAdmin = true" @mouseleave="openAdmin = false" class="w-full block">
        <button @click.prevent="openAdmin = !openAdmin" type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
            <div class="flex items-center pointer-events-none">
                <i class="fa-solid fa-screwdriver-wrench w-5 text-center text-slate-400"></i>
                <span class="ml-1">Administración</span>
            </div>
            <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200 pointer-events-none" :class="{'rotate-90': openAdmin}"></i>
        </button>
        <div x-show="openAdmin" x-collapse class="bg-slate-50 border-y border-slate-100 w-full block">
            @hasanyrole('SuperAdmin|Administrador')
                <a href="{{ route('dashboard') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Estadísticas
                </a>
                <a href="{{ route('roles.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Usuarios y Roles
                </a>
            @endhasanyrole
            @hasanyrole('Bodeguero|Administrador|SuperAdmin')
                <a href="{{ route('productos.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Productos
                </a>
                <a href="{{ route('pedidos.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Pedidos de Compra
                </a>
            @endhasanyrole
            @hasanyrole('Vendedor|Administrador|SuperAdmin')
                <a href="{{ route('ventas.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Ventas
                </a>
                <a href="{{ route('retiros.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Retiros y Envíos
                </a>
            @endhasanyrole
            @hasanyrole('Supervisor|Administrador|SuperAdmin')
                <a href="{{ route('anuncios.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                    Anuncios
                </a>
            @endhasanyrole
        </div>
    </div>
@endhasanyrole

@role('Proveedor')
    <div class="border-t border-slate-100 my-1"></div>
    <div x-data="{ openProv: false }" @mouseenter="openProv = true" @mouseleave="openProv = false" class="w-full block">
        <button @click.prevent="openProv = !openProv" type="button" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
            <div class="flex items-center pointer-events-none">
                <i class="fa-solid fa-truck w-5 text-center text-slate-400"></i>
                <span class="ml-1">Mi Portal</span>
            </div>
            <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200 pointer-events-none" :class="{'rotate-90': openProv}"></i>
        </button>
        <div x-show="openProv" x-collapse class="bg-slate-50 border-y border-slate-100 w-full block">
            <a href="{{ route('proveedor.pedidos.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                Mis Pedidos
            </a>
            <a href="{{ route('proveedor.catalogo.index') }}" class="block px-4 pl-10 py-2.5 text-sm font-medium text-slate-600 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                Mi Catálogo
            </a>
        </div>
    </div>
@endrole

<div class="border-t border-slate-100 my-1"></div>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" onclick="localStorage.removeItem('ferremax_cart')" class="w-full text-left px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center text-red-400"></i> Cerrar Sesión
    </button>
</form>
