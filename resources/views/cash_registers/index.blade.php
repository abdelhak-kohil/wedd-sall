@extends('layouts.app')
@section('title', 'Caisse')
@section('page_title', app()->getLocale() === 'ar' ? 'إدارة الصندوق' : 'Gestion de la Caisse')

@section('content')
<div class="space-y-6">

    {{-- ── Active Register Banner ── --}}
    @if($activeRegister)
    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
            <div>
                <p class="text-emerald-400 font-semibold text-sm">Caisse ouverte — {{ $activeRegister->register_date->format('d/m/Y') }}</p>
                <p class="text-slate-300 text-xs mt-0.5">
                    Fond initial : <strong>{{ number_format($activeRegister->opening_balance, 2, ',', ' ') }} DA</strong>
                    &nbsp;·&nbsp; Entrées : <strong class="text-emerald-400">+{{ number_format($activeRegister->total_cash_in, 2, ',', ' ') }} DA</strong>
                    &nbsp;·&nbsp; Solde attendu : <strong>{{ number_format($activeRegister->expected_closing_balance, 2, ',', ' ') }} DA</strong>
                </p>
            </div>
        </div>
        <button onclick="document.getElementById('modal-close').classList.remove('hidden')"
                class="shrink-0 px-4 py-2 bg-rose-600/80 hover:bg-rose-600 text-white text-sm font-medium rounded-xl transition">
            Clôturer la caisse
        </button>
    </div>
    @else
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-slate-400 text-sm">Aucune caisse ouverte aujourd'hui.</p>
            <p class="text-slate-500 text-xs mt-0.5">Ouvrez la caisse pour enregistrer des paiements espèces.</p>
        </div>
        <button onclick="document.getElementById('modal-open').classList.remove('hidden')"
                class="shrink-0 px-4 py-2 bg-emerald-600/80 hover:bg-emerald-600 text-white text-sm font-medium rounded-xl transition">
            Ouvrir la caisse
        </button>
    </div>
    @endif

    {{-- ── Today's Cash Payments (if open) ── --}}
    @if($activeRegister && $todayCashPayments->count())
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-800/60">
            <h2 class="text-sm font-semibold text-slate-300">Mouvements de la journée</h2>
        </div>
        <div class="divide-y divide-slate-800/40">
            @foreach($todayCashPayments as $p)
            <div class="px-5 py-3 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-slate-200">{{ $p->receipt_number }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $p->reservation->customer_name ?? '—' }}
                        &nbsp;·&nbsp; {{ $p->reservation->reservation_number ?? '' }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-sm font-semibold {{ $p->payment_type === 'REFUND' ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $p->payment_type === 'REFUND' ? '-' : '+' }}{{ number_format($p->amount, 2, ',', ' ') }} DA
                    </span>
                    <p class="text-xs text-slate-500">{{ ucfirst(strtolower($p->payment_type)) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── History ── --}}
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-800/60">
            <h2 class="text-sm font-semibold text-slate-300">Historique des caisses</h2>
        </div>
        @if($registers->isEmpty())
        <p class="px-5 py-8 text-center text-slate-500 text-sm">Aucune caisse enregistrée.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/50">
                    <tr class="text-xs text-slate-400 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-right">Fond initial</th>
                        <th class="px-4 py-3 text-right">Entrées</th>
                        <th class="px-4 py-3 text-right">Solde attendu</th>
                        <th class="px-4 py-3 text-right">Solde réel</th>
                        <th class="px-4 py-3 text-right">Écart</th>
                        <th class="px-4 py-3 text-center">Statut</th>
                        <th class="px-4 py-3 text-left">Ouvert par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @foreach($registers as $reg)
                    @php
                        $ecart = $reg->actual_closing_balance !== null
                            ? (float)$reg->actual_closing_balance - (float)$reg->expected_closing_balance
                            : null;
                    @endphp
                    <tr class="hover:bg-slate-800/20 transition">
                        <td class="px-4 py-3 font-medium text-slate-200">{{ $reg->register_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right text-slate-300">{{ number_format($reg->opening_balance, 2, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right text-emerald-400">+{{ number_format($reg->total_cash_in, 2, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right text-slate-300">{{ number_format($reg->expected_closing_balance, 2, ',', ' ') }}</td>
                        <td class="px-4 py-3 text-right text-slate-300">
                            {{ $reg->actual_closing_balance !== null ? number_format($reg->actual_closing_balance, 2, ',', ' ') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium">
                            @if($ecart !== null)
                                <span class="{{ $ecart > 0 ? 'text-emerald-400' : ($ecart < 0 ? 'text-rose-400' : 'text-slate-400') }}">
                                    {{ $ecart >= 0 ? '+' : '' }}{{ number_format($ecart, 2, ',', ' ') }}
                                </span>
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $reg->status === 'OPEN' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-slate-700/50 text-slate-400' }}">
                                {{ $reg->status === 'OPEN' ? 'Ouverte' : 'Clôturée' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $reg->opener?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($registers->hasPages())
        <div class="px-5 py-3 border-t border-slate-800/60">{{ $registers->links() }}</div>
        @endif
        @endif
    </div>
</div>

{{-- ── Modal: Open Register ── --}}
<div id="modal-open" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-semibold text-white mb-4">Ouvrir la caisse</h3>
        <form method="POST" action="{{ route('cash-registers.open') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Fond initial (DA)</label>
                    <input type="number" name="opening_balance" min="0" step="0.01" value="0"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Remarques (optionnel)</label>
                    <textarea name="notes" rows="2"
                              class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-open').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-700 text-slate-300 text-sm rounded-xl hover:bg-slate-800 transition">Annuler</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-500 transition">Ouvrir</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Close Register ── --}}
@if($activeRegister)
<div id="modal-close" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-semibold text-white mb-1">Clôturer la caisse</h3>
        <p class="text-slate-400 text-xs mb-4">Solde théorique : <strong class="text-white">{{ number_format($activeRegister->expected_closing_balance, 2, ',', ' ') }} DA</strong></p>
        <form method="POST" action="{{ route('cash-registers.close', $activeRegister) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Solde réel compté (DA)</label>
                    <input type="number" name="actual_closing_balance" min="0" step="0.01"
                           value="{{ number_format($activeRegister->expected_closing_balance, 2, '.', '') }}"
                           class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">Remarques de clôture</label>
                    <textarea name="notes" rows="2"
                              class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="button" onclick="document.getElementById('modal-close').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-slate-700 text-slate-300 text-sm rounded-xl hover:bg-slate-800 transition">Annuler</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-500 transition">Clôturer</button>
            </div>
        </form>
    </div>
</div>
@endif

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
     class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white text-sm px-5 py-3 rounded-xl shadow-lg">
    {{ session('success') }}
</div>
@endif
@endsection
