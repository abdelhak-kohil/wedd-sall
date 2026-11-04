@extends('layouts.app')
@section('title', 'Réservation Rapide (< 60s)')
@section('page_title', app()->getLocale() === 'ar' ? 'حجز سريع (< 60 ثانية)' : 'Réservation Rapide (< 60s)')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⚡</span>
                <div>
                    <h2 class="text-base font-bold text-white">Prise de Réservation Rapide</h2>
                    <p class="text-xs text-slate-400">Verrouillage de date et création du dossier en moins d'une minute</p>
                </div>
            </div>
            <span class="text-xs bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2.5 py-1 rounded-full font-medium">
                Anti double-booking actif 🛡️
            </span>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-rose-950/40 border border-rose-800/60 text-rose-300 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('reservations.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Espace, Date & Créneau -->
            <div>
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">1. Date & Salle</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Salle / Espace <span class="text-rose-500">*</span></label>
                        <select name="venue_space_id" id="venue_space_id" required
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500">
                            @foreach($spaces as $sp)
                                <option value="{{ $sp->id }}"
                                    data-price="{{ $sp->default_price }}"
                                    data-capacity="{{ $sp->capacity_seated }}"
                                    {{ (old('venue_space_id', $preSelectedSpace) == $sp->id) ? 'selected' : '' }}>
                                    {{ $sp->name }} (max {{ $sp->capacity_seated }} pers.)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Date de l'événement <span class="text-rose-500">*</span></label>
                        <input type="date" name="event_date" required min="{{ date('Y-m-d') }}"
                            value="{{ old('event_date', $preSelectedDate ?? date('Y-m-d')) }}"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Créneau horaire prédéfini</label>
                        <select name="time_slot_id" id="time_slot_id"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Horaire personnalisé —</option>
                            @foreach($slots as $slot)
                                <option value="{{ $slot->id }}"
                                    data-start="{{ $slot->start_time }}"
                                    data-end="{{ $slot->end_time }}"
                                    {{ (old('time_slot_id', $preSelectedSlot) == $slot->id) ? 'selected' : '' }}>
                                    {{ $slot->name }} ({{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Heure de début <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" id="start_time" required
                            value="{{ old('start_time', '16:00') }}"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Heure de fin <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" id="end_time" required
                            value="{{ old('end_time', '23:30') }}"
                            class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                    </div>
                </div>
            </div>

            <!-- Section 2: Fête & Invités -->
            <div>
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">2. Détails de l'événement</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Type d'événement <span class="text-rose-500">*</span></label>
                        <select name="event_type" required
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                            <option value="Mariage" {{ old('event_type') === 'Mariage' ? 'selected' : '' }}>👰 Mariage (عرس)</option>
                            <option value="Fiançailles" {{ old('event_type') === 'Fiançailles' ? 'selected' : '' }}>💍 Fiançailles (خطوبة)</option>
                            <option value="Khitana" {{ old('event_type') === 'Khitana' ? 'selected' : '' }}>✂️ Khitana (ختانة)</option>
                            <option value="Dîner / Réception" {{ old('event_type') === 'Dîner / Réception' ? 'selected' : '' }}>🍽️ Dîner / Réception</option>
                            <option value="Conférence" {{ old('event_type') === 'Conférence' ? 'selected' : '' }}>🎤 Conférence / Séminaire</option>
                            <option value="Autre" {{ old('event_type') === 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Nombre d'invités estimé <span class="text-rose-500">*</span></label>
                        <input type="number" name="expected_guests" min="1" required
                            value="{{ old('expected_guests', 200) }}"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-slate-300 mb-1">Marié</label>
                            <input type="text" name="groom_name" placeholder="Walid"
                                value="{{ old('groom_name', $foundCustomer?->groom_name) }}"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-300 mb-1">Mariée</label>
                            <input type="text" name="bride_name" placeholder="Samira"
                                value="{{ old('bride_name', $foundCustomer?->bride_name) }}"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Client & Contact -->
            <div>
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">3. Client & Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Nom complet du client <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_name" required
                            value="{{ old('customer_name', $foundCustomer?->full_name) }}"
                            placeholder="Ex: Mohamed Belhadj"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Numéro de téléphone <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_phone" required
                            value="{{ old('customer_phone', $foundCustomer?->phone) }}"
                            placeholder="0555 12 34 56"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Wilaya</label>
                        <select name="wilaya_id" class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                            <option value="">— Wilaya —</option>
                            @foreach($wilayas as $w)
                                <option value="{{ $w->id }}" {{ (old('wilaya_id', $foundCustomer?->wilaya_id) == $w->id) ? 'selected' : '' }}>
                                    {{ $w->code }} - {{ $w->name_fr }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(!$foundCustomer)
                    <div class="mt-2 flex items-center gap-2">
                        <input type="checkbox" name="create_customer" value="1" id="create_customer" checked
                            class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-0">
                        <label for="create_customer" class="text-xs text-slate-400">
                            Créer automatiquement une fiche client dans le CRM
                        </label>
                    </div>
                @else
                    <input type="hidden" name="customer_id" value="{{ $foundCustomer->id }}">
                    <p class="mt-2 text-xs text-emerald-400">✓ Client existant identifié: {{ $foundCustomer->customer_code }}</p>
                @endif
            </div>

            <!-- Section 4: Tarification & Statut -->
            <div>
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">4. Accord Financier & Statut</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Prix Total Accordé (DA) <span class="text-rose-500">*</span></label>
                        <input type="number" step="1000" name="total_price" id="total_price" required
                            value="{{ old('total_price', 250000) }}"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm font-mono text-emerald-400 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Acompte / 'Erboun' Exigé (DA) <span class="text-rose-500">*</span></label>
                        <input type="number" step="1000" name="deposit_required" id="deposit_required" required
                            value="{{ old('deposit_required', 50000) }}"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sm font-mono text-white">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-300 mb-1">Type de Réservation <span class="text-rose-500">*</span></label>
                        <select name="status" id="reservation_status"
                            class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                            <option value="TEMPORARY_HOLD" {{ old('status') === 'TEMPORARY_HOLD' ? 'selected' : '' }}>
                                ⏳ Bloqué temporaire (Sans acompte)
                            </option>
                            <option value="CONFIRMED" {{ old('status', 'CONFIRMED') === 'CONFIRMED' ? 'selected' : '' }}>
                                ✅ Confirmé (Acompte convenu)
                            </option>
                        </select>
                    </div>
                </div>

                <div id="hold_duration_wrapper" class="mt-3 hidden">
                    <label class="block text-xs text-amber-400 mb-1">Délai d'expiration du blocage temporaire</label>
                    <select name="hold_hours" class="w-48 px-3 py-2 bg-slate-800 border border-amber-600/40 rounded-xl text-xs text-white">
                        <option value="24">24 heures</option>
                        <option value="48" selected>48 heures</option>
                        <option value="72">72 heures (3 jours)</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs text-slate-300 mb-1">Notes internes & arrangements particuliers</label>
                <textarea name="internal_notes" rows="2"
                    placeholder="Ex: Demande de tables rondes, traiteur externe..."
                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white resize-none">{{ old('internal_notes') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between border-t border-slate-800 pt-4">
                <a href="{{ route('reservations.index') }}" class="text-xs text-slate-400 hover:text-white transition">← Annuler</a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg transition flex items-center gap-2">
                    <span>⚡ Enregistrer la réservation</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-fill time slot start and end times
document.getElementById('time_slot_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.start && opt.dataset.end) {
        document.getElementById('start_time').value = opt.dataset.start.substring(0, 5);
        document.getElementById('end_time').value = opt.dataset.end.substring(0, 5);
    }
});

// Toggle hold duration select
const statusSelect = document.getElementById('reservation_status');
const holdWrapper = document.getElementById('hold_duration_wrapper');

function checkStatus() {
    if (statusSelect.value === 'TEMPORARY_HOLD') {
        holdWrapper.classList.remove('hidden');
    } else {
        holdWrapper.classList.add('hidden');
    }
}
statusSelect.addEventListener('change', checkStatus);
checkStatus();
</script>
@endpush
