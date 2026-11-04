@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'لوحة القيادة' : 'Tableau de bord')
@section('page_title', app()->getLocale() === 'ar' ? 'نظرة عامة على القاعة' : 'Vue d\'ensemble de la salle')

@section('content')
<div class="space-y-6">

    {{-- ── Welcome Banner ── --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-950/40 border border-slate-800/80 rounded-2xl p-6 relative overflow-hidden shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                        @switch($user->role)
                            @case('OWNER') bg-amber-500/10 text-amber-400 border border-amber-500/30 @break
                            @case('MANAGER') bg-teal-500/10 text-teal-400 border border-teal-500/30 @break
                            @case('RECEPTIONIST') bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 @break
                            @case('ACCOUNTANT') bg-sky-500/10 text-sky-400 border border-sky-500/30 @break
                            @case('EVENT_COORDINATOR') bg-purple-500/10 text-purple-400 border border-purple-500/30 @break
                        @endswitch">
                        ● {{ $user->role }}
                    </span>
                    <span class="text-xs text-slate-400">· {{ $business->name_fr ?? 'Palais El Bahia' }} · {{ now()->isoFormat('dddd D MMMM Y') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">
                    Bienvenue, {{ $user->name }} 👋
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $stats['today_count'] > 0 ? $stats['today_count'].' événement(s) prévu(s) aujourd\'hui.' : 'Aucun événement aujourd\'hui.' }}
                    @if($stats['expiring_today'] > 0)
                        <span class="text-amber-400 font-medium">· {{ $stats['expiring_today'] }} option(s) expire(nt) aujourd'hui !</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('reservations.create') }}"
                   class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle Réservation
                </a>
                <a href="{{ route('availability.index') }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Disponibilité
                </a>
            </div>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Today's events --}}
        <a href="{{ route('reservations.index') }}" class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 hover:border-emerald-800/60 hover:bg-slate-800/40 transition group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Événements Aujourd'hui</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ $stats['today_count'] }}</div>
            <p class="text-xs text-emerald-400 font-medium">
                @if($stats['today_count'] > 0)
                    {{ $todayReservations->pluck('space.name_fr')->filter()->join(', ') ?: 'Voir le calendrier' }}
                @else
                    Journée libre
                @endif
            </p>
        </a>

        {{-- Active holds --}}
        <a href="{{ route('reservations.index') }}" class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 hover:border-amber-800/60 hover:bg-slate-800/40 transition group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Options Temporaires</span>
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 group-hover:bg-amber-500/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ $stats['active_holds'] }}</div>
            <p class="text-xs font-medium {{ $stats['expiring_today'] > 0 ? 'text-amber-400' : 'text-slate-400' }}">
                @if($stats['expiring_today'] > 0)
                    ⚠ {{ $stats['expiring_today'] }} expire(nt) aujourd'hui
                @else
                    Aucune expiration imminente
                @endif
            </p>
        </a>

        {{-- Outstanding balance --}}
        <a href="{{ route('payments.index') }}" class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 hover:border-rose-800/60 hover:bg-slate-800/40 transition group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Soldes Restants</span>
                <span class="p-2 rounded-xl bg-rose-500/10 text-rose-400 group-hover:bg-rose-500/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-white mb-1">{{ number_format($stats['unpaid_balance'], 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">DA</span></div>
            <p class="text-xs text-rose-400 font-medium">À recouvrer sur dossiers actifs</p>
        </a>

        {{-- Month profit --}}
        <a href="{{ route('expenses.report') }}" class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 hover:border-indigo-800/60 hover:bg-slate-800/40 transition group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bénéfice {{ now()->format('M Y') }}</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400 group-hover:bg-indigo-500/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold {{ $stats['month_profit'] >= 0 ? 'text-indigo-400' : 'text-rose-400' }} mb-1">
                {{ $stats['month_profit'] >= 0 ? '+' : '' }}{{ number_format($stats['month_profit'], 0, ',', ' ') }} <span class="text-sm font-normal text-slate-500">DA</span>
            </div>
            <p class="text-xs text-slate-400">
                Recettes : {{ number_format($stats['month_revenue'], 0, ',', ' ') }} DA
                · Dépenses : {{ number_format($stats['month_expenses'], 0, ',', ' ') }} DA
            </p>
        </a>
    </div>

    {{-- ── Secondary stats row ── --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 text-center">
            <p class="text-xs text-slate-500 mb-1 uppercase tracking-wider">Événements ce mois</p>
            <p class="text-2xl font-bold text-white">{{ $stats['booked_this_month'] }}</p>
            <div class="mt-2 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width:{{ $stats['occupancy_pct'] }}%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-1">Occupation : {{ $stats['occupancy_pct'] }}%</p>
        </div>
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 text-center">
            <p class="text-xs text-slate-500 mb-1 uppercase tracking-wider">Prochains 30 jours</p>
            <p class="text-2xl font-bold text-white">{{ $stats['upcoming_count'] }}</p>
            <p class="text-xs text-teal-400 mt-2 font-medium">Réservations confirmées / options</p>
        </div>
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-4 text-center">
            <p class="text-xs text-slate-500 mb-1 uppercase tracking-wider">Base Clients</p>
            <p class="text-2xl font-bold text-white">{{ $stats['total_customers'] }}</p>
            <p class="text-xs text-slate-400 mt-2">
                <span class="text-emerald-400 font-medium">+{{ $stats['new_customers'] }}</span> ce mois
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── Today's Events ── --}}
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-800/60 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-300">Événements du {{ now()->format('d/m/Y') }}</h2>
                <a href="{{ route('calendar.index') }}" class="text-xs text-emerald-400 hover:underline">Calendrier →</a>
            </div>
            @if($todayReservations->isEmpty())
            <div class="py-10 text-center">
                <p class="text-slate-500 text-sm">Aucun événement aujourd'hui.</p>
                <a href="{{ route('reservations.create') }}" class="mt-2 inline-block text-xs text-emerald-400 hover:underline">Créer une réservation →</a>
            </div>
            @else
            <div class="divide-y divide-slate-800/40">
                @foreach($todayReservations as $res)
                <a href="{{ route('reservations.show', $res) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-slate-800/30 transition">
                    <div class="w-1.5 h-10 rounded-full shrink-0
                        {{ $res->status === 'CONFIRMED' ? 'bg-emerald-500' : ($res->status === 'TEMPORARY_HOLD' ? 'bg-amber-500' : 'bg-slate-600') }}">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-slate-200 truncate">{{ $res->customer_name }}</p>
                        <p class="text-xs text-slate-500">{{ $res->space->name_fr ?? '—' }} · {{ $res->start_time }} – {{ $res->end_time }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs text-slate-400">{{ $res->expected_guests }} inv.</p>
                        <p class="text-xs font-medium {{ $res->remaining_balance > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                            {{ $res->remaining_balance > 0 ? number_format($res->remaining_balance, 0, ',', ' ').' DA dû' : 'Soldé ✓' }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Upcoming Reservations ── --}}
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-800/60 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-300">Prochains événements (30 jours)</h2>
                <a href="{{ route('reservations.index') }}" class="text-xs text-emerald-400 hover:underline">Tout voir →</a>
            </div>
            @if($upcomingReservations->isEmpty())
            <div class="py-10 text-center">
                <p class="text-slate-500 text-sm">Aucune réservation à venir.</p>
            </div>
            @else
            <div class="divide-y divide-slate-800/40">
                @foreach($upcomingReservations as $res)
                <a href="{{ route('reservations.show', $res) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-slate-800/30 transition">
                    <div class="shrink-0 text-center w-10">
                        <p class="text-xs font-bold text-slate-400 uppercase">{{ $res->event_date->format('M') }}</p>
                        <p class="text-lg font-bold text-white leading-none">{{ $res->event_date->format('d') }}</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-slate-200 truncate">{{ $res->customer_name }}</p>
                        <p class="text-xs text-slate-500">{{ $res->space->name_fr ?? '—' }} · {{ $res->expected_guests }} invités</p>
                    </div>
                    <div class="shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $res->status === 'CONFIRMED' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">
                            {{ $res->status === 'CONFIRMED' ? 'Confirmé' : 'Option' }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Active Holds ── --}}
        @if($activeHolds->isNotEmpty())
        <div class="bg-slate-900/80 border border-amber-900/30 rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-amber-900/30 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-amber-300">⏱ Options temporaires actives</h2>
            </div>
            <div class="divide-y divide-slate-800/40">
                @foreach($activeHolds as $hold)
                @php $expiresIn = $hold->hold_expires_at ? now()->diffInHours($hold->hold_expires_at, false) : null; @endphp
                <a href="{{ route('reservations.show', $hold) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-slate-800/30 transition">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-slate-200 truncate">{{ $hold->customer_name }}</p>
                        <p class="text-xs text-slate-500">{{ $hold->event_date->format('d/m/Y') }} · {{ $hold->space->name_fr ?? '—' }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        @if($expiresIn !== null)
                        <p class="text-xs font-semibold {{ $expiresIn <= 0 ? 'text-rose-400' : ($expiresIn <= 24 ? 'text-amber-400' : 'text-slate-400') }}">
                            {{ $expiresIn <= 0 ? 'Expiré' : ($expiresIn < 24 ? "Expire dans {$expiresIn}h" : 'Option active') }}
                        </p>
                        @if($hold->hold_expires_at)
                        <p class="text-[11px] text-slate-600">{{ $hold->hold_expires_at->format('d/m H:i') }}</p>
                        @endif
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Recent Activity ── --}}
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-800/60">
                <h2 class="text-sm font-semibold text-slate-300">Dossiers récents</h2>
            </div>
            @if($recentReservations->isEmpty())
            <div class="py-10 text-center">
                <p class="text-slate-500 text-sm">Aucun dossier créé.</p>
            </div>
            @else
            <div class="divide-y divide-slate-800/40">
                @foreach($recentReservations as $res)
                <a href="{{ route('reservations.show', $res) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-slate-800/30 transition">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center shrink-0">
                        <span class="text-[10px] font-bold text-slate-400">{{ substr($res->customer_name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-200 truncate">{{ $res->customer_name }}</p>
                        <p class="text-xs text-slate-500">{{ $res->reservation_number }} · {{ $res->event_date->format('d/m/Y') }}</p>
                    </div>
                    <span class="shrink-0 text-xs px-2 py-0.5 rounded-full
                        @switch($res->status)
                            @case('CONFIRMED') bg-emerald-500/15 text-emerald-400 @break
                            @case('TEMPORARY_HOLD') bg-amber-500/15 text-amber-400 @break
                            @case('CANCELLED') bg-rose-500/15 text-rose-400 @break
                            @case('COMPLETED') bg-slate-700 text-slate-300 @break
                            @default bg-slate-800 text-slate-400
                        @endswitch">
                        {{ ['CONFIRMED'=>'Confirmé','TEMPORARY_HOLD'=>'Option','CANCELLED'=>'Annulé','COMPLETED'=>'Terminé','INQUIRY'=>'Demande'][$res->status] ?? $res->status }}
                    </span>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Stock & Inventory Alert Widget ── --}}
        @if($lowStockItems->isNotEmpty())
        <div class="bg-slate-900/80 border border-amber-900/40 rounded-2xl overflow-hidden">
            <div class="px-5 py-3 border-b border-amber-900/30 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-amber-300 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'تنبيهات المخزون (مواد شارفت على النفاذ)' : 'Alertes Stock & Matériel' }}</span>
                </h2>
                <a href="{{ route('inventory.index', ['low_stock' => 1]) }}" class="text-xs text-amber-400 hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'عرض الكل ←' : 'Gérer le stock →' }}
                </a>
            </div>
            <div class="divide-y divide-slate-800/40">
                @foreach($lowStockItems as $it)
                <a href="{{ route('inventory.index', ['q' => $it->name]) }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-800/30 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-lg">{{ $it->category_icon }}</span>
                        <div class="min-w-0">
                            <p class="font-medium text-xs text-slate-200 truncate">{{ $it->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $it->category_label }} · {{ $it->location ?: 'Non spécifié' }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $it->is_out_of_stock ? 'bg-rose-950/80 text-rose-300 border border-rose-800' : 'bg-amber-950/80 text-amber-300 border border-amber-800' }}">
                            {{ $it->quantity_available }} / {{ $it->quantity_total }} {{ $it->unit }}
                        </span>
                        <p class="text-[10px] text-slate-500 mt-0.5">Seuil alerte : {{ $it->quantity_alert }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

</div>
@endsection
