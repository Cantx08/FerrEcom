<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-sky-900">Crear Cuenta</h2>
            <p class="text-sm text-slate-500 mt-1">Únete a FerreMax y empieza a comprar</p>
        </div>

        <!-- Name -->
        <div>
            <label for="name" class="block font-medium text-sm text-slate-700 mb-1">Nombre Completo</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-user text-slate-400"></i>
                </div>
                <input id="name" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ej. Juan Pérez">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block font-medium text-sm text-slate-700 mb-1">Correo Electrónico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-envelope text-slate-400"></i>
                </div>
                <input id="email" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="tu@correo.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-slate-700 mb-1">Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-slate-400"></i>
                </div>
                <input id="password" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-slate-700 mb-1">Confirmar Contraseña</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-slate-400"></i>
                </div>
                <input id="password_confirmation" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
        </div>

        <div class="mt-8">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition">
                Registrarse <i class="fa-solid fa-user-plus ml-2 mt-0.5"></i>
            </button>
        </div>
        
        <div class="mt-6 text-center text-sm text-slate-500">
            ¿Ya tienes cuenta? 
            <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-700 hover:underline transition">Inicia sesión aquí</a>
        </div>
    </form>
</x-guest-layout>
