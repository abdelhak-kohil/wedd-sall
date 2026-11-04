@extends('layouts.app')
@section('title', 'Modifier Devis ' . $quotation->quote_number)
@section('page_title', 'Modifier le Devis Commercial')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header info banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-slate-700/80 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-2xl shadow-inner">
                ✏️
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-bold text-white flex items-center gap-2">
                        Modification du Devis <span class="font-mono text-emerald-400">{{ $quotation->quote_number }}</span>
                    </h1>
                    @php $badge = $quotation->statusBadge(); @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $badge['class'] }}">
                        {{ $badge['label_fr'] }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Modifiez les prestations, tarifs, remises et conditions financières de cette proposition commerciale.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quotations.show', $quotation) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition flex items-center gap-1.5">
                ← Annuler / Retour au devis
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-950/50 border border-rose-800/80 text-rose-300 text-xs shadow-lg">
            <div class="font-bold flex items-center gap-2 mb-1 text-sm text-rose-200">
                <span>⚠️</span> Veuillez corriger les erreurs suivantes :
            </div>
            <ul class="list-disc list-inside space-y-1 text-[11px]">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quotations.update', $quotation) }}" id="quotation_form" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Form Sections -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Informations Client -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">👤</span>
                            <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">1. Informations Client & Événement</h2>
                        </div>
                        <span class="text-[11px] text-slate-400">Prospect / Client</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Nom complet du client <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="customer_name" required
                                value="{{ old('customer_name', $quotation->customer_name) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @if($quotation->customer_id)
                                <input type="hidden" name="customer_id" value="{{ $quotation->customer_id }}">
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Téléphone de contact <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="customer_phone" required
                                value="{{ old('customer_phone', $quotation->customer_phone) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                Prénom du marié (Optionnel)
                            </label>
                            <input type="text" name="groom_name" value="{{ old('groom_name', $quotation->groom_name) }}"
                                class="w-full px-3.5 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">
                                Prénom de la mariée (Optionnel)
                            </label>
                            <input type="text" name="bride_name" value="{{ old('bride_name', $quotation->bride_name) }}"
                                class="w-full px-3.5 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- 2. Salle, Date & Formule de base -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🏛️</span>
                            <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">2. Espace, Date & Paramètres</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-[11px] text-slate-400">Statut du devis :</label>
                            <select name="status" class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-lg text-xs text-white">
                                @foreach(\App\Models\Quotation::STATUSES as $stKey => $stVal)
                                    <option value="{{ $stKey }}" {{ old('status', $quotation->status) === $stKey ? 'selected' : '' }}>
                                        {{ $stVal['label_fr'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Espace / Salle <span class="text-rose-500">*</span>
                            </label>
                            <select name="venue_space_id" id="space_select" required
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                @foreach($spaces as $sp)
                                    <option value="{{ $sp->id }}"
                                        data-name="{{ $sp->name }}"
                                        data-base="{{ $sp->base_price }}"
                                        data-capacity="{{ $sp->capacity_max ?? 300 }}"
                                        {{ old('venue_space_id', $quotation->venue_space_id) == $sp->id ? 'selected' : '' }}>
                                        {{ $sp->name }} ({{ number_format($sp->base_price, 0, ',', ' ') }} DA)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Date de l'événement <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="event_date" required
                                value="{{ old('event_date', $quotation->event_date->format('Y-m-d')) }}"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Formule / Pack associé
                            </label>
                            <select name="package_id" id="package_select"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="" data-base="0" data-guest="0" data-name="">— Location sans pack prédéfini —</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}"
                                        data-name="{{ $pkg->name_fr }}"
                                        data-base="{{ $pkg->base_price }}"
                                        data-guest="{{ $pkg->price_per_guest }}"
                                        {{ old('package_id', $quotation->package_id) == $pkg->id ? 'selected' : '' }}>
                                        {{ $pkg->name_fr }} (Base {{ number_format($pkg->base_price, 0, ',', ' ') }} DA)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Nombre d'invités estimé <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="expected_guests" id="expected_guests" min="1" required
                                    value="{{ old('expected_guests', $quotation->expected_guests) }}"
                                    class="w-full pl-3 pr-10 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-500">pers.</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Heure de début <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" name="start_time" required
                                value="{{ old('start_time', substr($quotation->start_time, 0, 5)) }}"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Heure de fin <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" name="end_time" required
                                value="{{ old('end_time', substr($quotation->end_time, 0, 5)) }}"
                                class="w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- 3. Prestations Détaillées & Lignes de Devis -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base">📋</span>
                                <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">3. Détail Chiffré des Prestations (Lignes de Devis)</h2>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Ajustez les prestations, quantités et tarifs unitaires.
                            </p>
                        </div>
                        <button type="button" onclick="addNewLine()"
                            class="px-3 py-1.5 rounded-xl bg-emerald-600/30 hover:bg-emerald-600 border border-emerald-500/50 text-emerald-300 hover:text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                            <span>+ Ajouter une ligne</span>
                        </button>
                    </div>

                    <!-- Quick Add Preset Pills -->
                    <div>
                        <span class="text-[11px] font-medium text-slate-400 block mb-2">⚡ Prestations rapides :</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="addPreset('Animation DJ & Sonorisation Haut de Gamme', 1, 35000)"
                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] border border-slate-700 transition">
                                🎵 DJ & Sonorisation (+35k)
                            </button>
                            <button type="button" onclick="addPreset('Décoration Florale & Scénographie Complète', 1, 50000)"
                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] border border-slate-700 transition">
                                🌸 Décoration Florale (+50k)
                            </button>
                            <button type="button" onclick="addPreset('Photographe Professionnel & Caméra 4K', 1, 40000)"
                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] border border-slate-700 transition">
                                📸 Photo & Vidéo 4K (+40k)
                            </button>
                            <button type="button" onclick="addCateringPreset()"
                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] border border-slate-700 transition">
                                🍽️ Dîner Traiteur / Invité (1 200 DA/p)
                            </button>
                            <button type="button" onclick="addPreset('Équipe de Serveurs & Maître d\'hôtel (6 pers.)', 1, 25000)"
                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] border border-slate-700 transition">
                                🤵 Service & Serveurs (+25k)
                            </button>
                            <button type="button" onclick="addPreset('Pièce Montée d\'Honneur & Gâteaux', 1, 30000)"
                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] border border-slate-700 transition">
                                🎂 Pièce Montée (+30k)
                            </button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/40">
                        <table class="w-full text-xs text-left" id="items_table">
                            <thead class="bg-slate-800/80 text-[11px] text-slate-400 uppercase tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="py-2.5 px-3" style="width: 46%;">Désignation de la Prestation</th>
                                    <th class="py-2.5 px-3 text-center" style="width: 14%;">Quantité</th>
                                    <th class="py-2.5 px-3 text-right" style="width: 20%;">Prix Unitaire (DA)</th>
                                    <th class="py-2.5 px-3 text-right" style="width: 20%;">Montant (DA)</th>
                                    <th class="py-2.5 px-2 text-center" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="items_tbody" class="divide-y divide-slate-800/60">
                                <!-- Dynamic Rows Generated by JS -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 px-1">
                        <button type="button" onclick="resetToDefaults()" class="text-slate-500 hover:text-slate-300 text-[11px] underline">
                            ↺ Réinitialiser selon salle & pack
                        </button>
                        <div class="text-right">
                            <span class="text-slate-400 mr-2">Sous-total prestations brutes :</span>
                            <span id="subtotal_display" class="font-mono font-bold text-white text-sm">0 DA</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Remarques & Modalités de Règlement -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                        <span class="text-base">📝</span>
                        <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-wider">4. Clauses & Modalités Particulières</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Notes & arrangements spécifiques
                            </label>
                            <textarea name="notes" rows="3"
                                class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 resize-none focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes', $quotation->notes) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1.5">
                                Modalités de paiement & conditions
                            </label>
                            <textarea name="payment_terms" rows="3"
                                class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 resize-none focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('payment_terms', $quotation->payment_terms) }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Col: Financial Summary Card (Sticky) -->
            <div class="lg:col-span-1">
                <div class="bg-slate-900/95 border border-slate-800 rounded-2xl p-5 shadow-2xl space-y-5 sticky top-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-base">💰</span>
                            <h2 class="text-xs font-bold text-white uppercase tracking-wider">Calcul Commercial Devis</h2>
                        </div>
                        <span class="text-[10px] font-mono uppercase bg-emerald-950/70 text-emerald-400 border border-emerald-800/80 px-2 py-0.5 rounded-md">
                            DA Algérien
                        </span>
                    </div>

                    <!-- Hidden Inputs for Submission -->
                    <input type="hidden" name="subtotal" id="hidden_subtotal" value="{{ $quotation->subtotal }}">
                    <input type="hidden" name="discount_type" id="hidden_discount_type" value="{{ $quotation->discount_type }}">
                    <input type="hidden" name="discount_value" id="hidden_discount_value" value="{{ $quotation->discount_value }}">
                    <input type="hidden" name="total_price" id="hidden_total_price" value="{{ $quotation->total_price }}">

                    <!-- Breakdown details -->
                    <div class="space-y-3 text-xs">

                        <!-- Sous-Total Brut -->
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Sous-total brut :</span>
                            <span id="side_subtotal" class="font-mono font-semibold text-white">0 DA</span>
                        </div>

                        <!-- Remise Commerciale -->
                        <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Remise Commerciale</label>
                                <span id="discount_amount_badge" class="font-mono text-xs font-bold text-amber-400">- 0 DA</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <select id="discount_type_select" onchange="recalculateTotals()"
                                    class="w-full px-2 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-xs text-white">
                                    <option value="none" {{ $quotation->discount_type === 'none' ? 'selected' : '' }}>Sans remise</option>
                                    <option value="percent" {{ $quotation->discount_type === 'percent' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="fixed" {{ $quotation->discount_type === 'fixed' ? 'selected' : '' }}>Montant fixe (DA)</option>
                                </select>
                                <input type="number" id="discount_input" min="0" step="any"
                                    value="{{ (float)$quotation->discount_value }}" oninput="recalculateTotals()"
                                    placeholder="Valeur"
                                    class="w-full px-2 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-xs font-mono text-white text-right">
                            </div>
                        </div>

                        <!-- Net Commercial HT -->
                        <div class="flex items-center justify-between border-t border-slate-800/60 pt-2 text-slate-300">
                            <span>Net Hors Taxe (HT) :</span>
                            <span id="net_ht_display" class="font-mono font-semibold text-slate-200">0 DA</span>
                        </div>

                        <!-- TVA Configuration -->
                        <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">TVA (Optionnelle)</label>
                                <span id="tax_amount_badge" class="font-mono text-xs font-semibold text-blue-400">+ 0 DA</span>
                            </div>
                            <select name="tax_rate" id="tax_rate_select" onchange="recalculateTotals()"
                                class="w-full px-2 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-xs text-white">
                                <option value="0" {{ (float)$quotation->tax_rate == 0 ? 'selected' : '' }}>0% — Franchise / Exonéré / Particulier</option>
                                <option value="9" {{ (float)$quotation->tax_rate == 9 ? 'selected' : '' }}>9% — Taux réduit restauration</option>
                                <option value="19" {{ (float)$quotation->tax_rate == 19 ? 'selected' : '' }}>19% — Taux normal prestation</option>
                            </select>
                        </div>

                        <!-- Grand Total Card -->
                        <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-950/60 to-slate-900 border-2 border-emerald-500/40 shadow-inner space-y-1">
                            <span class="text-[11px] uppercase tracking-wider text-emerald-400 font-bold block">
                                Montant Total Proposé (Net / TTC)
                            </span>
                            <div class="flex items-baseline justify-between">
                                <span id="grand_total_display" class="text-2xl font-black font-mono text-emerald-400">
                                    0 DA
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400">Montant contractuel final en Dinars Algériens</p>
                        </div>

                        <!-- Acompte / Erboun Configuration -->
                        <div class="p-3 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                                    Acompte Exigé ('Erboun') <span class="text-rose-500">*</span>
                                </label>
                                <span id="deposit_percent_badge" class="text-[10px] text-slate-400 font-mono">25%</span>
                            </div>

                            <div class="flex gap-1.5 mb-2">
                                <button type="button" onclick="setDepositPercent(20)"
                                    class="flex-1 py-1 rounded bg-slate-700/70 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">20%</button>
                                <button type="button" onclick="setDepositPercent(25)"
                                    class="flex-1 py-1 rounded bg-slate-700/70 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">25%</button>
                                <button type="button" onclick="setDepositPercent(30)"
                                    class="flex-1 py-1 rounded bg-slate-700/70 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">30%</button>
                                <button type="button" onclick="setDepositPercent(50)"
                                    class="flex-1 py-1 rounded bg-slate-700/70 hover:bg-slate-700 text-slate-300 text-[10px] font-bold">50%</button>
                            </div>

                            <div class="relative">
                                <input type="number" step="1000" name="deposit_required" id="deposit_required" required
                                    value="{{ (float)$quotation->deposit_required }}"
                                    data-manual="true"
                                    oninput="onDepositInput()"
                                    class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-xs font-mono font-bold text-white pr-10">
                                <span class="absolute right-3 top-2 text-[11px] text-slate-500 font-mono">DA</span>
                            </div>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-700/60 text-[11px]">
                                <span class="text-slate-400">Solde restant (Jour J) :</span>
                                <span id="balance_due_display" class="font-mono font-bold text-slate-200">0 DA</span>
                            </div>
                        </div>

                        <!-- Date d'échéance de validité -->
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-medium text-slate-300">
                                Date limite de validité
                            </label>
                            <input type="date" name="valid_until"
                                value="{{ old('valid_until', $quotation->valid_until->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        </div>

                    </div>

                    <!-- Submit & Delete Buttons -->
                    <div class="border-t border-slate-800 pt-4 space-y-2">
                        <button type="submit"
                            class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-xl transition flex items-center justify-center gap-2">
                            <span>💾 Enregistrer les Modifications</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>

    @if((auth()->user()?->isOwner() || auth()->user()?->isManager()) && $quotation->status !== 'ACCEPTED')
        <div class="p-4 bg-slate-900/60 border border-rose-900/40 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h4 class="text-xs font-bold text-rose-400">Zone de danger : Suppression du devis</h4>
                <p class="text-[11px] text-slate-400">Cette action supprimera définitivement le devis N° {{ $quotation->quote_number }}. Accessible uniquement aux gérants et propriétaires.</p>
            </div>
            <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                onsubmit="return confirm('Attention : Êtes-vous certain de vouloir supprimer définitivement le devis {{ $quotation->quote_number }} ?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-rose-600/80 hover:bg-rose-600 border border-rose-500 text-white text-xs font-bold transition shadow-sm shrink-0">
                    🗑️ Supprimer ce devis
                </button>
            </form>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// State pre-populated with existing items from quotation
let items = @json($quotation->items_list);
if (!Array.isArray(items) || items.length === 0) {
    items = [{
        description: @json($quotation->package ? $quotation->package->name_fr : ('Mise à disposition ' . $quotation->space->name)),
        quantity: 1,
        unit_price: {{ (float)$quotation->total_price }}
    }];
}

function recalculateTotals() { window.recalculateTotals(); }
function setDepositPercent(pct) { window.setDepositPercent(pct); }
function onDepositInput() { window.onDepositInput(); }
function addPreset(desc, qty, price) { window.addPreset(desc, qty, price); }
function addCateringPreset() { window.addCateringPreset(); }
function addNewLine(desc, qty, price) { window.addNewLine(desc, qty, price); }
function resetToDefaults() { window.resetToDefaults(); }
function removeItem(index) { window.removeItem(index); }
function updateItemField(index, field, value) { window.updateItemField(index, field, value); }

// Global functions attached to window
window.escapeHtml = function(text) {
    if (!text) return '';
    return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
};

window.formatDA = function(num) {
    return Math.round(num || 0).toLocaleString('fr-FR') + ' DA';
};

window.addNewLine = function(desc = '', qty = 1, price = 0) {
    items.push({
        description: desc,
        quantity: parseFloat(qty) || 1,
        unit_price: parseFloat(price) || 0
    });
    window.renderItems();
};

window.addPreset = function(desc, qty, price) {
    const existing = items.find(it => it.description.toLowerCase().includes(desc.toLowerCase().substring(0, 15)));
    if (existing) {
        existing.quantity += parseFloat(qty) || 1;
    } else {
        items.push({ description: desc, quantity: parseFloat(qty) || 1, unit_price: parseFloat(price) || 0 });
    }
    window.renderItems();
};

window.addCateringPreset = function() {
    const guestsInput = document.getElementById('expected_guests');
    const guests = parseInt(guestsInput ? guestsInput.value : 200) || 200;
    items.push({
        description: `Dîner gastronomique complet (${guests} couverts)`,
        quantity: guests,
        unit_price: 1200
    });
    window.renderItems();
};

window.removeItem = function(index) {
    items.splice(index, 1);
    window.renderItems();
};

window.updateItemField = function(index, field, value) {
    if (!items[index]) return;
    if (field === 'quantity' || field === 'unit_price') {
        items[index][field] = parseFloat(value) || 0;
    } else {
        items[index][field] = value;
    }

    const qty = parseFloat(items[index].quantity) || 0;
    const unitPrice = parseFloat(items[index].unit_price) || 0;
    const lineTotal = qty * unitPrice;

    const totalSpan = document.getElementById(`item_total_${index}`);
    const hiddenTotalInput = document.getElementById(`item_hidden_total_${index}`);
    if (totalSpan) totalSpan.textContent = window.formatDA(lineTotal);
    if (hiddenTotalInput) hiddenTotalInput.value = lineTotal.toFixed(2);

    window.recalculateTotals();
};

window.recalculateTotals = function() {
    const itemsTbody = document.getElementById('items_tbody');
    const subtotalDisplay = document.getElementById('subtotal_display');
    const sideSubtotal = document.getElementById('side_subtotal');
    const hiddenSubtotal = document.getElementById('hidden_subtotal');
    const discountTypeSelect = document.getElementById('discount_type_select');
    const discountInput = document.getElementById('discount_input');
    const discountAmountBadge = document.getElementById('discount_amount_badge');
    const hiddenDiscountType = document.getElementById('hidden_discount_type');
    const hiddenDiscountValue = document.getElementById('hidden_discount_value');
    const netHtDisplay = document.getElementById('net_ht_display');
    const taxRateSelect = document.getElementById('tax_rate_select');
    const taxAmountBadge = document.getElementById('tax_amount_badge');
    const grandTotalDisplay = document.getElementById('grand_total_display');
    const hiddenTotalPrice = document.getElementById('hidden_total_price');
    const depositInput = document.getElementById('deposit_required');
    const balanceDueDisplay = document.getElementById('balance_due_display');
    const depositPercentBadge = document.getElementById('deposit_percent_badge');

    let subtotal = 0;
    items.forEach(it => {
        subtotal += (parseFloat(it.quantity) || 0) * (parseFloat(it.unit_price) || 0);
    });

    if (subtotalDisplay) subtotalDisplay.textContent = window.formatDA(subtotal);
    if (sideSubtotal) sideSubtotal.textContent = window.formatDA(subtotal);
    if (hiddenSubtotal) hiddenSubtotal.value = subtotal.toFixed(2);

    // Discount
    const discType = discountTypeSelect ? discountTypeSelect.value : 'none';
    const discVal = discountInput ? (parseFloat(discountInput.value) || 0) : 0;
    let discAmount = 0;

    if (hiddenDiscountType) hiddenDiscountType.value = discType;
    if (hiddenDiscountValue) hiddenDiscountValue.value = discVal;

    if (discType === 'percent') {
        discAmount = subtotal * (discVal / 100);
        if (discountAmountBadge) discountAmountBadge.textContent = `- ${window.formatDA(discAmount)} (${discVal}%)`;
    } else if (discType === 'fixed') {
        discAmount = Math.min(subtotal, discVal);
        if (discountAmountBadge) discountAmountBadge.textContent = `- ${window.formatDA(discAmount)}`;
    } else {
        if (discountAmountBadge) discountAmountBadge.textContent = `- 0 DA`;
    }

    const netHt = Math.max(0, subtotal - discAmount);
    if (netHtDisplay) netHtDisplay.textContent = window.formatDA(netHt);

    // Tax (TVA)
    const taxRate = taxRateSelect ? (parseFloat(taxRateSelect.value) || 0) : 0;
    const taxAmount = netHt * (taxRate / 100);
    if (taxAmountBadge) taxAmountBadge.textContent = `+ ${window.formatDA(taxAmount)} (${taxRate}%)`;

    // Grand Total
    const grandTotal = Math.round(netHt + taxAmount);
    if (grandTotalDisplay) grandTotalDisplay.textContent = window.formatDA(grandTotal);
    if (hiddenTotalPrice) hiddenTotalPrice.value = grandTotal;

    // Deposit check
    if (depositInput) {
        let currentDeposit = parseFloat(depositInput.value || 0);
        const balanceDue = Math.max(0, grandTotal - currentDeposit);
        if (balanceDueDisplay) balanceDueDisplay.textContent = window.formatDA(balanceDue);
        const depositPct = grandTotal > 0 ? Math.round((currentDeposit / grandTotal) * 100) : 0;
        if (depositPercentBadge) depositPercentBadge.textContent = `${depositPct}%`;
    }
};

window.renderItems = function() {
    const itemsTbody = document.getElementById('items_tbody');
    if (!itemsTbody) return;
    itemsTbody.innerHTML = '';
    items.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-800/40 transition group';
        const lineTotal = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
        tr.innerHTML = `
            <td class="py-2 px-3">
                <input type="text" name="items[${index}][description]" value="${window.escapeHtml(item.description)}"
                    placeholder="Description de la prestation..."
                    oninput="window.updateItemField(${index}, 'description', this.value)"
                    class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700/80 rounded-lg text-xs text-white placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </td>
            <td class="py-2 px-3">
                <input type="number" step="any" min="0" name="items[${index}][quantity]" value="${item.quantity}"
                    oninput="window.updateItemField(${index}, 'quantity', this.value)"
                    class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700/80 rounded-lg text-xs font-mono text-center text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </td>
            <td class="py-2 px-3">
                <input type="number" step="any" min="0" name="items[${index}][unit_price]" value="${item.unit_price}"
                    oninput="window.updateItemField(${index}, 'unit_price', this.value)"
                    class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700/80 rounded-lg text-xs font-mono text-right text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </td>
            <td class="py-2 px-3 text-right font-mono font-bold text-slate-200">
                <span id="item_total_${index}">${window.formatDA(lineTotal)}</span>
                <input type="hidden" id="item_hidden_total_${index}" name="items[${index}][total]" value="${lineTotal.toFixed(2)}">
            </td>
            <td class="py-2 px-2 text-center">
                <button type="button" onclick="window.removeItem(${index})" title="Supprimer la ligne"
                    class="text-slate-500 hover:text-rose-400 p-1 rounded transition">
                    ✕
                </button>
            </td>
        `;
        itemsTbody.appendChild(tr);
    });

    window.recalculateTotals();
};

window.setDepositPercent = function(pct) {
    const hiddenTotalPrice = document.getElementById('hidden_total_price');
    const depositInput = document.getElementById('deposit_required');
    if (!hiddenTotalPrice || !depositInput) return;
    const total = parseFloat(hiddenTotalPrice.value || 0);
    const deposit = Math.round((total * (pct / 100)) / 1000) * 1000;
    depositInput.value = deposit;
    depositInput.dataset.manual = 'true';
    window.recalculateTotals();
};

window.onDepositInput = function() {
    const hiddenTotalPrice = document.getElementById('hidden_total_price');
    const depositInput = document.getElementById('deposit_required');
    const balanceDueDisplay = document.getElementById('balance_due_display');
    const depositPercentBadge = document.getElementById('deposit_percent_badge');
    if (!depositInput || !hiddenTotalPrice) return;
    depositInput.dataset.manual = 'true';
    const total = parseFloat(hiddenTotalPrice.value || 0);
    const currentDeposit = parseFloat(depositInput.value || 0);
    const balanceDue = Math.max(0, total - currentDeposit);
    if (balanceDueDisplay) balanceDueDisplay.textContent = window.formatDA(balanceDue);
    const pct = total > 0 ? Math.round((currentDeposit / total) * 100) : 0;
    if (depositPercentBadge) depositPercentBadge.textContent = `${pct}%`;
};

window.resetToDefaults = function() {
    if (!confirm('Réinitialiser les lignes selon la salle et le forfait actuels ?')) return;
    items = [];
    const spaceSelect = document.getElementById('space_select');
    const packageSelect = document.getElementById('package_select');
    const guestsInput = document.getElementById('expected_guests');

    const spaceOpt = spaceSelect ? spaceSelect.options[spaceSelect.selectedIndex] : null;
    const spaceName = spaceOpt ? (spaceOpt.dataset.name || 'Salle') : 'Salle';
    const spaceBase = spaceOpt ? parseFloat(spaceOpt.dataset.base || 0) : 0;

    const pkgOpt = packageSelect ? packageSelect.options[packageSelect.selectedIndex] : null;
    const pkgName = pkgOpt ? (pkgOpt.dataset.name || '') : '';
    const pkgBase = pkgOpt ? parseFloat(pkgOpt.dataset.base || 0) : 0;
    const pkgGuest = pkgOpt ? parseFloat(pkgOpt.dataset.guest || 0) : 0;
    const guests = guestsInput ? (parseInt(guestsInput.value || 200) || 200) : 200;

    if (pkgName && pkgBase > 0) {
        items.push({
            description: `Forfait Événement : ${pkgName} (Base)`,
            quantity: 1,
            unit_price: pkgBase
        });
        if (pkgGuest > 0) {
            items.push({
                description: `Supplément invité ${pkgName} (${guests} invités)`,
                quantity: guests,
                unit_price: pkgGuest
            });
        }
    } else {
        items.push({
            description: `Mise à disposition et location exclusive : ${spaceName}`,
            quantity: 1,
            unit_price: spaceBase > 0 ? spaceBase : 220000
        });
    }

    window.renderItems();
};

// Bind DOM event listeners once loaded
document.addEventListener('DOMContentLoaded', function() {
    const spaceSelect = document.getElementById('space_select');
    const packageSelect = document.getElementById('package_select');
    const guestsInput = document.getElementById('expected_guests');

    if (spaceSelect) {
        spaceSelect.addEventListener('change', function() {
            if (items.length === 1 && items[0].description.includes('Mise à disposition')) {
                window.resetToDefaults();
            }
        });
    }

    if (packageSelect) {
        packageSelect.addEventListener('change', function() {
            window.resetToDefaults();
        });
    }

    if (guestsInput) {
        guestsInput.addEventListener('input', function() {
            const guests = parseInt(this.value || 0) || 0;
            items.forEach(it => {
                if (it.description.toLowerCase().includes('invités') || it.description.toLowerCase().includes('couverts')) {
                    it.quantity = guests;
                }
            });
            window.renderItems();
        });
    }

    window.renderItems();
});

// Also trigger immediately if document is ready
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    window.renderItems();
}
</script>
@endpush
