@extends('layouts.app')
@section('title', 'Paiements & Reçus')
@section('page_title', app()->getLocale() === 'ar' ? 'سجل المدفوعات والوصولات' : 'Journal des Encaissements & Reçus')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <span class="text-xs text-slate-400 block mb-1">Total Encaissé (Tous modes)</span>
            <div class="text-2xl font-bold font-mono text-emerald-400">
                {{ number_format($totalCollected, 0, ',', ' ') }} DA
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Règlements clients cumulés</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <span class="text-xs text-slate-400 block mb-1">Encaissements Espèces (Caisse)</span>
            <div class="text-2xl font-bold font-mono text-amber-400">
                {{ number_format($cashCollected, 0, ',', ' ') }} DA
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Fonds perçus en liquide</span>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 block mb-1">Total des Reçus Émis</span>
                <div class="text-2xl font-bold font-mono text-white">
                    {{ $payments->total() }}
                </div>
                <span class="text-[11px] text-slate-500 mt-1 block">Reçus officiels horodatés</span>
            </div>
            <a href="{{ route('cash-registers.index') }}"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-white transition border border-slate-700">
                Voir Caisse →
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-xl">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الوصل أو الزبون...' : 'Rechercher N° reçu, client, réservation...' }}"
                class="px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 flex-1 min-w-[200px]">

            <select name="method" onchange="this.form.submit()"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tous les modes</option>
                @foreach(\App\Models\Payment::METHODS as $key => $m)
                    <option value="{{ $key }}" {{ request('method') === $key ? 'selected' : '' }}>
                        {{ $m['label_fr'] }}
                    </option>
                @endforeach
            </select>

            <select name="type" onchange="this.form.submit()"
                class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Tous les types</option>
                @foreach(\App\Models\Payment::TYPES as $key => $t)
                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                        {{ $t['label_fr'] }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 rounded-xl text-white text-xs font-medium transition">
                Filtrer
            </button>
        </form>

        <a href="{{ route('reservations.index') }}"
            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow transition shrink-0">
            + Nouveau Versement (depuis réservation)
        </a>
    </div>

    <!-- Payments Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-xs">
            <thead class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-900 border-b border-slate-800">
                <tr>
                    <th class="py-3 px-4 text-left">N° Reçu</th>
                    <th class="py-3 px-4 text-left">Date</th>
                    <th class="py-3 px-4 text-left">Réservation & Client</th>
                    <th class="py-3 px-4 text-left">Type de versement</th>
                    <th class="py-3 px-4 text-left">Mode</th>
                    <th class="py-3 px-4 text-right">Montant</th>
                    <th class="py-3 px-4 text-left hidden lg:table-cell">Encaissé par</th>
                    <th class="py-3 px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($payments as $pay)
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            {{ $pay->receipt_number }}
                        </td>
                        <td class="py-3 px-4 text-slate-300">
                            {{ $pay->payment_date->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('reservations.show', $pay->reservation) }}" class="font-semibold text-white hover:text-emerald-400">
                                {{ $pay->reservation->customer_name }}
                            </a>
                            <span class="block text-[11px] font-mono text-slate-400">
                                {{ $pay->reservation->reservation_number }} ({{ $pay->reservation->space->name }})
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $pay->typeLabel() }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-300 font-medium">
                            {{ $pay->methodLabel() }}
                            @if($pay->cheque_number)
                                <span class="block text-[10px] font-mono text-slate-400">N° {{ $pay->cheque_number }} ({{ $pay->bank_name }})</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-sm {{ $pay->payment_type === 'REFUND' ? 'text-rose-400' : 'text-emerald-400' }}">
                            {{ $pay->payment_type === 'REFUND' ? '-' : '+' }} {{ number_format($pay->amount, 0, ',', ' ') }} DA
                        </td>
                        <td class="py-3 px-4 hidden lg:table-cell text-slate-400">
                            {{ $pay->receiver->name }}
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('payments.receipt', $pay) }}" target="_blank"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition inline-flex items-center gap-1 shadow">
                                <span>🖨️ Reçu</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            Aucun versement enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-slate-800">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
