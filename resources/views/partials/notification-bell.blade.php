{{-- Campana de notificaciones: visible para cualquier usuario autenticado, sin importar su rol --}}
<div class="relative" x-data="{ notifMenu: false }" @click.away="notifMenu = false">
    <button @click="notifMenu = !notifMenu" title="Notificaciones"
       class="relative flex flex-col items-center p-2 rounded-xl hover:bg-slate-100 text-slate-600 hover:text-orange-600 transition group focus:outline-none">
        <i class="fa-solid fa-bell text-lg"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
        @endif
    </button>

    {{-- Dropdown Notificaciones --}}
    <div x-show="notifMenu" x-transition.opacity.duration.200ms
         class="absolute right-0 mt-2 w-72 sm:w-80 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 overflow-hidden" style="display: none;">
        <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <p class="text-xs font-bold text-slate-800">Notificaciones</p>
        </div>
        <div class="max-h-64 overflow-y-auto">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 border-b border-slate-50 hover:bg-sky-50 transition-colors">
                    <p class="text-xs text-slate-700 font-medium">{{ $notification->data['message'] ?? 'Nueva notificación' }}</p>
                    @if(isset($notification->data['estado']))
                        <span class="text-[10px] uppercase font-bold text-sky-600 mt-1 inline-block">{{ $notification->data['estado'] }}</span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-6 text-center text-slate-500 text-xs">
                    No tienes nuevas notificaciones
                </div>
            @endforelse
        </div>
    </div>
</div>
