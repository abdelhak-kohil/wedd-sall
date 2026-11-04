<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Palais des Fêtes') }} - @yield('title', 'Gestion')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased selection:bg-emerald-500 selection:text-white flex overflow-hidden">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900/95 border-r rtl:border-r-0 rtl:border-l border-slate-800/80 flex flex-col shrink-0 z-30 transition-all">
        <!-- Logo & Hall Info -->
        <div class="h-16 px-5 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-md shadow-emerald-950">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-sm text-white truncate w-36">
                        {{ auth()->user()->business->name_fr ?? 'Palais El Bahia' }}
                    </h1>
                    <p class="text-[11px] text-emerald-400 font-medium tracking-wide">
                        {{ auth()->user()->business->wilaya ?? 'Alger' }} · DA (DZD)
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-xs font-medium">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-semibold shadow-md shadow-emerald-950' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'لوحة القيادة' : 'Tableau de bord' }}</span>
            </a>

            <!-- Quick Availability Screen (Star Feature) -->
            <a href="{{ route('availability.index') }}"
                class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('availability.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-emerald-400 hover:bg-emerald-950/40' }} border border-emerald-900/50 bg-emerald-950/20">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'فحص التوفر السريع' : 'Disponibilité Rapide' }}</span>
                </div>
                <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded font-mono">Alt+A</span>
            </a>

            <!-- Calendar -->
            <a href="{{ route('calendar.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('calendar.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'روزنامة الحجوزات' : 'Calendrier des Salles' }}</span>
            </a>

            <!-- Section: Réservations & Clients -->
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'الحجوزات والزبائن' : 'Réservations & Clients' }}
                </p>
            </div>

            <!-- Reservations -->
            <a href="{{ route('reservations.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('reservations.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'قائمة الحجوزات' : 'Toutes les Réservations' }}</span>
            </a>

            <!-- Customers -->
            <a href="{{ route('customers.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('customers.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'دليل الزبائن' : 'Fichier Clients' }}</span>
            </a>


            <!-- Quotations -->
            <a href="{{ route('quotations.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('quotations.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'عروض الأسعار (الديسكاونت)' : 'Devis & Estimations' }}</span>
            </a>


            <!-- Section: Finances (Owner, Manager, Accountant) -->
            @if(auth()->user()->isOwner() || auth()->user()->isManager() || auth()->user()->isAccountant())
                <div class="pt-3 pb-1">
                    <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        {{ app()->getLocale() === 'ar' ? 'المالية والصندوق' : 'Finances & Caisse' }}
                    </p>
                </div>

                <!-- Payments -->
                <a href="{{ route('payments.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('payments.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'المدفوعات والوصولات' : 'Paiements & Reçus' }}</span>
                </a>

                <!-- Cash Register -->
                <a href="{{ route('cash-registers.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('cash-registers.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'صندوق اليومية' : 'Caisse du Jour' }}</span>
                </a>

                <!-- Expenses & Profitability -->
                <a href="{{ route('expenses.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('expenses.index') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'المصاريف والربحية' : 'Dépenses & Rentabilité' }}</span>
                </a>

                <a href="{{ route('expenses.report') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('expenses.report') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'تقرير الربحية' : 'Rapport Rentabilité' }}</span>
                </a>
            @endif

            <!-- Section: Configuration (Owner & Manager) -->
            @if(auth()->user()->isOwner() || auth()->user()->isManager())
                <div class="pt-3 pb-1">
                    <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        {{ app()->getLocale() === 'ar' ? 'إعدادات القاعة' : 'Configuration' }}
                    </p>
                </div>

                <a href="{{ route('settings.spaces') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('settings.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'القاعات والأماكن' : 'Salles & Espaces' }}</span>
                </a>

                <a href="{{ route('packages.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('packages.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'الباقات والخدمات' : 'Packs & Forfaits' }}</span>
                </a>
            @endif

            {{-- Section: Opérations & Jour J --}}
            <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'العمليات اللوجستية' : 'Opérations & Logistique' }}
                </p>
            </div>

            <!-- Operations Command Center -->
            <a href="{{ route('operations.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('operations.*') ? 'bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-950' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'إدارة العمليات واليوم الموعود' : 'Gestion Opérationnelle' }}</span>
                @php
                    $imminentEventsCount = \App\Models\Reservation::where('business_id', auth()->user()->business_id ?? 1)
                        ->whereIn('status', [\App\Models\Reservation::STATUS_CONFIRMED, \App\Models\Reservation::STATUS_HOLD])
                        ->whereBetween('event_date', [now()->toDateString(), now()->addDays(2)->toDateString()])
                        ->count();
                @endphp
                @if($imminentEventsCount > 0)
                    <span class="ms-auto text-[9px] bg-amber-500 text-slate-950 px-1.5 py-0.5 rounded-full font-bold animate-pulse">{{ $imminentEventsCount }}</span>
                @endif
            </a>

            <!-- Inventory / Stock -->
            <a href="{{ route('inventory.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('inventory.*') ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span>{{ app()->getLocale() === 'ar' ? 'المخزون والمواد' : 'Stock & Matériel' }}</span>
                @php
                    $lowStockCount = \App\Models\InventoryItem::where('business_id', auth()->user()->business_id ?? 1)
                        ->whereColumn('quantity_available', '<=', 'quantity_alert')
                        ->where('is_active', true)->count();
                @endphp
                @if($lowStockCount > 0)
                    <span class="ms-auto text-[9px] bg-rose-600 text-white px-1.5 py-0.5 rounded-full font-bold">{{ $lowStockCount }}</span>
                @endif
            </a>
        </nav>

        <!-- User Profile Card in Sidebar -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-900/60">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-white shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">
                            @switch(auth()->user()->role)
                                @case('OWNER')
                                    <span class="text-amber-400 font-medium">Propriétaire</span>
                                    @break
                                @case('MANAGER')
                                    <span class="text-teal-400 font-medium">Gérant</span>
                                    @break
                                @case('RECEPTIONIST')
                                    <span class="text-emerald-400 font-medium">Réception</span>
                                    @break
                                @case('ACCOUNTANT')
                                    <span class="text-sky-400 font-medium">Comptable</span>
                                    @break
                                @case('EVENT_COORDINATOR')
                                    <span class="text-purple-400 font-medium">Coordinateur</span>
                                    @break
                            @endswitch
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-slate-900/80 backdrop-blur border-b border-slate-800/80 px-6 flex items-center justify-between shrink-0 z-20">
            <!-- Left: Page Title / Breadcrumb -->
            <div class="flex items-center gap-3">
                <h2 class="text-base font-bold text-white tracking-tight">
                    @yield('page_title', 'Tableau de bord')
                </h2>
                <div class="hidden sm:flex items-center text-xs text-slate-400 gap-1.5 font-medium">
                    <span>•</span>
                    <span>{{ now()->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}</span>
                </div>
            </div>

            <!-- Right: Actions & Switchers -->
            <div class="flex items-center gap-3">
                <!-- Quick Reservation Button -->
                <a href="{{ route('reservations.create') }}"
                    class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white text-xs font-semibold shadow-md shadow-emerald-950 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? '+ حجز جديد' : '+ Nouvelle Réservation' }}</span>
                </a>

                <!-- Language Switcher -->
                <div class="flex items-center gap-1 bg-slate-800/80 border border-slate-700/80 rounded-lg p-1 text-xs">
                    <a href="{{ route('lang.switch', 'fr') }}" class="px-2 py-0.5 rounded font-medium transition {{ app()->getLocale() === 'fr' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        FR
                    </a>
                    <a href="{{ route('lang.switch', 'ar') }}" class="px-2 py-0.5 rounded font-medium font-arabic transition {{ app()->getLocale() === 'ar' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white' }}">
                        عربي
                    </a>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <main class="flex-1 overflow-y-auto p-6 bg-slate-950">
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Hotkey script: Alt+A to jump to availability -->
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.altKey && (e.key === 'a' || e.key === 'A')) {
                e.preventDefault();
                window.location.href = "{{ route('availability.index') }}";
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
