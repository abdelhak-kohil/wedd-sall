@extends('layouts.app')
@section('title', 'Devis & Estimations')
@section('page_title', app()->getLocale() === 'ar' ? 'سجل عروض الأسعار والتقديرات' : 'Gestion des Devis & Estimations Commerciales')

@section('content')
<div class="space-y-6">

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Devis Émis</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold font-mono text-white">{{ $stats['total'] }}</span>
                <span class="text-xs text-slate-500 font-mono">Dossiers</span>
            </div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[11px] font-bold text-blue-400 uppercase tracking-wider block">Devis en Négociation</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold font-mono text-blue-400">{{ $stats['sent'] }}</span>
                <span class="text-xs text-blue-400/70 font-mono">Envoyés</span>
            </div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block">Devis Convertis / Acceptés</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-2xl font-bold font-mono text-emerald-400">{{ $stats['accepted'] }}</span>
                <span class="text-xs text-emerald-400/70 font-mono">Conclus ✓</span>
            </div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Pipeline Potentiel</span>
            <div class="flex items-baseline justify-between mt-2">
                <span class="text-xl font-bold font-mono text-amber-400">{{ number_format($stats['potential_revenue'], 0, ',', ' ') }}</span>
                <span class="text-xs text-amber-400/70 font-mono">DA</span>
            </div>
        </div>
    </div>

    <!-- Top Toolbar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-xl">
        <form method="GET" action="{{ route('quotations.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم العرض أو الزبون...' : 'Recherche N° devis, client, tél...' }}"
                class="px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 flex-1 min-w-[200px]">

            <select name="status" onchange="this.form.submit()"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tous les statuts</option>
                @foreach(\App\Models\Quotation::STATUSES as $key => $st)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                        {{ $st['label_fr'] }} ({{ $st['label_ar'] }})
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3.5 py-2 bg-slate-700 hover:bg-slate-600 rounded-xl text-white text-xs font-semibold transition">
                Filtrer
            </button>
            @if(request('q') || request('status'))
                <a href="{{ route('quotations.index') }}" class="px-3 py-2 text-xs text-slate-400 hover:text-white transition">
                    Réinitialiser
                </a>
            @endif
        </form>

        <a href="{{ route('quotations.create') }}"
            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-lg transition flex items-center gap-1.5 shrink-0">
            <span>+ {{ app()->getLocale() === 'ar' ? 'إنشاء عرض سعر جديد' : 'Nouveau Devis Professionnel' }}</span>
        </a>
    </div>

    <!-- Quotations Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-xs">
            <thead class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-900 border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4 text-left">N° Devis</th>
                    <th class="py-3 px-4 text-left">Client & Contact</th>
                    <th class="py-3 px-4 text-left">Date Événement</th>
                    <th class="py-3 px-4 text-left hidden md:table-cell">Salle & Prestations</th>
                    <th class="py-3 px-4 text-left hidden lg:table-cell">Validité</th>
                    <th class="py-3 px-4 text-left">Statut</th>
                    <th class="py-3 px-4 text-right">Montant Total</th>
                    <th class="py-3 px-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($quotations as $quote)
                    @php $badge = $quote->statusBadge(); @endphp
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            <a href="{{ route('quotations.show', $quote) }}" class="hover:text-emerald-400 transition">
                                {{ $quote->quote_number }}
                            </a>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-semibold text-white block">{{ $quote->customer_name }}</span>
                            <span class="text-[11px] font-mono text-slate-400">{{ $quote->customer_phone }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-300">
                            {{ $quote->event_date->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4 hidden md:table-cell text-slate-300">
                            <div class="font-medium text-white">{{ $quote->space->name }}</div>
                            @if($quote->package)
                                <div class="text-[11px] text-amber-300/90">📦 {{ $quote->package->name_fr }}</div>
                            @elseif($quote->items_list && count($quote->items_list) > 1)
                                <div class="text-[11px] text-slate-400">{{ count($quote->items_list) }} prestations chiffrées</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 hidden lg:table-cell text-slate-400 text-[11px]">
                            jusqu'au {{ $quote->valid_until->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $badge['class'] }}">
                                {{ $badge['label_fr'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-400">
                            {{ number_format($quote->total_price, 0, ',', ' ') }} DA
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('quotations.edit', $quote) }}"
                                    class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 hover:text-white transition" title="Modifier le devis">
                                    ✏️
                                </a>
                                <a href="{{ route('quotations.print', $quote) }}" target="_blank"
                                    class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Imprimer le devis officiel">
                                    🖨️
                                </a>
                                @if((auth()->user()?->isOwner() || auth()->user()?->isManager()) && $quote->status !== 'ACCEPTED')
                                    <form method="POST" action="{{ route('quotations.destroy', $quote) }}" class="inline"
                                        onsubmit="return confirm('Confirmer la suppression définitive du devis {{ $quote->quote_number }} ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-950/70 border border-transparent hover:border-rose-800/80 text-slate-400 hover:text-rose-300 transition" title="Supprimer le devis (Gérant / Propriétaire)">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('quotations.show', $quote) }}"
                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition">
                                    Détails →
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            Aucun devis enregistré pour le moment.
                            <a href="{{ route('quotations.create') }}" class="text-emerald-400 hover:underline ml-1 font-semibold">
                                Établir un devis professionnel
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($quotations->hasPages())
            <div class="px-4 py-3 border-t border-slate-800">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
