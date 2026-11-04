@extends('layouts.app')
@section('title', 'Devis ' . $quotation->quote_number)
@section('page_title', 'Détails du Devis Commercial')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-slate-700/80 rounded-2xl p-5 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-2xl shadow-inner">
                🧾
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold font-mono text-white tracking-wide">{{ $quotation->quote_number }}</h1>
                    @php $badge = $quotation->statusBadge(); @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badge['class'] }}">
                        {{ $badge['label_fr'] }}
                    </span>
                    <span class="text-xs text-slate-400">
                        Émis le <span class="font-medium text-slate-200">{{ $quotation->created_at->format('d/m/Y') }}</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Événement prévu le <span class="font-bold text-white">{{ $quotation->event_date->translatedFormat('l d F Y') }}</span>
                    pour <span class="font-bold text-white">{{ $quotation->customer_name }}</span>
                    • Valable jusqu'au <span class="font-bold text-amber-300">{{ $quotation->valid_until->format('d/m/Y') }}</span>
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $waText = "Bonjour {$quotation->customer_name},\nVoici votre devis officiel {$quotation->quote_number} pour votre célébration le {$quotation->event_date->format('d/m/Y')}.\nSalle: {$quotation->space->name}\nMontant total: " . number_format($quotation->total_price, 0, ',', ' ') . " DA\nAcompte exigé ('Erboun'): " . number_format($quotation->deposit_required, 0, ',', ' ') . " DA.\nOffre valable jusqu'au {$quotation->valid_until->format('d/m/Y')}.\nMerci de votre confiance !";
                $waUrl = "https://wa.me/213" . ltrim(preg_replace('/\D/', '', $quotation->customer_phone), '0') . "?text=" . urlencode($waText);
            @endphp

            <a href="{{ $waUrl }}" target="_blank"
                class="px-3.5 py-2 rounded-xl bg-emerald-600/30 hover:bg-emerald-600 border border-emerald-500/50 text-emerald-300 hover:text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                💬 Partager WhatsApp
            </a>

            <a href="{{ route('quotations.print', $quotation) }}" target="_blank"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                🖨️ Imprimer / PDF
            </a>

            @if($quotation->status !== 'ACCEPTED')
                <a href="{{ route('quotations.edit', $quotation) }}"
                    class="px-3.5 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 text-amber-300 text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                    ✏️ Modifier
                </a>

                @if(auth()->user()?->isOwner() || auth()->user()?->isManager())
                    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                        onsubmit="return confirm('Attention : Êtes-vous sûr de vouloir supprimer définitivement le devis {{ $quotation->quote_number }} ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-2 rounded-xl bg-rose-950/40 hover:bg-rose-900/60 border border-rose-800/60 text-rose-300 hover:text-white text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                            🗑️ Supprimer
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('quotations.convert', $quotation) }}" onsubmit="return confirm('Confirmer la conversion de ce devis en réservation ferme ?')">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-xs font-bold shadow-lg transition flex items-center gap-1.5">
                        ⚡ Convertir en Réservation
                    </button>
                </form>
            @else
                <a href="{{ route('reservations.show', $quotation->converted_reservation_id) }}"
                    class="px-4 py-2 rounded-xl bg-indigo-950/60 border border-indigo-700/60 text-indigo-300 text-xs font-semibold flex items-center gap-1">
                    ✓ Réservation N° {{ $quotation->reservation?->reservation_number }}
                </a>
            @endif
        </div>
    </div>

    <!-- 3 Pillars Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Pillar 1: Client & Mariés -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-3">
            <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider border-b border-slate-800 pb-3 flex items-center gap-2">
                <span>👤 Prospect / Client</span>
            </h2>

            <div class="space-y-2.5 text-xs">
                <div>
                    <span class="text-slate-500 block text-[11px]">Nom & Prénom</span>
                    <span class="font-bold text-white text-sm">{{ $quotation->customer_name }}</span>
                </div>

                <div>
                    <span class="text-slate-500 block text-[11px]">Téléphone</span>
                    <span class="font-mono text-emerald-400 text-sm font-semibold">{{ $quotation->customer_phone }}</span>
                </div>

                @if($quotation->groom_name || $quotation->bride_name)
                    <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                        <span class="text-slate-400 block text-[11px] mb-1">💍 Noms des mariés</span>
                        <span class="font-bold text-amber-300">
                            {{ $quotation->groom_name ?? '—' }} & {{ $quotation->bride_name ?? '—' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pillar 2: Salle & Événement -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-3">
            <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider border-b border-slate-800 pb-3 flex items-center gap-2">
                <span>🏛️ Espace & Planning</span>
            </h2>

            <div class="space-y-2.5 text-xs">
                <div>
                    <span class="text-slate-500 block text-[11px]">Espace sélectionné</span>
                    <span class="font-bold text-white text-sm">{{ $quotation->space->name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-500 block text-[11px]">Créneau horaire</span>
                        <span class="font-mono text-slate-200">{{ substr($quotation->start_time, 0, 5) }} - {{ substr($quotation->end_time, 0, 5) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Invités prévus</span>
                        <span class="text-slate-200 font-semibold">{{ $quotation->expected_guests }} personnes</span>
                    </div>
                </div>

                @if($quotation->package)
                    <div class="p-2.5 bg-slate-800/60 rounded-xl border border-slate-700/50">
                        <span class="text-amber-400 block text-xs font-bold">📦 {{ $quotation->package->name_fr }}</span>
                        @if(!empty($quotation->package->included_items))
                            <div class="mt-1 text-[11px] text-slate-300">
                                {{ implode(', ', $quotation->package->included_items) }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Pillar 3: Proposition Financière -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-3">
            <h2 class="text-xs font-bold text-slate-300 uppercase tracking-wider border-b border-slate-800 pb-3 flex items-center gap-2">
                <span>💰 Synthèse Financière</span>
            </h2>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Total Devis (TTC) :</span>
                    <span class="font-mono font-bold text-emerald-400 text-base">
                        {{ number_format($quotation->total_price, 0, ',', ' ') }} DA
                    </span>
                </div>

                <div class="flex items-center justify-between border-t border-slate-800 pt-2">
                    <span class="text-slate-400">Acompte convenu ('Erboun') :</span>
                    <span class="font-mono font-bold text-white">
                        {{ number_format($quotation->deposit_required, 0, ',', ' ') }} DA
                    </span>
                </div>

                <div class="flex items-center justify-between border-t border-slate-800 pt-2">
                    <span class="text-slate-400">Solde restant au Jour J :</span>
                    <span class="font-mono text-slate-300">
                        {{ number_format($quotation->balance_due, 0, ',', ' ') }} DA
                    </span>
                </div>

                <div class="p-2.5 rounded-xl bg-slate-800/40 border border-slate-700/50 text-[11px] text-slate-400">
                    Offre valable jusqu'au <strong class="text-slate-200">{{ $quotation->valid_until->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Line Items Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl space-y-4 p-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-base">📋</span>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Ventilation Détaillée des Prestations Chiffrées</h2>
            </div>
            <span class="text-xs font-mono text-slate-400 bg-slate-800 px-3 py-1 rounded-full">
                Devis Officiel N° {{ $quotation->quote_number }}
            </span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-800/80 text-[11px] text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4" style="width: 50%;">Désignation de la Prestation / Service</th>
                        <th class="py-3 px-4 text-center" style="width: 15%;">Quantité</th>
                        <th class="py-3 px-4 text-right" style="width: 17%;">Prix Unitaire</th>
                        <th class="py-3 px-4 text-right" style="width: 18%;">Montant Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @php $items = $quotation->items_list; @endphp
                    @if(count($items) > 0)
                        @foreach($items as $item)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 px-4 font-medium text-white">
                                    {{ $item['description'] ?? 'Prestation' }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-slate-300">
                                    {{ $item['quantity'] ?? 1 }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-300">
                                    {{ number_format($item['unit_price'] ?? 0, 0, ',', ' ') }} DA
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-100">
                                    {{ number_format(($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0), 0, ',', ' ') }} DA
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="py-3 px-4 font-medium text-white">
                                {{ $quotation->package ? $quotation->package->name_fr : ('Mise à disposition ' . $quotation->space->name) }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-300">1</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">
                                {{ number_format($quotation->total_price, 0, ',', ' ') }} DA
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-100">
                                {{ number_format($quotation->total_price, 0, ',', ' ') }} DA
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Financial Totals Calculation Box -->
        <div class="flex justify-end pt-2">
            <div class="w-full md:w-96 bg-slate-950/60 border border-slate-800 rounded-xl p-4 space-y-2 text-xs">
                
                <!-- Sous-total brut -->
                <div class="flex items-center justify-between text-slate-300">
                    <span>Sous-total brut HT :</span>
                    <span class="font-mono font-semibold text-white">
                        {{ number_format($quotation->subtotal > 0 ? $quotation->subtotal : $quotation->total_price, 0, ',', ' ') }} DA
                    </span>
                </div>

                <!-- Remise -->
                @if($quotation->discount_amount > 0)
                    <div class="flex items-center justify-between text-amber-400">
                        <span>
                            Remise commerciale
                            @if($quotation->discount_type === 'percent') ({{ $quotation->discount_value }}%) @endif :
                        </span>
                        <span class="font-mono font-bold">
                            - {{ number_format($quotation->discount_amount, 0, ',', ' ') }} DA
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-slate-300 border-t border-slate-800/80 pt-1">
                        <span>Net Commercial HT :</span>
                        <span class="font-mono font-semibold">
                            {{ number_format($quotation->net_before_tax, 0, ',', ' ') }} DA
                        </span>
                    </div>
                @endif

                <!-- TVA -->
                @if($quotation->tax_amount > 0 || $quotation->tax_rate > 0)
                    <div class="flex items-center justify-between text-blue-400">
                        <span>TVA ({{ $quotation->tax_rate }}%) :</span>
                        <span class="font-mono font-semibold">
                            + {{ number_format($quotation->tax_amount, 0, ',', ' ') }} DA
                        </span>
                    </div>
                @endif

                <!-- Grand Total -->
                <div class="flex items-center justify-between border-t-2 border-emerald-500/50 pt-2 pb-1 text-sm font-bold">
                    <span class="text-emerald-400 uppercase tracking-wide">Total Général (TTC) :</span>
                    <span class="font-mono text-emerald-400 text-base">
                        {{ number_format($quotation->total_price, 0, ',', ' ') }} DA
                    </span>
                </div>

                <!-- Acompte et solde -->
                <div class="border-t border-slate-800/80 pt-2 space-y-1.5 text-slate-400">
                    <div class="flex items-center justify-between">
                        <span>Acompte exigé ('Erboun') :</span>
                        <span class="font-mono font-bold text-white">
                            {{ number_format($quotation->deposit_required, 0, ',', ' ') }} DA
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Solde exigible au Jour J :</span>
                        <span class="font-mono text-slate-200">
                            {{ number_format($quotation->balance_due, 0, ',', ' ') }} DA
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Notes and terms -->
        @if($quotation->notes || $quotation->payment_terms)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-800 pt-4 text-xs">
                @if($quotation->notes)
                    <div class="p-3 bg-slate-800/40 rounded-xl border border-slate-800">
                        <span class="font-bold text-slate-400 block mb-1 text-[11px] uppercase tracking-wider">📌 Notes particulières</span>
                        <p class="text-slate-300 whitespace-pre-line">{{ $quotation->notes }}</p>
                    </div>
                @endif
                @if($quotation->payment_terms)
                    <div class="p-3 bg-slate-800/40 rounded-xl border border-slate-800">
                        <span class="font-bold text-slate-400 block mb-1 text-[11px] uppercase tracking-wider">💳 Modalités de règlement</span>
                        <p class="text-slate-300 whitespace-pre-line">{{ $quotation->payment_terms }}</p>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
@endsection
