@extends('layouts.app')
@section('title', 'Command Center - ' . $reservation->reservation_number)
@section('page_title', 'Gestion Opérationnelle Événement')

@section('content')
<div class="space-y-6">

    {{-- ================================================================ --}}
    {{-- TOP EVENT BANNER                                                --}}
    {{-- ================================================================ --}}
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border border-slate-800 rounded-2xl p-5 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600/20 border border-indigo-500/40 flex flex-col items-center justify-center shrink-0 text-indigo-300">
                    <span class="text-[10px] font-bold uppercase">{{ $reservation->event_date->translatedFormat('M') }}</span>
                    <span class="text-xl font-bold font-mono leading-none text-white">{{ $reservation->event_date->format('d') }}</span>
                    <span class="text-[9px] text-slate-400">{{ $reservation->event_date->translatedFormat('Y') }}</span>
                </div>

                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-indigo-400">{{ $reservation->reservation_number }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $reservation->statusBadge()['class'] }}">
                            {{ $reservation->statusBadge()['label'] }}
                        </span>
                        <span class="text-xs text-slate-400">
                            {{ $reservation->event_date->translatedFormat('l d F Y') }}
                            ({{ $reservation->event_date->diffForHumans() }})
                        </span>
                    </div>

                    <h1 class="text-xl font-bold text-white mt-1">
                        @if($reservation->bride_name || $reservation->groom_name)
                            💍 Mariage : {{ $reservation->groom_name ?: '—' }} & {{ $reservation->bride_name ?: '—' }}
                        @else
                            {{ $reservation->event_type }} — {{ $reservation->customer_name }}
                        @endif
                    </h1>

                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-xs text-slate-400 mt-1">
                        <span>🏛️ <strong>{{ $reservation->space->name_fr ?? 'Salle' }}</strong></span>
                        <span>·</span>
                        <span>⏰ {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</span>
                        <span>·</span>
                        <span>👥 {{ $reservation->expected_guests }} personnes</span>
                        <span>·</span>
                        <span>👤 Client : <strong class="text-slate-200">{{ $reservation->customer_name }}</strong></span>
                        <span>·</span>
                        <a href="https://wa.me/213{{ ltrim(preg_replace('/\D/', '', $reservation->customer_phone), '0') }}"
                           target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1 font-mono">
                            <span>📱</span> <span>{{ $reservation->customer_phone }}</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('operations.print', $reservation) }}" target="_blank"
                   class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-2">
                    <span>🖨️</span>
                    <span>Imprimer Feuille Jour J</span>
                </a>

                <a href="{{ route('reservations.show', $reservation) }}"
                   class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                    <span>📂</span>
                    <span>Dossier Réservation</span>
                </a>

                <a href="{{ route('operations.index') }}"
                   class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs rounded-xl border border-slate-700 transition">
                    ← Hub Opérationnel
                </a>
            </div>

        </div>

        {{-- Overall Progress Meter --}}
        <div class="mt-5 pt-4 border-t border-slate-800/80 grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <div class="md:col-span-3 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-white flex items-center gap-2">
                        <span>🎯 État d'avancement de la préparation</span>
                        <span class="font-mono text-sm {{ $progressPct >= 80 ? 'text-emerald-400' : ($progressPct >= 40 ? 'text-amber-400' : 'text-slate-400') }}">
                            {{ $progressPct }}% Prêt
                        </span>
                    </span>
                    <span class="text-slate-400 font-mono text-[11px]">
                        {{ $doneTasks }} sur {{ $totalTasks }} tâches complétées
                    </span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden p-0.5 border border-slate-700/60">
                    <div class="h-full rounded-full transition-all {{ $progressPct >= 80 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($progressPct >= 40 ? 'bg-gradient-to-r from-amber-500 to-orange-400' : 'bg-gradient-to-r from-indigo-500 to-sky-400') }}"
                         style="width: {{ $progressPct }}%"></div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 text-xs">
                @if($totalTasks === 0)
                    <form method="POST" action="{{ route('operations.checklist.generate', $reservation) }}">
                        @csrf
                        <button type="submit"
                                class="px-3.5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl transition shadow flex items-center gap-1.5">
                            <span>⚡</span>
                            <span>Générer Checklist Standard</span>
                        </button>
                    </form>
                @endif
                <button type="button" onclick="document.getElementById('add_task_modal').classList.remove('hidden')"
                        class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition shadow flex items-center gap-1.5">
                    <span>+</span>
                    <span>Ajouter une tâche</span>
                </button>
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
    {{-- 3 MAIN OPERATIONAL COLUMNS                                      --}}
    {{-- ================================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── COLUMN 1 & 2: CHECKLIST DE PRÉPARATION (2 COLS) ───────── --}}
        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">✅</span>
                        <div>
                            <h2 class="text-sm font-bold text-white">Checklist Opérationnelle</h2>
                            <p class="text-[11px] text-slate-400">Contrôle de conformité de la salle et des équipements</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($totalTasks > 0)
                            <form method="POST" action="{{ route('operations.checklist.generate', $reservation) }}"
                                  onsubmit="return confirm('Régénérer complétera les tâches manquantes sans effacer celles terminées. Continuer ?')">
                                @csrf
                                <button type="submit" class="text-[11px] text-slate-400 hover:text-white px-2 py-1 bg-slate-800 rounded-lg transition" title="Compléter les tâches types">
                                    🔄 Réappliquer modèle standard
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($tasks->isEmpty())
                    <div class="py-12 text-center text-slate-500">
                        <div class="text-4xl mb-2">📋</div>
                        <p class="text-sm text-slate-300 font-medium">Aucune tâche dans la checklist.</p>
                        <p class="text-xs text-slate-500 mt-1">Générez automatiquement les 15 points de contrôle essentiels en un clic.</p>
                        <form method="POST" action="{{ route('operations.checklist.generate', $reservation) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow">
                                ⚡ Générer la checklist standard
                            </button>
                        </form>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach(\App\Models\EventTask::TIMING_PHASES as $phaseKey => $phaseMeta)
                            @php
                                $phaseTasks = $tasksByPhase->get($phaseKey, collect());
                            @endphp
                            @if($phaseTasks->isNotEmpty())
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 px-1">
                                    <span class="w-2 h-2 rounded-full bg-{{ $phaseMeta['color'] }}-400"></span>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                                        {{ $phaseMeta['label'] }}
                                    </h3>
                                    <span class="text-[10px] text-slate-500 font-mono">
                                        ({{ $phaseTasks->where('status', \App\Models\EventTask::STATUS_COMPLETED)->count() }}/{{ $phaseTasks->count() }})
                                    </span>
                                </div>

                                <div class="bg-slate-950/40 border border-slate-800/80 rounded-xl divide-y divide-slate-800/60 overflow-hidden">
                                    @foreach($phaseTasks as $task)
                                    @php
                                        $cat = $task->category_meta;
                                        $isDone = $task->is_completed;
                                    @endphp
                                    <div class="p-3 flex items-start gap-3 hover:bg-slate-800/30 transition group {{ $isDone ? 'bg-emerald-950/10' : '' }}">
                                        
                                        {{-- Toggle Checkbox Form --}}
                                        <form method="POST" action="{{ route('operations.tasks.toggle', $task) }}" class="pt-0.5">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="w-5 h-5 rounded-lg border flex items-center justify-center transition {{ $isDone ? 'bg-emerald-600 border-emerald-500 text-white' : 'border-slate-600 bg-slate-800 text-transparent hover:border-emerald-500' }}"
                                                    title="{{ $isDone ? 'Marquer comme non fait' : 'Valider cette tâche' }}">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- Task Details --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                                <span class="text-xs">{{ $cat['icon'] }}</span>
                                                <span class="text-[10px] font-semibold text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded">
                                                    {{ $cat['label'] }}
                                                </span>
                                                @if($task->is_critical)
                                                    <span class="text-[10px] font-bold text-rose-300 bg-rose-950/80 border border-rose-800 px-1.5 py-0.5 rounded">
                                                        ⚠️ CRITIQUE
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs font-medium {{ $isDone ? 'text-slate-400 line-through' : 'text-slate-200' }}">
                                                {{ $task->title }}
                                            </p>

                                            @if($task->notes)
                                                <p class="text-[11px] text-slate-400 mt-0.5 italic">
                                                    💬 {{ $task->notes }}
                                                </p>
                                            @endif

                                            @if($isDone && $task->completed_at)
                                                <p class="text-[10px] text-emerald-400 mt-1 flex items-center gap-1">
                                                    <span>✓ Validé le {{ $task->completed_at->format('d/m à H:i') }}</span>
                                                    @if($task->completedByUser)
                                                        <span>par {{ $task->completedByUser->name }}</span>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Delete action --}}
                                        <form method="POST" action="{{ route('operations.tasks.destroy', $task) }}"
                                              onsubmit="return confirm('Supprimer cette tâche ?')" class="opacity-0 group-hover:opacity-100 transition">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-500 hover:text-rose-400 p-1 text-xs" title="Supprimer">
                                                🗑
                                            </button>
                                        </form>

                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── SECTION: RUN OF SHOW / DÉROULEMENT CHRONOLOGIQUE ─────── --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">⏱️</span>
                        <div>
                            <h2 class="text-sm font-bold text-white">Déroulement Chronologique (Run of Show)</h2>
                            <p class="text-[11px] text-slate-400">Planning minute par minute du jour de l'événement</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($reservation->timelines->isEmpty())
                            <form method="POST" action="{{ route('operations.timeline.generate', $reservation) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-indigo-600/30 hover:bg-indigo-600/50 border border-indigo-600/60 text-indigo-300 text-xs font-semibold rounded-xl transition flex items-center gap-1">
                                    <span>⚡</span>
                                    <span>Planning Type</span>
                                </button>
                            </form>
                        @endif
                        <button type="button" onclick="document.getElementById('add_timeline_modal').classList.remove('hidden')"
                                class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition flex items-center gap-1">
                            <span>+</span>
                            <span>Ajouter étape</span>
                        </button>
                    </div>
                </div>

                @if($reservation->timelines->isEmpty())
                    <div class="py-8 text-center text-slate-500 text-xs">
                        Aucune étape chronologique définie.
                        <form method="POST" action="{{ route('operations.timeline.generate', $reservation) }}" class="mt-2">
                            @csrf
                            <button type="submit" class="text-indigo-400 hover:underline">Générer le déroulement standard (7 étapes clé)</button>
                        </form>
                    </div>
                @else
                    <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                        @foreach($reservation->timelines as $step)
                        <div class="relative flex items-start gap-3 bg-slate-950/40 border border-slate-800/80 rounded-xl p-3 hover:border-slate-700 transition group {{ $step->is_done ? 'bg-emerald-950/10 border-emerald-900/40' : '' }}">
                            
                            {{-- Bullet icon on line --}}
                            <div class="absolute -left-[27px] top-4 w-4 h-4 rounded-full border-2 {{ $step->is_done ? 'bg-emerald-500 border-emerald-400' : 'bg-slate-900 border-indigo-400' }} flex items-center justify-center">
                            </div>

                            <form method="POST" action="{{ route('operations.timeline.toggle', $step) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-2 py-1 rounded-lg font-mono text-xs font-bold {{ $step->is_done ? 'bg-emerald-600 text-white' : 'bg-indigo-600/20 text-indigo-300 border border-indigo-500/30' }}">
                                    {{ $step->time }}
                                </button>
                            </form>

                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold {{ $step->is_done ? 'text-slate-400 line-through' : 'text-white' }}">
                                    {{ $step->title }}
                                </h4>
                                @if($step->description)
                                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $step->description }}</p>
                                @endif
                                @if($step->person_in_charge)
                                    <p class="text-[10px] text-amber-400/90 mt-1">
                                        👤 Responsable : <strong>{{ $step->person_in_charge }}</strong>
                                    </p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('operations.timeline.destroy', $step) }}"
                                  onsubmit="return confirm('Supprimer cette étape ?')" class="opacity-0 group-hover:opacity-100 transition">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-500 hover:text-rose-400 p-1 text-xs">🗑</button>
                            </form>

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── COLUMN 3: PRESTATAIRES EXTERNES (1 COL) ───────────────── --}}
        <div class="space-y-4">
            
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🤝</span>
                        <div>
                            <h2 class="text-sm font-bold text-white">Prestataires Externes</h2>
                            <p class="text-[11px] text-slate-400">Coordonnées & Heures d'arrivée</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('add_vendor_modal').classList.remove('hidden')"
                            class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1">
                        <span>+</span>
                        <span>Prestataire</span>
                    </button>
                </div>

                @if($reservation->vendors->isEmpty())
                    <div class="py-10 text-center text-slate-500 text-xs">
                        <div class="text-3xl mb-1">🎭</div>
                        <p class="text-slate-400 font-medium">Aucun prestataire déclaré.</p>
                        <p class="text-[11px] text-slate-500 mt-1">Enregistrez le traiteur, le DJ, le photographe pour coordonner le Jour J.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($reservation->vendors as $vendor)
                        @php $vCat = $vendor->category_meta; @endphp
                        <div class="bg-slate-950/50 border border-slate-800/80 rounded-xl p-3.5 space-y-2 hover:border-slate-700 transition relative group">
                            
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-base">{{ $vCat['icon'] }}</span>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-white truncate">{{ $vendor->company_name }}</h4>
                                        <span class="text-[10px] text-slate-400">{{ $vCat['label'] }}</span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('operations.vendors.destroy', $vendor) }}"
                                      onsubmit="return confirm('Retirer ce prestataire ?')" class="opacity-0 group-hover:opacity-100 transition">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-500 hover:text-rose-400 text-xs">✕</button>
                                </form>
                            </div>

                            @if($vendor->contact_person)
                                <p class="text-[11px] text-slate-300">
                                    Interlocuteur : <strong>{{ $vendor->contact_person }}</strong>
                                </p>
                            @endif

                            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-800/60">
                                <div class="flex items-center gap-2">
                                    <a href="tel:{{ $vendor->phone }}" class="text-slate-300 hover:text-white font-mono flex items-center gap-1">
                                        <span>📞</span> <span>{{ $vendor->phone }}</span>
                                    </a>
                                    <a href="https://wa.me/213{{ ltrim(preg_replace('/\D/', '', $vendor->phone), '0') }}"
                                       target="_blank" class="text-emerald-400 hover:underline text-[11px]">
                                        WhatsApp
                                    </a>
                                </div>

                                @if($vendor->arrival_time)
                                    <span class="px-2 py-0.5 rounded-md bg-amber-950/60 border border-amber-800/50 text-amber-300 font-mono text-[10px]">
                                        Arrivée {{ $vendor->arrival_time }}
                                    </span>
                                @endif
                            </div>

                            @if($vendor->special_notes)
                                <p class="text-[10px] text-slate-400 bg-slate-900/60 p-2 rounded-lg border border-slate-800 italic">
                                    📌 {{ $vendor->special_notes }}
                                </p>
                            @endif

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Urgent Call box --}}
            <div class="bg-gradient-to-br from-amber-950/30 to-slate-900 border border-amber-800/40 rounded-2xl p-4 text-xs space-y-2">
                <h3 class="font-bold text-amber-300 flex items-center gap-1.5">
                    <span>⚡</span>
                    <span>Consignes Sécurité & Salle</span>
                </h3>
                <ul class="text-[11px] text-slate-300 space-y-1 list-disc list-inside">
                    <li>Groupe électrogène testé avant ouverture.</li>
                    <li>Extincteurs dégagés et issues de secours déverrouillées.</li>
                    <li>Conserver la facture & solde restant signés avant le gâteau.</li>
                </ul>
            </div>

        </div>

    </div>

</div>

{{-- ================================================================ --}}
{{-- MODAL: AJOUTER UNE TÂCHE                                        --}}
{{-- ================================================================ --}}
<div id="add_task_modal" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>+</span> <span>Ajouter une Tâche Opérationnelle</span>
            </h3>
            <button onclick="document.getElementById('add_task_modal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form method="POST" action="{{ route('operations.tasks.store', $reservation) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Intitulé de la tâche <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Ex: Vérifier la pression d'eau et climatisation..."
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catégorie <span class="text-rose-500">*</span></label>
                    <select name="category" required
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\EventTask::CATEGORIES as $k => $c)
                            <option value="{{ $k }}">{{ $c['icon'] }} {{ $c['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Moment d'exécution <span class="text-rose-500">*</span></label>
                    <select name="timing_phase" required
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\EventTask::TIMING_PHASES as $k => $p)
                            <option value="{{ $k }}" {{ $k === 'DAY_OF_SETUP' ? 'selected' : '' }}>{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer p-2.5 bg-slate-800/60 border border-slate-700/60 rounded-xl">
                    <input type="checkbox" name="is_critical" value="1"
                        class="rounded border-slate-600 bg-slate-700 text-rose-500 focus:ring-rose-500">
                    <span class="text-xs font-semibold text-rose-300">⚠️ Marquer comme tâche critique (bloquante)</span>
                </label>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Instructions / Notes complémentaires</label>
                <textarea name="notes" rows="2" placeholder="Détails techniques, lieu des clés..."
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('add_task_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow">
                    Enregistrer la tâche
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================ --}}
{{-- MODAL: AJOUTER UN PRESTATAIRE                                   --}}
{{-- ================================================================ --}}
<div id="add_vendor_modal" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>+</span> <span>Déclarer un Prestataire Externe</span>
            </h3>
            <button onclick="document.getElementById('add_vendor_modal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form method="POST" action="{{ route('operations.vendors.store', $reservation) }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catégorie <span class="text-rose-500">*</span></label>
                    <select name="category" required
                        class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\EventVendor::CATEGORIES as $k => $c)
                            <option value="{{ $k }}">{{ $c['icon'] }} {{ $c['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Heure d'arrivée prévue</label>
                    <input type="text" name="arrival_time" placeholder="Ex: 16:30"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nom de l'entreprise ou prestataire <span class="text-rose-500">*</span></label>
                <input type="text" name="company_name" required placeholder="Ex: Traiteur Les Délices d'Alger"
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Personne contact</label>
                    <input type="text" name="contact_person" placeholder="Ex: Chef Nabil"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Téléphone <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" required placeholder="0550 11 22 33"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Besoins spécifiques & Remarques</label>
                <textarea name="special_notes" rows="2" placeholder="Ex: Accès rampe livraison requis, 2 prises 220V..."
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('add_vendor_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow">
                    Enregistrer le prestataire
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================ --}}
{{-- MODAL: AJOUTER UNE ÉTAPE CHRONOLOGIQUE                          --}}
{{-- ================================================================ --}}
<div id="add_timeline_modal" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>+</span> <span>Ajouter une Étape au Planning</span>
            </h3>
            <button onclick="document.getElementById('add_timeline_modal').classList.add('hidden')" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form method="POST" action="{{ route('operations.timeline.store', $reservation) }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Horaire <span class="text-rose-500">*</span></label>
                    <input type="text" name="time" required placeholder="Ex: 21:00"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Titre de l'étape <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ex: Service du buffet chaud..."
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Responsable</label>
                <input type="text" name="person_in_charge" placeholder="Ex: Maître d'hôtel / DJ / Régisseur"
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Consignes & Détails</label>
                <textarea name="description" rows="2" placeholder="Musique spéciale, éclairage tamisé..."
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('add_timeline_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">Annuler</button>
                <button type="submit"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow">
                    Ajouter au planning
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
