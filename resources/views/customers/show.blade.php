@extends('layouts.app')
@section('title', 'Client: ' . $customer->full_name)
@section('page_title', 'Fiche Client')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-950/60 border border-emerald-700/60 flex items-center justify-center font-bold text-2xl text-emerald-400">
                {{ strtoupper(mb_substr($customer->full_name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-white">{{ $customer->full_name }}</h1>
                    <span class="text-xs font-mono bg-slate-800 px-2 py-0.5 rounded text-slate-400">{{ $customer->customer_code }}</span>
                </div>
                <div class="text-xs text-slate-400 mt-1 flex flex-wrap gap-3">
                    <span>📞 {{ $customer->phone }}</span>
                    @if($customer->second_phone)<span>📞 {{ $customer->second_phone }}</span>@endif
                    <span>📍 {{ $customer->commune?->name_fr ?? '' }} {{ $customer->wilaya?->name_fr ? '(' . $customer->wilaya->code . ' - ' . $customer->wilaya->name_fr . ')' : '' }}</span>
                    <span>💍 {{ $customer->display_name }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ $customer->whatsappLink('Bonjour ' . $customer->full_name) }}" target="_blank"
                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                💬 WhatsApp
            </a>
            <a href="{{ route('reservations.create', ['phone' => $customer->phone]) }}"
                class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                + Nouvelle Réservation
            </a>
        </div>
    </div>

    <!-- Reservations History -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <span>📅 Historique des Réservations</span>
            <span class="text-xs bg-slate-800 px-2 py-0.5 rounded-full text-slate-400 font-normal">({{ $customer->reservations->count() }})</span>
        </h2>

        @if($customer->reservations->isEmpty())
            <p class="text-xs text-slate-500 py-4 text-center">Aucune réservation pour ce client pour l'instant.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="text-[11px] text-slate-400 uppercase bg-slate-900 border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3 text-left">N°</th>
                            <th class="py-2.5 px-3 text-left">Date</th>
                            <th class="py-2.5 px-3 text-left">Espace</th>
                            <th class="py-2.5 px-3 text-left">Type</th>
                            <th class="py-2.5 px-3 text-left">Statut</th>
                            <th class="py-2.5 px-3 text-right">Total</th>
                            <th class="py-2.5 px-3 text-right">Reste</th>
                            <th class="py-2.5 px-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($customer->reservations as $res)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 px-3 font-mono font-bold text-white">{{ $res->reservation_number }}</td>
                                <td class="py-3 px-3 text-slate-300">{{ \Carbon\Carbon::parse($res->event_date)->format('d/m/Y') }}</td>
                                <td class="py-3 px-3 text-slate-300">{{ $res->space?->name ?? '—' }}</td>
                                <td class="py-3 px-3 text-slate-400">{{ $res->event_type }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $res->statusBadge()['class'] }}">
                                        {{ $res->statusBadge()['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-white">{{ number_format($res->total_price, 0, ',', ' ') }} DA</td>
                                <td class="py-3 px-3 text-right font-mono {{ $res->remaining_balance > 0 ? 'text-amber-400 font-bold' : 'text-emerald-400' }}">
                                    {{ number_format($res->remaining_balance, 0, ',', ' ') }} DA
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('reservations.show', $res) }}" class="text-indigo-400 hover:underline">Voir →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
