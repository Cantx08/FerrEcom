<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FerreMax') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/1d2e493c85.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased bg-sky-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute -right-20 -top-20 opacity-10 transform -rotate-12">
                <i class="fa-solid fa-screwdriver-wrench text-[350px] text-white"></i>
            </div>
            <div class="absolute -left-20 -bottom-20 opacity-10 transform rotate-12">
                <i class="fa-solid fa-gear text-[300px] text-white"></i>
            </div>

            <div class="z-10 text-center mb-8">
                <a href="/" class="flex flex-col items-center gap-3 group">
                    <div class="bg-orange-600 p-2 rounded-xl shadow-lg transform group-hover:scale-105 transition">
                        <img src="{{ asset('logo.png') }}" class="h-16 w-auto" alt="FerreMax Logo">
                    </div>
                    <h1 class="font-extrabold text-white text-4xl tracking-tight px-2">Ferre<span class="text-orange-500 px-2">Max</span></h1>
                    <p class="text-sky-200 text-sm font-semibold tracking-wider uppercase">Portal de Acceso</p>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-8 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-2xl z-10 border border-white/20 backdrop-blur-sm">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-sky-200 text-sm font-semibold z-10">
                &copy; {{ date('Y') }} FerreMax S.A.
            </div>
        </div>
    </body>
</html>
