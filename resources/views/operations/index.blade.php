@extends('layouts.app')
@section('title', 'Gestion Opérationnelle')
@section('page_title', 'Gestion Opérationnelle & Préparation Jour J')

@section('content')
<div class="space-y-6">

    {{-- ================================================================ --}}
    {{-- HERO BANNER + OPERATIONAL KPIS                                  --}}
    {{-- ================================================================ --}}
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-slate-700/80 rounded-2xl p-5 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-2xl shadow-inner">
                    🎯
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white flex items-center gap-2">
                        Commandement Opérationnel Jour J
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/30">
                            Checklists · Prestataires · Déroulement
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Suivi de préparation, feuilles de route, prestataires externes et coordination des événements.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.index') }}"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition flex items-center gap-2">
                    <span>📅</span>
                    <span>Calendrier des salles</span>
                </a>
            </div>
        </div>

        {{-- KPI Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5 pt-4 border-t border-slate-800/80">
            <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl p-3">
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Événements filtrés</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-xl font-bold text-white font-mono">{{ $events->count() }}</span>
                    <span class="text-[10px] text-slate-400">célébrations</span>
                </div>
            </div>

            <div class="bg-amber-950/30 border border-amber-800/50 rounded-xl p-3">
                <p class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">⚡ Dans les 48 heures</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-xl font-bold text-amber-400 font-mono">{{ $next48hCount }}</span>
                    <span class="text-[10px] text-amber-400/80">urgent / imminent</span>
                </div>
            </div>

            <div class="bg-rose-950/30 border border-rose-800/50 rounded-xl p-3">
                <p class="text-[10px] uppercase font-bold text-rose-400 tracking-wider">⚠️ Tâches critiques non faites</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-xl font-bold text-rose-400 font-mono">{{ $criticalTasksPending }}</span>
                    <span class="text-[10px] text-rose-400/80">points de blocage</span>
                </div>
            </div>

            <div class="bg-emerald-950/30 border border-emerald-800/50 rounded-xl p-3">
                <p class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">✓ Taux moyen d'avancement</p>
                @php
                    $avgProgress = $events->count() > 0 ? (int) round($events->avg(fn($e) => $e->tasks_progress)) : 0;
                @endphp
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-xl font-bold text-emerald-400 font-mono">{{ $avgProgress }}%</span>
                    <span class="text-[10px] text-emerald-400/80">préparation globale</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- FLASH MESSAGES                                                  --}}
    {{-- ================================================================ --}}
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-700/60 rounded-xl px-4 py-3 text-emerald-300 text-sm flex items-center gap-2">
            <span>✓</span> {{ session('success') }}
        </div>
    @endif

    {{-- ================================================================ --}}
    {{-- FILTERS                                                         --}}
    {{-- ================================================================ --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-3">
        {{-- Range Tabs --}}
        <div class="flex items-center gap-1.5 p-1 bg-slate-800/80 rounded-xl border border-slate-700/60 text-xs">
            <a href="{{ route('operations.index', array_merge(request()->query(), ['range' => 'today'])) }}"
               class="px-3 py-1.5 rounded-lg font-medium transition {{ $range === 'today' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-slate-400 hover:text-white' }}">
                Aujourd'hui
            </a>
            <a href="{{ route('operations.index', array_merge(request()->query(), ['range' => '7days'])) }}"
               class="px-3 py-1.5 rounded-lg font-medium transition {{ $range === '7days' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-slate-400 hover:text-white' }}">
                7 prochains jours
            </a>
            <a href="{{ route('operations.index', array_merge(request()->query(), ['range' => '30days'])) }}"
               class="px-3 py-1.5 rounded-lg font-medium transition {{ $range === '30days' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-slate-400 hover:text-white' }}">
                30 prochains jours
            </a>
            <a href="{{ route('operations.index', array_merge(request()->query(), ['range' => 'all'])) }}"
               class="px-3 py-1.5 rounded-lg font-medium transition {{ $range === 'all' ? 'bg-indigo-600 text-white font-bold shadow' : 'text-slate-400 hover:text-white' }}">
                Tous à venir
            </a>
        </div>

        {{-- Filter by Space --}}
        <form method="GET" action="{{ route('operations.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="range" value="{{ $range }}">
            <select name="space_id" onchange="this.form.submit()"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Toutes les salles</option>
                @foreach($spaces as $sp)
                    <option value="{{ $sp->id }}" {{ $spaceId == $sp->id ? 'selected' : '' }}>
                        {{ $sp->name_fr }} ({{ $sp->capacity_max }} p.)
                    </option>
                @endforeach
            </select>
            @if($spaceId)
                <a href="{{ route('operations.index', ['range' => $range]) }}" class="text-xs text-rose-400 hover:underline">Réinitialiser</a>
            @endif
        </form>
    </div>

    {{-- ================================================================ --}}
    {{-- EVENTS OPERATIONAL LIST                                         --}}
    {{-- ================================================================ --}}
    @if($events->isEmpty())
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-16 text-center shadow-xl">
            <div class="text-5xl mb-3">🎪</div>
            <p class="text-base font-semibold text-white">Aucun événement à préparer pour cette période.</p>
            <p class="text-xs text-slate-400 mt-1">Changez le filtre de date ou consultez le calendrier pour voir les réservations futures.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($events as $event)
            @php
                $isToday = $event->event_date->isToday();
                $isTomorrow = $event->event_date->isTomorrow();
                $diffDays = (int) now()->startOfDay()->diffInDays($event->event_date->startOfDay(), false);
                $tasksCount = $event->tasks->count();
                $doneCount = $event->tasks->where('status', \App\Models\EventTask::STATUS_COMPLETED)->count();
                $progress = $event->tasks_progress;
                $criticalPending = $event->tasks->where('is_critical', true)->where('status', '!=', \App\Models\EventTask::STATUS_COMPLETED)->count();
                $vendorsCount = $event->vendors->count();
                $timelineCount = $event->timelines->count();
            @endphp
            <div class="bg-slate-900/90 border {{ $isToday ? 'border-amber-500/80 ring-1 ring-amber-500/30' : ($isTomorrow ? 'border-indigo-500/60' : 'border-slate-800') }} rounded-2xl p-5 shadow-xl transition hover:border-slate-700">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    {{-- Left: Event Identification --}}
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl {{ $isToday ? 'bg-amber-500/20 border-amber-500/40 text-amber-300' : 'bg-slate-800 border-slate-700 text-slate-300' }} border flex flex-col items-center justify-center shrink-0">
                            <span class="text-[10px] uppercase font-bold">{{ $event->event_date->translatedFormat('M') }}</span>
                            <span class="text-xl font-bold font-mono leading-none">{{ $event->event_date->format('d') }}</span>
                            <span class="text-[9px] text-slate-400">{{ $event->event_date->translatedFormat('D') }}</span>
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-bold text-indigo-400">{{ $event->reservation_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $event->statusBadge()['class'] }}">
                                    {{ $event->statusBadge()['label'] }}
                                </span>
                                @if($isToday)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950 animate-pulse">
                                        ⚡ AUJOURD'HUI
                                    </span>
                                @elseif($isTomorrow)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/40">
                                        DEMAIN
                                    </span>
                                @elseif($diffDays > 1)
                                    <span class="text-[11px] text-slate-400 font-mono">
                                        (dans {{ $diffDays }} jours)
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base font-bold text-white mt-1">
                                @if($event->bride_name || $event->groom_name)
                                    💍 Mariage de {{ $event->groom_name ?: '...' }} & {{ $event->bride_name ?: '...' }}
                                @else
                                    {{ $event->event_type }} — {{ $event->customer_name }}
                                @endif
                            </h3>

                            <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-slate-400 mt-1">
                                <span class="flex items-center gap-1">
                                    🏛️ <strong class="text-slate-200">{{ $event->space->name_fr ?? 'Salle' }}</strong>
                                </span>
                                <span>·</span>
                                <span>⏰ {{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}</span>
                                <span>·</span>
                                <span>👥 {{ $event->expected_guests }} invités</span>
                                <span>·</span>
                                <a href="https://wa.me/213{{ ltrim(preg_replace('/\D/', '', $event->customer_phone), '0') }}"
                                   target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1">
                                    <span>📱</span> <span>{{ $event->customer_phone }}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Middle: Readiness Gauge & Indicators --}}
                    <div class="lg:w-72 bg-slate-950/50 border border-slate-800/80 rounded-xl p-3.5 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold text-slate-300 flex items-center gap-1.5">
                                <span>📋 Préparation :</span>
                                <strong class="font-mono {{ $progress >= 80 ? 'text-emerald-400' : ($progress >= 40 ? 'text-amber-400' : 'text-slate-400') }}">
                                    {{ $progress }}%
                                </strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                {{ $doneCount }}/{{ $tasksCount }} validée(s)
                            </span>
                        </div>

                        {{-- Progress bar --}}
                        <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $progress >= 80 ? 'bg-emerald-500' : ($progress >= 40 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                                 style="width: {{ $progress }}%"></div>
                        </div>

                        {{-- Badges row --}}
                        <div class="flex flex-wrap items-center gap-1.5 pt-1 text-[11px]">
                            @if($criticalPending > 0)
                                <span class="px-2 py-0.5 rounded-md bg-rose-950/80 border border-rose-800 text-rose-300 font-bold">
                                    ⚠️ {{ $criticalPending }} critique(s) en attente
                                </span>
                            @elseif($tasksCount > 0)
                                <span class="px-2 py-0.5 rounded-md bg-emerald-950/80 border border-emerald-800 text-emerald-300 font-medium">
                                    ✓ Points critiques OK
                                </span>
                            @endif

                            <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                                🤝 {{ $vendorsCount }} prestataire(s)
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 border border-slate-700">
                                ⏱️ {{ $timelineCount }} étape(s)
                            </span>
                        </div>
                    </div>

                    {{-- Right: Direct Actions --}}
                    <div class="flex items-center lg:flex-col justify-end gap-2 shrink-0">
                        <a href="{{ route('operations.show', $event) }}"
                           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow flex items-center justify-center gap-1.5 w-full">
                            <span>🎯</span>
                            <span>Command Center</span>
                        </a>

                        <div class="flex items-center gap-2 w-full">
                            <a href="{{ route('operations.print', $event) }}" target="_blank"
                               class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-xl border border-slate-700 transition flex items-center justify-center gap-1 flex-1"
                               title="Imprimer la feuille de route du jour J">
                                <span>🖨️</span>
                                <span>Feuille Jour J</span>
                            </a>

                            @if($tasksCount === 0)
                                <form method="POST" action="{{ route('operations.checklist.generate', $event) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-2.5 py-1.5 bg-amber-600/30 hover:bg-amber-600/50 border border-amber-600/60 text-amber-300 text-xs font-medium rounded-xl transition flex items-center justify-center gap-1"
                                            title="Générer la checklist standard en 1 clic">
                                        <span>⚡</span>
                                        <span>Checklist</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
