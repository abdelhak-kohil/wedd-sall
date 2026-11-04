<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Palais des Fêtes') }} - @yield('title', 'Connexion')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased selection:bg-emerald-500 selection:text-white">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <!-- Header & Language Switcher -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md flex justify-between items-center px-4 sm:px-0 mb-6 relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-900/40">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-white">Palais El Bahia</h1>
                    <p class="text-xs text-emerald-400 font-medium tracking-wide uppercase">Gestion de Salle des Fêtes</p>
                </div>
            </div>

            <!-- Language Switcher -->
            <div class="flex items-center gap-1 bg-slate-900/80 backdrop-blur border border-slate-800 rounded-lg p-1 text-xs">
                <a href="{{ route('lang.switch', 'fr') }}" class="px-2.5 py-1 rounded font-medium transition {{ app()->getLocale() === 'fr' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                    Français
                </a>
                <a href="{{ route('lang.switch', 'ar') }}" class="px-2.5 py-1 rounded font-medium font-arabic transition {{ app()->getLocale() === 'ar' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white' }}">
                    العربية
                </a>
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10 px-4 sm:px-0">
            @yield('content')
        </div>
    </div>
</body>
</html>
