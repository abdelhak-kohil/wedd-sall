@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'روزنامة الحجوزات' : 'Calendrier des Salles')
@section('page_title', app()->getLocale() === 'ar' ? 'روزنامة حجوزات الأفراح والمناسبات' : 'Calendrier Visuel des Réservations')

@section('content')
<div class="space-y-6">

    <!-- Calendar Navigation & Filter Bar -->
    <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Month Navigator -->
        <div class="flex items-center gap-2">
            <a href="{{ route('calendar.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month, 'space_id' => $spaceId]) }}"
                class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Mois précédent">
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <h3 class="text-lg font-bold text-white tracking-tight px-3 py-1">
                {{ $currentMonth->locale(app()->getLocale())->isoFormat('MMMM YYYY') }}
            </h3>

            <a href="{{ route('calendar.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month, 'space_id' => $spaceId]) }}"
                class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition" title="Mois suivant">
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            @if(!$currentMonth->isCurrentMonth())
                <a href="{{ route('calendar.index', ['space_id' => $spaceId]) }}"
                    class="ml-2 text-xs px-2.5 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 font-semibold border border-emerald-500/20 hover:bg-emerald-500/20 transition">
                    {{ app()->getLocale() === 'ar' ? 'هذا الشهر' : 'Ce mois-ci' }}
                </a>
            @endif
        </div>

        <!-- Space Filter & Legend -->
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('calendar.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="year" value="{{ $currentMonth->year }}">
                <input type="hidden" name="month" value="{{ $currentMonth->month }}">
                
                <select name="space_id" onchange="this.form.submit()"
                    class="px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع القاعات' : 'Toutes les salles' }}</option>
                    @foreach($spaces as $sp)
                        <option value="{{ $sp->id }}" {{ $spaceId == $sp->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $sp->name_ar : $sp->name_fr }}
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Legend Pills -->
            <div class="hidden xl:flex items-center gap-2 text-[11px] font-medium text-slate-400">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> {{ app()->getLocale() === 'ar' ? 'مؤكد' : 'Confirmé' }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> {{ app()->getLocale() === 'ar' ? 'خيار مؤقت' : 'Hold' }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-500"></span> {{ app()->getLocale() === 'ar' ? 'منتهي' : 'Terminé' }}</span>
            </div>
        </div>
    </div>

    <!-- Monthly Grid -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-2xl">
        <!-- Weekday Headers -->
        <div class="grid grid-cols-7 border-b border-slate-800 bg-slate-900 text-center text-xs font-bold text-slate-400 py-3">
            <div>{{ app()->getLocale() === 'ar' ? 'الاثنين' : 'LUN' }}</div>
            <div>{{ app()->getLocale() === 'ar' ? 'الثلاثاء' : 'MAR' }}</div>
            <div>{{ app()->getLocale() === 'ar' ? 'الأربعاء' : 'MER' }}</div>
            <div>{{ app()->getLocale() === 'ar' ? 'الخميس' : 'JEU' }}</div>
            <div class="text-emerald-400">{{ app()->getLocale() === 'ar' ? 'الجمعة' : 'VEN' }}</div>
            <div class="text-teal-400">{{ app()->getLocale() === 'ar' ? 'السبت' : 'SAM' }}</div>
            <div>{{ app()->getLocale() === 'ar' ? 'الأحد' : 'DIM' }}</div>
        </div>

        <!-- Days Matrix -->
        <div class="grid grid-cols-7 divide-x divide-y rtl:divide-x-reverse divide-slate-800/60 bg-slate-950/40">
            @foreach($calendarDays as $day)
                @php
                    $isCurrent = $day['is_current_month'];
                    $isToday = $day['is_today'];
                    $events = $day['events'];
                @endphp

                <div class="min-h-[110px] sm:min-h-[130px] p-2 flex flex-col justify-between transition {{ $isCurrent ? 'bg-slate-900/40' : 'bg-slate-950/80 opacity-40' }} {{ $isToday ? 'ring-2 ring-emerald-500/50 inset-0' : '' }}">
                    <!-- Day Number & Quick Book button -->
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold {{ $isToday ? 'w-6 h-6 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center' : ($isCurrent ? 'text-white' : 'text-slate-500') }}">
                            {{ $day['date']->day }}
                        </span>

                        @if($isCurrent)
                            <a href="{{ route('availability.index', ['date' => $day['date']->toDateString()]) }}"
                                class="text-[10px] text-slate-500 hover:text-emerald-400 transition" title="Vérifier disponibilité">
                                🔍
                            </a>
                        @endif
                    </div>

                    <!-- Events Container -->
                    <div class="space-y-1 overflow-y-auto max-h-[85px]">
                        @foreach($events as $event)
                            <div class="px-2 py-1 rounded-md text-[11px] font-medium leading-tight truncate transition
                                @if($event->status === 'CONFIRMED') bg-emerald-950/80 text-emerald-300 border border-emerald-700/60 hover:bg-emerald-900
                                @elseif($event->status === 'TEMPORARY_HOLD') bg-amber-950/80 text-amber-300 border border-amber-700/60 hover:bg-amber-900
                                @elseif($event->status === 'COMPLETED') bg-slate-800 text-slate-300 border border-slate-700
                                @else bg-slate-800/60 text-slate-400
                                @endif"
                                title="{{ $event->event_type }}: {{ $event->customer_name }} ({{ $event->space->name_fr ?? '' }} {{ $event->time_range }})">
                                
                                <div class="flex items-center justify-between gap-1">
                                    <span class="font-bold truncate">
                                        {{ $event->bride_name && $event->groom_name ? $event->groom_name . ' & ' . $event->bride_name : $event->customer_name }}
                                    </span>
                                    <span class="text-[9px] shrink-0 font-mono">{{ substr($event->start_time, 0, 5) }}</span>
                                </div>
                                <div class="text-[10px] opacity-80 truncate">
                                    {{ $event->space->name_fr ?? '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div></div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Agenda Overview Table -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <h4 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <span>📋</span>
            <span>{{ app()->getLocale() === 'ar' ? 'قائمة فعاليات هذا الشهر' : 'Agenda des Événements du Mois' }} ({{ $reservations->count() }})</span>
        </h4>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left rtl:text-right">
                <thead class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-800/60 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Salle & Horaire</th>
                        <th class="py-3 px-4">Événement & Client</th>
                        <th class="py-3 px-4">Invités</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4">Solde Restant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($reservations as $res)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 font-semibold text-white">
                                {{ $res->event_date->format('d/m/Y') }}
                                <span class="block text-[11px] text-slate-400 font-normal">{{ $res->event_date->locale(app()->getLocale())->isoFormat('dddd') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-white">{{ $res->space->name_fr ?? 'Salle' }}</span>
                                <span class="block text-[11px] text-slate-400 font-mono">{{ $res->time_range }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white">{{ $res->bride_name && $res->groom_name ? $res->groom_name . ' & ' . $res->bride_name : $res->customer_name }}</span>
                                <span class="block text-[11px] text-slate-400">{{ $res->event_type }} · {{ $res->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-white">
                                {{ $res->expected_guests }} pers.
                            </td>
                            <td class="py-3.5 px-4">
                                @if($res->status === 'CONFIRMED')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        Confirmé
                                    </span>
                                @elseif($res->status === 'TEMPORARY_HOLD')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        Hold ({{ $res->hold_hours_remaining }}h)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300">
                                        {{ $res->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold font-mono {{ $res->remaining_balance > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                {{ number_format($res->remaining_balance, 0, ',', ' ') }} DA
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Aucun événement planifié pour ce mois.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
