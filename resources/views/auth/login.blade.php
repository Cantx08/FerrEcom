<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-sky-900">Iniciar Sesión</h2>
            <p class="text-sm text-slate-500 mt-1">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-slate-700 mb-1">Correo Electrónico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-envelope text-slate-400"></i>
                </div>
                <input id="email" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="tu@correo.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <label for="password" class="block font-medium text-sm text-slate-700 mb-1">Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-slate-400"></i>
                </div>
                <input id="password" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4 flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Recuérdame</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-sky-700 hover:text-orange-600 rounded-md focus:outline-none transition" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition">
                Ingresar <i class="fa-solid fa-arrow-right-to-bracket ml-2 mt-0.5"></i>
            </button>
        </div>
        
        <div class="mt-6 text-center text-sm text-slate-500">
            ¿No tienes cuenta? 
            <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700 hover:underline transition">Regístrate aquí</a>
        </div>
    </form>
</x-guest-layout>
