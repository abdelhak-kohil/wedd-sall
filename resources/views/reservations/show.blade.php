@extends('layouts.app')
@section('title', 'Dossier ' . $reservation->reservation_number)
@section('page_title', 'Dossier Événement 360°')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/40 flex items-center justify-center text-xl">
                📂
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold font-mono text-white">{{ $reservation->reservation_number }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $reservation->statusBadge()['class'] }}">
                        {{ $reservation->statusBadge()['label'] }}
                    </span>
                    @if($reservation->isTemporaryHold() && $reservation->hold_expires_at)
                        <span class="text-[11px] text-amber-400 bg-amber-950/40 border border-amber-800/40 px-2 py-0.5 rounded-full">
                            ⏳ Expire le {{ $reservation->hold_expires_at->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Événement prévu le <span class="font-bold text-white">{{ \Carbon\Carbon::parse($reservation->event_date)->translatedFormat('l d F Y') }}</span>
                    ({{ \Carbon\Carbon::parse($reservation->event_date)->diffForHumans() }})
                </p>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if($reservation->status === 'TEMPORARY_HOLD')
                <form method="POST" action="{{ route('reservations.status', $reservation) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="CONFIRMED">
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow">
                        ✅ Confirmer la réservation
                    </button>
                </form>
            @endif

            <a href="{{ route('operations.show', $reservation) }}"
                class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow">
                🎯 Command Center Jour J
            </a>

            <a href="{{ route('reservations.contract', $reservation) }}" target="_blank"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition flex items-center gap-1.5 shadow">
                📜 Imprimer le Contrat
            </a>

            @if($reservation->status !== 'CANCELLED')
                <button type="button" onclick="document.getElementById('cancel_modal').classList.remove('hidden')"
                    class="px-3 py-2 rounded-xl bg-rose-950/60 hover:bg-rose-900 border border-rose-800/60 text-rose-300 text-xs font-semibold transition">
                    ❌ Annuler
                </button>
            @endif

            <a href="{{ route('reservations.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs transition">
                ← Retour liste
            </a>
        </div>
    </div>

    {{-- Operational Preparation Banner --}}
    @php
        $tasksCount = $reservation->tasks()->count();
        $doneCount = $reservation->tasks()->where('status', \App\Models\EventTask::STATUS_COMPLETED)->count();
        $progress = $reservation->tasks_progress;
        $vendorsCount = $reservation->vendors()->count();
        $criticalPending = $reservation->tasks()->where('is_critical', true)->where('status', '!=', \App\Models\EventTask::STATUS_COMPLETED)->count();
    @endphp
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-indigo-900/60 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-xl shrink-0">
                🎯
            </div>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-white">Gestion Opérationnelle & Préparation Jour J</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $progress >= 80 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($progress >= 40 ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-slate-800 text-slate-300 border border-slate-700') }}">
                        {{ $progress }}% Prêt ({{ $doneCount }}/{{ $tasksCount }} tâches)
                    </span>
                    @if($criticalPending > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                            ⚠️ {{ $criticalPending }} critique(s)
                        </span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400">
                    <span>🤝 {{ $vendorsCount }} prestataire(s) externe(s)</span>
                    <span>·</span>
                    <span>⏱️ {{ $reservation->timelines()->count() }} étapes au planning</span>
                    <span>·</span>
                    <a href="{{ route('operations.show', $reservation) }}" class="text-indigo-400 hover:underline">
                        Gérer les checklists et déroulement →
                    </a>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('operations.print', $reservation) }}" target="_blank"
               class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 shadow">
                <span>🖨️</span>
                <span>Feuille Jour J</span>
            </a>
            <a href="{{ route('operations.show', $reservation) }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                <span>🎯</span>
                <span>Ouvrir Command Center</span>
            </a>
        </div>
    </div>

    <!-- 360 Grid: 3 Pillars (Client, Event/Salle, Finances) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Pillar 1: Client & Contact -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                    <span>👤 Client & Mariés</span>
                </h2>
                @if($reservation->customer)
                    <a href="{{ route('customers.show', $reservation->customer) }}" class="text-[11px] text-indigo-400 hover:underline">
                        Voir fiche →
                    </a>
                @endif
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 block text-[11px]">Client principal</span>
                    <span class="font-bold text-white text-sm">{{ $reservation->customer_name }}</span>
                </div>

                @if($reservation->groom_name || $reservation->bride_name)
                    <div class="p-3 bg-slate-800/50 rounded-xl border border-slate-700/50">
                        <span class="text-slate-400 block text-[11px] mb-1">💍 Noms des mariés</span>
                        <span class="font-bold text-amber-300">
                            {{ $reservation->groom_name ?? '—' }} & {{ $reservation->bride_name ?? '—' }}
                        </span>
                    </div>
                @endif

                <div>
                    <span class="text-slate-500 block text-[11px]">Téléphone</span>
                    <div class="flex items-center justify-between mt-0.5">
                        <span class="font-mono text-white text-sm">{{ $reservation->customer_phone }}</span>
                        <a href="https://wa.me/213{{ ltrim(preg_replace('/\D/', '', $reservation->customer_phone), '0') }}?text={{ urlencode('Salam alaykoum, concernant votre réservation ' . $reservation->reservation_number . ' pour le ' . $reservation->event_date) }}"
                            target="_blank"
                            class="px-2.5 py-1 bg-emerald-600/30 hover:bg-emerald-600 border border-emerald-500/50 text-emerald-300 hover:text-white rounded-lg text-[11px] font-semibold transition">
                            💬 WhatsApp
                        </a>
                    </div>
                </div>

                @if($reservation->customer?->wilaya)
                    <div>
                        <span class="text-slate-500 block text-[11px]">Wilaya</span>
                        <span class="text-slate-300">{{ $reservation->customer->wilaya->code }} - {{ $reservation->customer->wilaya->name_fr }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pillar 2: Salle & Détails Événement -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                    <span>🏛️ Salle & Horaires</span>
                </h2>
                <span class="text-[11px] font-mono text-slate-400">{{ $reservation->space?->venue?->name }}</span>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 block text-[11px]">Espace réservé</span>
                    <span class="font-bold text-white text-sm">{{ $reservation->space?->name ?? '—' }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-500 block text-[11px]">Type d'événement</span>
                        <span class="text-slate-200 font-semibold">{{ $reservation->event_type }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Invités prévus</span>
                        <span class="text-slate-200 font-semibold">{{ $reservation->expected_guests }} personnes</span>
                    </div>
                </div>

                <div class="p-3 bg-slate-800/50 rounded-xl border border-slate-700/50">
                    <span class="text-slate-400 block text-[11px] mb-1">⏰ Horaires d'occupation</span>
                    <div class="font-mono text-indigo-300 font-bold text-sm">
                        {{ substr($reservation->start_time, 0, 5) }} → {{ substr($reservation->end_time, 0, 5) }}
                    </div>
                    @if($reservation->timeSlot)
                        <span class="text-[11px] text-slate-400 mt-1 block">Créneau: {{ $reservation->timeSlot->name }}</span>
                    @endif
                </div>

                @if($reservation->internal_notes)
                    <div>
                        <span class="text-slate-500 block text-[11px]">Notes internes</span>
                        <p class="text-slate-300 text-xs italic bg-slate-800/30 p-2 rounded-lg border border-slate-800">
                            {{ $reservation->internal_notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pillar 3: Bilan Financier -->
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2">
                    <span>💰 Bilan Financier</span>
                </h2>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded {{ $reservation->remaining_balance <= 0 ? 'bg-emerald-950/60 text-emerald-400' : 'bg-amber-950/60 text-amber-400' }}">
                    {{ $reservation->remaining_balance <= 0 ? 'Soldé' : 'Solde restant' }}
                </span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Prix total convenu:</span>
                    <span class="font-mono font-bold text-white text-sm">
                        {{ number_format($reservation->total_price, 0, ',', ' ') }} DA
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Acompte exigé ('Erboun'):</span>
                    <span class="font-mono text-slate-300">
                        {{ number_format($reservation->deposit_required, 0, ',', ' ') }} DA
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Acompte versé:</span>
                    <span class="font-mono text-emerald-400 font-semibold">
                        {{ number_format($reservation->deposit_paid, 0, ',', ' ') }} DA
                    </span>
                </div>

                <div class="flex items-center justify-between border-t border-slate-800 pt-3">
                    <span class="text-slate-400 font-bold">Reste à payer:</span>
                    <span class="font-mono font-bold text-base {{ $reservation->remaining_balance > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                        {{ number_format($reservation->remaining_balance, 0, ',', ' ') }} DA
                    </span>
                </div>

                @if($reservation->remaining_balance > 0)
                    <div class="p-2.5 rounded-xl bg-amber-950/30 border border-amber-800/40 text-[11px] text-amber-300">
                        ⚠️ Solde dû au plus tard le jour de l'événement.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cancellation Banner if Cancelled -->
    @if($reservation->status === 'CANCELLED')
        <div class="bg-rose-950/40 border border-rose-800/60 rounded-2xl p-5 shadow-xl">
            <h3 class="text-xs font-bold text-rose-300 uppercase tracking-wider mb-2">Détails de l'annulation</h3>
            <div class="text-xs text-slate-300 space-y-1">
                <p>Annulé le: <span class="font-mono text-white">{{ $reservation->cancelled_at?->format('d/m/Y H:i') }}</span></p>
                @if($reservation->cancellation_reason)
                    <p>Motif: <span class="text-white italic">{{ $reservation->cancellation_reason }}</span></p>
                @endif
            </div>
        </div>
    @endif

</div>

<!-- Cancellation Modal -->
<div id="cancel_modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="text-base font-bold text-white mb-2">Annuler la réservation</h3>
        <p class="text-xs text-slate-400 mb-4">Cette action libèrera immédiatement la date et la salle dans le calendrier.</p>

        <form method="POST" action="{{ route('reservations.status', $reservation) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="CANCELLED">

            <div>
                <label class="block text-xs text-slate-300 mb-1">Motif de l'annulation <span class="text-rose-500">*</span></label>
                <textarea name="cancellation_reason" required rows="3"
                    placeholder="Ex: Désistement des mariés, report de date..."
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('cancel_modal').classList.add('hidden')"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs rounded-xl transition">
                    Fermer
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl transition shadow">
                    Confirmer l'annulation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
